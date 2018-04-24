<?php
namespace CMaker\components;
use CMaker\Component;

/**
 * 单行文本input text
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class text extends Component
{

    public static function attr(){
        return [
            'label'=>'单行文本',
            'placeholder' => '单行文本',
            'helpinfo' => '',
            'value' => '',
            'classname' => 'preview',
            'name' => '',
            'layVerify' => '',
            'placeholder' => '',
            'showtype' => 'inline',
            'disabled' => false,
        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;
        $attr['placeholder'] = $attr['placeholder']?$attr['placeholder']:$attr['label'];
        $attr['disabled'] = $attr['disabled'] ? 'disabled' : '';

        $dom = <<<EOT
    <div class="layui-form-item" component-name="{$attr['component_name']}">
        <label class="layui-form-label">{$attr['label']}</label>
        <div class="layui-input-{$attr['showtype']}">
          <input lay-verify="{$attr['layVerify']}" type="text" class="{$attr['classname']} layui-input" name="{$attr['name']}" value="{$attr['value']}"  placeholder="{$attr['placeholder']}" {$attr['disabled']}>
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