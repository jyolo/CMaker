<?php
/**
 *  可自定义表单生成的方案的 生成器
 *  切换生成方案只需要在配置文件中定义 form_dom_plan => '方案名称' 即可
 *
 */

namespace CMaker;

use think\Db;
use think\Exception;
interface Transport{

    /*
     * 设置默认的组件属性信息
     * @return array
     * */
    public static function attr();
    /*
     * 设置组件的html dom
     * @return array
     * */
    public static function dom();

    /**
     * 设置组件依赖的js插件
     * @param $static_path
     * @return mixed
     */
    public static function relyOnJsPlugin($static_path);

    /**
     * 设置组件调用的js
     * @param $attr
     * @return mixed
     */
    public static function script($attr);
}

/**
 * 组件的父类 组件的公共类
 * Class component
 * @package FormMaker
 */
abstract class Component implements Transport
{
    //组件的attr属性
    public static $attr = [];
    //组件的唯一id
    public static $id = null;

    /**
     * 全局的组件所需依赖的 js|css 文件
     * @param $static_path
     * @return array|mixed
     */
    public static function relyOnJsPlugin($static_path){
        return [
//            'css' => [
//                [
//                    'path' => "{$static_path}plugin/layui/css/layui.css",
//                    'attr' => ['media' => 'all']
//                ]
//            ],
//            'js' => [
//                ['path' => "{$static_path}plugin/layui/layui.js"],
//                ['path' => "{$static_path}plugin/lay-extend-module/config.js"],
//            ]
        ];
    }

    /**
     * 全局的组件 javascript 脚本 默认值
     * @param $attr
     * @return mixed|void
     */
    public static function script($attr){return ;}

    /**
     * 非递归得到 层级树形结构的 多维数组
     * @param array $config [ treeData => array ,field => 'id ,pid ,name']
     * @param bool 是否保留数组 默认false不保留
     * @return bool 是否直接返回树形层级的数组 默认false
     * @throws Exception
     */
    public static function get_tree_array($config = [] ,$keep_array = false ,$return_tree_array = false){
        $field = explode(',',$config['field']);


        if(!isset($field[1]))throw new Exception('缺少父级关系字段 比如:parentid');

        //如果有 数据直接传进来 则直接使用数据
        if(isset($config['treeData']) ){
            if(!count($config['treeData'])) return [];
            $arr = $config['treeData'];
        }
        else
        {
            $sql = 'show tables like \''.config('database.prefix').$config['table'].'\' ';
            $flag = Db::query($sql);
            //表不存在返回空数组
            if(!count($flag))return [];

            //表存在的情况下
            //db::name($config['table'])->value();
            if(!isset($config['field']) || strlen($config['field']) == 0) throw new \Exception('field 不能为空');

            $field = explode(',' ,$config['field']);

            $sql = 'select COUNT(*) as num from information_schema.columns WHERE table_name = "'.config('database.prefix').$config['table'].'" and column_name= "'.$field[1].'" ';
            $flag = Db::query($sql);

            //查看 关系字段是否存在，不存在 直接返回空 数组
            //解决 当生成方案 是normal 转 category 的时候，需要对自己的表新增层级关系的时候
            if($flag[0]['num'] == 0)return [];

            $arr = Db::name($config['table'])
                ->field($config['field'].',path,listorder')
                ->where(isset($config['where']) ? $config['where'] : '')
                ->select();
            if(!count($arr)) return [];

        }

        //创建初始化数组
        foreach($arr as $k => $v){
            $tree[ $v[ $field[0] ] ] = $v;
            $tree[ $v[ $field[0] ] ]['son'] = [];
        }

        //引用
        foreach($tree as $sk => $sv){
            //自动生成层级关系 //查找的数据 可能上级的元素不存在
            if($sv[ $field[1] ] != 0 && isset($tree[ $sv[$field[1]] ])){
                //传值引用
                $tree[ $sv[ $field[1] ] ]['son'][] = &$tree[$sk];
            }
        }


        $true_tree = [];
        //剔除多余元素
        foreach($tree as $k => $v){
            //存储上级不存在的数组
            if(!isset($tree[ $v[$field[1]] ])){
                $true_tree[$k] = $v;
            }
        }
        sort($true_tree);

        //对tree 进行排序，默认排序字段是 listorder
        $true_tree = self::tree_array_sort($true_tree);

        //如果要返回树形数组 则排序后直接返回
        if($return_tree_array == true)  return $true_tree;

        //树形数组 转换成 一维数组 用于 select, table
        $arr = self::tree_to_array($true_tree ,$config['field'] ,$keep_array);

        if($keep_array){ //name值保留数组的层级关系，下标从新从0开始

            $i = 0;$table_arr = [];
            foreach($arr as $k => $v){
                unset($v['son']); //删除无用的son元素
                $table_arr[$i] = $v;
                $i++;
            }

            return $table_arr;
        }else{
            return $arr;
        }


    }


    /**
     * 递归 层级树形结构的 多维数组 转化成  带层级关系的一维数组
     * @param array $tree 树形结构的数据
     * @param string $component_setting_field 字段字符串 id,pid
     * @param boolean $keep_array 是否保留数组
     * @return array
     */
    protected static function tree_to_array($tree ,$component_setting_field = false ,$keep_array = false){
        if(!$component_setting_field) throw new Exception('缺少 组件设置的 field的参数');
        $arr = [];
        $field = explode(',',$component_setting_field);
        //第三个字段 为显示的字段
        $showfield = (isset($field[2]) && strlen($field[2])) ? $field[2]: $field[1];
        $pid = $field[1];


        foreach($tree as $k => $v){

            //保留数组
            if($keep_array){
                $v[$showfield] = self::buildSpace($v['path']) . $v[$showfield];

                $arr[$v[$field['0']]] = $v;

            }else{
                $arr[$v[$field['0']]] = self::buildSpace($v['path']) . $v[$showfield];

            }

            if(count($v['son'])){
                $gui = self::tree_to_array($v['son'] ,$component_setting_field ,$keep_array);
                $arr = $arr + $gui;
            }
        }


        return $arr;
    }
    /**
     * 层级树形结构的数组 进行递归冒泡排序 (降序)
     * @param array $tree 树形结构的数据
     * @return array
     */
    protected static function tree_array_sort($tree , $orderfield = 'listorder' ){
        $temp_arr = $arg = [];

        for($i = 0; $i<count($tree) ;$i++){
            for($j = 0 ;$j < (count($tree)-1); $j++){
                $now = $tree[$j];
                $prev = $tree[$j+1];

                if($now[$orderfield] < $prev[$orderfield])
                {
                    $temp = $now;
                    $tree[$j] = $prev;
                    $tree[$j+1] = $now;
                }

            }

            if(count($tree[$i]['son']) > 0){

                $tree[$i]['son'] = self::tree_array_sort($tree[$i]['son']);

            }
        }



        return $tree;

    }

    /*
    * 生成空格层级字符串
    */
    protected static function buildSpace($str){

        $spacer = '';
        $str = trim($str,',');
        $path = explode(',',$str);

        if(count($path) == 1){
            $spacer .= '';
        }else{
            $spacer .= '&nbsp;&nbsp;';
            for($i = 0 ;$i < count($path)-1 ;$i++ ){
                $spacer .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            $spacer .= '|- ';
        }

        return $spacer;
    }

}

