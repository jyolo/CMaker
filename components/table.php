<?php
namespace CMaker\components;
use CMaker\Component;

/**
 * Created by PhpStorm.
 * User: jyolo
 * Date: 2017/7/6
 * example
 * {:CMaker('table')->filter('manager')
 *   ->cols([
 *               ['checkbox' => true],
 *               ['field' => 'id' ,'title' => 'ID','sort' => true],
 *               ['field' => 'mobile' ,'title' => '手机号','sort' => true],
 *               ['toolbar' => '#actionTpl' ,'title' => '操作','fixed' => 'right'],
 *       ])
 *   ->url(url('base/manager/index'))
 *   ->render()}
 */
class table extends Component
{

    public static function attr(){
        return [
            'filter' => '',
            'cols' => [
                ['checkbox' => true]
            ],
            'url' => '',
            'method' => 'post',
            'height' => '500',
            'page' => true,
            'limit' => 10,
            'param' => [],
            'response' => [
                'statusName' => 'code',
                'statusCode' => 1,
                'msgName' => 'msg',
                'countName' => 'count',
                'dataName' => 'data'
            ]
        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;

        $id = self::$attr['id'];
        $dom = <<<EOT
    <table id="{$id}" lay-filter="{$attr['filter']}" class="layui-table"></table>
EOT;

        return $dom;
    }

    /**
     * 组件依赖的js 插件
     */
    public static function relyOnJsPlugin($static_path){
        return [];
    }

    /*
     * 设置组件的 javascript 脚本
     * @return string
     * */
    public static function script($attr){
        $srcript =<<<EOT
\n;layui.use(['table','tableExtend'], function(){
    var table = layui.table;
    var tableExtend = layui.tableExtend;
    var set = attr.set;
    
    //执行渲染
    table.render({
        elem: '#'+set.id //指定原始表格元素选择器（推荐id选择器）
        ,id: set.id
        ,height: set.height //容器高度
        ,cols:  [set.cols]
        ,url: set.url
        ,where:set.param
        ,page : set.page
        ,limit : set.limit
        ,method: set.method
        ,response: set.response
    });
    //设定tableid
    tableExtend.tableID = set.id; 
    
    table.on('tool('+attr.set.filter+')',tableExtend._tool);
    table.on('checkbox('+attr.set.filter+')',tableExtend._checkbox);

});\r\n
EOT;
        return $srcript;
    }



}