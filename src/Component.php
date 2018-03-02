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
     * @param array $config
     * @return array
     * @throws Exception
     */
    public static function get_tree_array($config = []){
        $field = explode(',',$config['field']);

        if(!isset($field[1]))throw new Exception('缺少父级关系字段 比如:parentid');

        if(isset($config['treeData']) ){
            if(!count($config['treeData'])) return [];
            $arr = $config['treeData'];
        }else{
            $sql = 'show tables like \''.config('database.prefix').$config['table'].'\' ';
            $flag = Db::query($sql);
            //表不存在返回空数组
            if(!count($flag))return [];

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
            //if(!isset($tree[$sv[$field[1]]]))$tree[$sv[$field[1]]] = [];
            if($sv[$field[1]] != 0 ){
                //自动生成层级关系

                $tree[$sk]['_path'] =  $tree[$sv[$field[1]]]['_path'] ;
                array_push($tree[$sk]['_path'],$tree[$sk][$field[1]]);
                //传值引用
                $tree[ $sv[ $field[1] ] ]['son'][] = &$tree[$sk];

            }
        }


        //剔除多余元素
        foreach($tree as $k => $v){
            //层级关系字符串化
            $tree[$k]['_path'] = join(',',$v['_path']);
            if(isset($v[$field[1]]) && $v[$field[1]] != 0)  unset($tree[$k]);
        }


        sort($tree);

        return $tree;

    }


    /**
     * 层级树形结构的 多维数组 转化成  带层级关系的一维数组
     * @param array $tree 树形结构的数据
     * @param string $component_setting_field 字段字符串 id,pid
     * @param boolean $keep_array 是否保留数组
     * @return array
     */
    public static function tree_to_array($tree ,$component_setting_field = false ,$keep_array = false){
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

