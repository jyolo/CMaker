<?php
namespace CMaker\components;
use CMaker\Component;

/**
 * 数字文本
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class quilleditor extends Component
{

    public static function attr(){
        return [
            'label'=>'数字文本',
            'helpinfo'=> '',
            'value' => '',
            'classname' => 'preview',
            'name' => '',

        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;



        $dom = <<<EOT
    <div class="layui-form-item" component-name="{$attr['component_name']}">
        <label class="layui-form-label">{$attr['label']}</label>
        <div class="layui-input-inline">
          <textarea id="{$attr['id']}" name="{$attr['name']}">{$attr['value']}</textarea>
        </div>
        <div class="layui-form-mid layui-word-aux">{$attr['helpinfo']}</div>
    </div>
EOT;

        return $dom;
    }

    /**
     * 组件依赖的js 插件
     */
    public static function relyOnJsPlugin($static_path){
        $arr = [
            'css' => [
                [
                    'path' => "https://cdn.quilljs.com/1.3.6/quill.snow.css",
                ]
            ],
            'js' => [
                [
                    'path' => "http://cdn.quilljs.com/1.3.6/quill.min.js"
                ],
            ]
        ];
        return $arr;
    }

    /*
     * 设置组件的 javascript 脚本
     * @return string
     * */
    public static function script($attr){return;}


}