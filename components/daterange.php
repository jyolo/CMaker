<?php
namespace CMaker\components;
use CMaker\Component;


/**
 * Created by PhpStorm.
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class daterange extends Component
{

    /**
     * 设置组件的属性以及初始默认值
     * @return array
     */
    public static function attr(){
        return [
            'label' => '时间范围',
            'name' => '',
            'placeholder' => '时间范围选择',
            'classname' => '',
            'value' => '',
            'helpinfo' => '帮助信息',
            'layVerify' => '',
            'range' => '-',//组件js的分隔符
            'display' => 'inline', //根据layui的表单形式 block 和 inline
            'readonly' => '',
        ];
    }


    /*
     * 设置dom结构
     * @return string
     * */
    public static function dom(){
        $attr = self::$attr;
        $id = self::$attr['id'];

        $attr['readonly'] = ($attr['readonly'] == 'on') ? 'readonly' : '';

        $dom = <<<EOT
            <div class="layui-form-item" component-name="{$attr['component_name']}">
                <label class="layui-form-label">{$attr['label']}</label>
                <div class="layui-input-{$attr['display']}">
                    <input id="{$id}"  lay-verify="{$attr['layVerify']}" class="layui-input {$attr['component_name']} {$attr['classname']}" value="{$attr['value']}" name="{$attr['name']}" placeholder="{$attr['placeholder']}" {$attr['readonly']}>
                </div>
                <div class="layui-form-mid layui-word-aux">{$attr['helpinfo']}</div>
            </div>
EOT;

        return $dom;
    }




    /*
     * 设置组件的 javascript 脚本
     * 防止多组件直接的变量冲突 ,系统会自动把组件的js 脚本转化为function(attr){//code}()
     * 函数会自动注入 该组件的设置 在js 脚本内 可以直接使用 attr.uniqid_id 或者 attr.component_name
     * @return string
     * */
    public static function script($attr){

        $srcript =<<<EOT
        
layui.use('laydate', function(){
    var laydate = layui.laydate;
    laydate.render({
        elem: '#'+attr.uniqid_id
        ,type: 'datetime'
        ,range: ''+attr.set.range+'' //或 range: '~' 来自定义分割字符
    });
});
EOT;
        return $srcript;
    }

    

}