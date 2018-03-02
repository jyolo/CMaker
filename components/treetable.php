<?php
namespace CMaker\components;
use CMaker\Component;
use think\Exception;
use think\Db;
/**
 * Created by PhpStorm.
 * User: jyolo
 * Date: 2017/7/6
 * example
 * {:CMaker('treetable')->filter('manager')
 *   ->cols([
 *               ['checkbox' => true],
 *               ['field' => 'id' ,'title' => 'ID','sort' => true],
 *               ['field' => 'mobile' ,'title' => '手机号','sort' => true],
 *               ['toolbar' => '#actionTpl' ,'title' => '操作','fixed' => 'right'],
 *       ])
 *   ->render()}
 */
class treetable extends Component
{
    public static $data;
    public static function attr(){
        return [
            'filter' => '',
            'cols' => [
                ['checkbox' => true]
            ],
            'table' => '',
            'field' => '',
            'height' => '500',


        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;


//        $thead = '';
//        foreach($attr['cols'] as $k => $v){
//            $title = isset($v['title']) ? $v['title'] :'';
//            $json = json_encode($v);
//
//            $thead .= '<th lay-data="'.json_encode($v).'">'.$title.'</th>';
//        }


        $data = self::get_tree_array(self::$attr);

        $data = self::tree_to_array($data ,self::$attr['field']);

        $tbody = '<tbody>';
        foreach($data as $k => $v){
            $tbody .= '<tr>';
            unset($data[$k]['son']);
            unset($v['path']);
            foreach($attr['cols'] as $sk => $sv){
                if(isset($sv['field'])){
                    $tbody .= '<td>'.$v[$sv['field']].'</td>';
                }else{
                    $tbody .= '<td></td>';
                }

            }

            $tbody .= '</tr>';
        }

        self::$data = $data;


        $id = self::$attr['id'];
        $dom = <<<EOT
    <table id="{$id}" lay-filter="{$attr['filter']}" class="layui-table">
        
        
    </table>
EOT;

        return $dom;
    }




    /*
     * 设置组件的 javascript 脚本
     * @return string
     * */
    public static function script($attr){
        $json = json_encode(self::$data);

        $srcript =<<<EOT
\n;layui.use(['table','tableExtend'], function(){
    var table = layui.table;
    var tableExtend = layui.tableExtend;
    var set = attr.set;
    var data =  JSON.parse('{$json}');
    p(data);
    //执行渲染
    table.render({
        elem: '#'+set.id //指定原始表格元素选择器（推荐id选择器）
        ,id: set.id
        ,height: set.height //容器高度
        ,cols:  [set.cols]
        ,data: []
       
    });
    //设定tableid
    tableExtend.tableID = set.id; 
    
    table.on('tool('+attr.set.filter+')',tableExtend._tool);
    table.on('checkbox('+attr.set.filter+')',tableExtend._checkbox);

});\r\n
EOT;
        return $srcript;
    }

    /**
     * 层级树形结构的 多维数组 转化成  带层级关系的一维数组
     * @param $tree
     * @return array
     */
    protected static function tree_to_array($tree ,$component_setting_field = false){
        if(!$component_setting_field) throw new Exception('缺少 组件设置的 field的参数');
        $arr = [];
        $field = explode(',',$component_setting_field);
        //第三个字段 为显示的字段
        $showfield = (isset($field[2]) && strlen($field[2])) ? $field[2]: $field[1];

        foreach($tree as $k => $v){
            $v[$showfield] = self::buildSpace($v['_path']).$v[$showfield];
            $arr[$v[$field['0']]] = $v;
            if(count($v['son'])){
                $gui = self::tree_to_array($v['son'] ,$component_setting_field);
                $arr = $arr + $gui;

            }
        }
        return $arr;
    }




}