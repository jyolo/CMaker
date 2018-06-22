<?php
namespace CMaker\components;
use CMaker\Component;
use think\Exception;

/**
 * 下拉选择
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class searchSelect extends Component
{

    public static function attr(){
        return [
            'label'=>'搜索下拉选择',
            'helpinfo' => '',
            'placeholder' => '请选择',
            'url' => '',//支持 字符串 和 数组 两种形式
            'param' => '',//请求的参数
            'name' => '',
            'width' => '190',
            'height'=> '38',
            'layVerify' => '',
            'layFilter' => '',
        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;

        $dom = <<<EOT
    <style type="text/css">
    .select2-container .select2-selection--single{  
      height:{$attr['height']}px !important;
      line-height: {$attr['height']}px !important;
    }  
    .select2-container--default .select2-selection--single{
        border-radius:0px !important;
        border:1px solid #e6e6e6 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered{
        line-height:{$attr['height']}px !important;;
        color: #999999;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow{
        height: {$attr['height']}px !important;
    }
</style>
    <div class="layui-form-item"  component-name="{$attr['component_name']}">
        <label class="layui-form-label">{$attr['label']}</label>
        <div class="layui-input-inline">
            <select class="searchSelect" name="{$attr['name']}" lay-ignore >
            </select>
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
                    'path' => "{$static_path}plugin/select2/select2.min.css",
                ]
            ],
            'js' => [
                [
                  'path' => 'https://cdn.bootcss.com/jquery/3.3.1/jquery.min.js',
                ],
                [
                    'path' => "{$static_path}plugin/select2/select2.min.js"
                ],
//                [
//                    'path' => "{$static_path}plugin/select2/zh-CN.js"
//                ]
            ]
        ];
        return $arr;
    }

    public static function script($attr){
        $script =<<<EOT
    layui.use(['jquery'],function () {
        var $ = layui.jquery;
        $('.searchSelect').select2({
            width: attr.set.width + 'px',
            placeholder: attr.set.placeholder ,
            language: "zh-CN",
            ajax: {
                url: attr.set.url,
                method:'post',
                dataType: 'json',
                delay:200,
                data: function (params) {
                    return {
                        input: params.term, // search term 请求参数为 input 请求框中输入的参数
                    };
                },
                processResults: function (data) { // 数据格式必须是 ['id' => 2 ,'text' => 'sadad']
                    return {
                        results: data
                    };
                }
                
            }
        });
    });
EOT;

        return $script;

    }




}