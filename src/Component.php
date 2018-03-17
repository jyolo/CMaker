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
        }else{
            $sql = 'show tables like \''.config('database.prefix').$config['table'].'\' ';
            $flag = Db::query($sql);
            //表不存在返回空数组
            if(!count($flag))return [];


            //表存在的情况下
            //b::name($config['table'])->value();
            if(!isset($config['field']) || strlen($config['field']) == 0) throw new \Exception('field 不能为空');
            $field = explode(',' ,$config['field']);
            $sql = 'select COUNT(*) as num from information_schema.columns WHERE table_name = "'.config('database.prefix').$config['table'].'" and column_name= "'.$field[1].'" ';
            $flag = Db::query($sql);
            //查看 关系字段是否存在，不存在 直接返回空 数组
            //解决 当生成方案 是normal 转 category 的时候，需要对自己的表新增层级关系的时候
            if($flag[0]['num'] == 0)return [];



            $arr = Db::name($config['table'])
                ->field($config['field'])
                ->where(isset($config['where']) ? $config['where'] : '')
                ->order(isset($config['order']) ? $config['order']: '')
                ->select();

        }
        $tree = [];
        //创建初始化数组
        foreach($arr as $k => $v){
            //如果第一个元素的pid 不是0 则，默认处理为0
            if($k == 0 && $v[$field[1]] != 0){
                $v[$field[1]] = 0;
            }
            $tree[ $v[ $field[0] ] ] = $v;
            $tree[ $v[ $field[0] ] ]['_path'] = [0];
            $tree[ $v[ $field[0] ] ]['son'] = [];
        }
        //引用
        foreach($tree as $sk => $sv){

            //自动生成层级关系
            if($sv[$field[1]] != 0 ){
                //查找的数据 可能上级的元素不存在
                if(!isset($tree[$sv[$field[1]]])){
                   continue;
                }else{
                    $tree[$sk]['_path'] =  $tree[$sv[$field[1]]]['_path'] ;
                    array_push($tree[$sk]['_path'],$tree[$sk][$field[1]]);
                    //传值引用
                    $tree[ $sv[ $field[1] ] ]['son'][] = &$tree[$sk];
                }
            }
        }
        //剔除多余元素
        foreach($tree as $k => $v){
            //层级关系字符串化
            $tree[$k]['_path'] = join(',',$v['_path']);

            //如果是直接返回树形层级关系的数组 则 剔除掉多余的数组 否则保留 table搜索的时候 会经过下一个处理 进行二次转换
            if($return_tree_array == true){
                if(isset($v[$field[1]]) && $v[$field[1]] != 0)  unset($tree[$k]);
            }

        }

        sort($tree);
        if($return_tree_array == true) return $tree;
        $arr = self::tree_to_array($tree ,$config['field'] ,$keep_array);

        //此刻的数据已经是带着层级排列 删除多余的数组属性
        foreach($arr as $ak => $av){
            if(isset($av['son']))unset($av['son']);
            if(isset($av['_path']))unset($av['_path']);
            $arr[$ak] = $av;
        }
        //释放变量
        unset($tree);
        //sort($arr);//不能重新排序 会影响到 treeselect 的value值 具体情况 看控制器逻辑层是否需要重新排序
        return $arr;


    }


    /**
     * 层级树形结构的 多维数组 转化成  带层级关系的一维数组
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

        foreach($tree as $k => $v){
            if(!$keep_array){

                //如果已经有层级符号则不添加
                if(!strpos($v[$showfield],'|-')) {
                    $arr[$v[$field['0']]] = self::buildSpace($v['_path']) . $v[$showfield];
                }
            }else{
                //如果已经有层级符号则不添加
                if(!strpos($v[$showfield],'|-')){
                    $v[$showfield] = self::buildSpace($v['_path']).$v[$showfield];
                }

                $arr[$v[$field['0']]] = $v;
            }

            if(count($v['son'])){
                $gui = self::tree_to_array($v['son'] ,$component_setting_field ,$keep_array);
                $arr = $arr + $gui;
            }
        }
        return $arr;
    }


    /*
    * 生成空格层级字符串
    */
    protected static function buildSpace($str){

        $spacer = '';
        if($str == '0'){
            $spacer .= '';
        }else{
            $path = explode(',',$str);
            $spacer .= '&nbsp;&nbsp;';
            for($i = 0 ;$i < count($path)-1 ;$i++ ){
                $spacer .= '&nbsp;&nbsp;&nbsp;&nbsp;';
            }
            $spacer .= '|- ';
        }

        return $spacer;
    }

}

