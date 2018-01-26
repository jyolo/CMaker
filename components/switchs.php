<?php
namespace CMaker\components;
use CMaker\Component;

/**
 * Created by PhpStorm.
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class switchs extends Component
{

    public static function attr(){
        return [
            'label'=>'开关',
            'helpinfo' => '',
            'open' => '',
            'classname' => 'preview',
            'name' => 'defualt',
            'text' => '是|否',
            'layVerify' => '',
            'disabled' => '',
        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;
        $attr['disabled'] = $attr['disabled'] ? 'disabled' : ''; //是否禁用
        $attr['open'] = (($attr['open'] === 'on') || ($attr['open'] == '1')) ? 'checked' : ''; //是否开启

        $value = 1 ; //开启时候的值 ，关闭则无值

        $dom = <<<EOT
    <div class="layui-form-item" component-name="{$attr['component_name']}">
        <label class="layui-form-label">{$attr['label']}</label>
        <div class="layui-input-inline">
            <input type="checkbox" name="{$attr['name']}" lay-skin="switch" value="{$value}" lay-text="{$attr['text']}" {$attr['open']} {$attr['disabled']}>
        </div>
        <div class="layui-form-mid layui-word-aux">{$attr['helpinfo']}</div>
    </div>
EOT;

        return $dom;
    }



}