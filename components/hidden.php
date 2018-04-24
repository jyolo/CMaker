<?php
namespace CMaker\components;
use CMaker\Component;

/**
 * 隐藏文本
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class hidden extends Component
{

    public static function attr(){
        return [
            'label'=>'单行文本',
            'value' => '',
            'classname' => 'preview',
            'name' => '',
        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;



        $dom = <<<EOT
          <input  type="hidden" class="{$attr['classname']} layui-input" name="{$attr['name']}" value='{$attr['value']}' >
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