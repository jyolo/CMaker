<?php
namespace CMaker\components;
use CMaker\Component;

/**
 * Created by PhpStorm.
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class ueditor extends Component
{

    public static function attr(){
        return [
            'label'=>'文本编辑器',
            'helpinfo' => '',
            'value' => '',
            'layVerify' => '',
            'name' => 'content',
            'show' => 'simple',
            'serverUrl' => url('/mvcbuilder/ueditor'),
        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;

        $dom = <<<EOT
    <div class="layui-form-item" component-name="{$attr['component_name']}">
        <label class="layui-form-label">{$attr['label']}</label>
        <div class="layui-input-block">
          <script id="{$attr['id']}" class="{$attr['component_name']}" name="{$attr['name']}" type="text/plain" style="margin-bottom: 10px;">{$attr['value']}</script>
        </div>
    </div>
   
EOT;

        return $dom;
    }

    /**
     * 组件依赖的js 插件
     */
    public static function relyOnJsPlugin($static_path){
        $arr = [
//            'css' => [
//                [
//                    'path' => "{$static_path}plugin/layui/css/layui.css",
//                    'attr' => ['media' => 'all']
//                ]
//            ],
            'js' => [
                [
                    'path' => "{$static_path}plugin/ueditor/ueditor.config.js"
                ],
                [
                    'path' => "{$static_path}plugin/ueditor/ueditor.all.js"
                ]
            ]
        ];
        return $arr;
    }

    /*
     * 设置组件的 javascript 脚本
     * @return string
     * */
    public static function script($attr){
        $toolbars['simple'] = [
            ['fullscreen', 'source', 'undo', 'redo', 'bold']
        ];
        $toolbars['middle'] = [
            ['fullscreen', 'source', 'undo', 'redo', 'bold','bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc']
        ];
        $toolbars['all'] = false;
        $toolbars = json_encode($toolbars);

        $js = <<<EOT
        var tool = JSON.parse('{$toolbars}'); 
        var show = attr.set.show;
        var toolbars = tool[''+show+''];
       
        if(tool[''+show+''] != false){
            var config = {serverUrl:'{$attr['set']['serverUrl']}' , toolbars:toolbars};
        }else{
           var config = {serverUrl:'{$attr['set']['serverUrl']}'};
        }
        
        var ue = UE.getEditor(''+attr.set.id+'',config);
EOT;
        return $js;
    }


}