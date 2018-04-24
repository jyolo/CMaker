<?php
namespace CMaker\components;
use CMaker\Component;

/**
 * 多行文本text
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class textarea extends Component
{

    public static function attr(){
        return [
            'label'=>'文本输入',
            'placeholder' => '多行文本输入框',
            'helpinfo' => '',
            'value' => '',
            'classname' => 'preview',
            'name' => '',
            'layVerify' => '',
            'placeholder' => '',
            'showtype' => 'block',
        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;
        $attr['placeholder'] = $attr['placeholder']?$attr['placeholder']:$attr['label'];


        $dom = <<<EOT
    <div class="layui-form-item layui-form-text" component-name="{$attr['component_name']}">
        <label class="layui-form-label">{$attr['label']}</label>
        <div class="layui-input-{$attr['showtype']}">
           <textarea lay-verify="{$attr['layVerify']}" type="text" class="{$attr['classname']} layui-textarea" name="{$attr['name']}" placeholder="{$attr['placeholder']}">{$attr['value']}</textarea>
        </div>
        <div class="layui-form-mid layui-word-aux">{$attr['helpinfo']}</div>
    </div>
EOT;

        return $dom;
    }

    /**
     * 组件依赖的js 插件
     */
    public static function relyOnJsPlugin($static_path){return;}

    /*
     * 设置组件的 javascript 脚本
     * @return string
     * */
    public static function script($attr){return;}


}