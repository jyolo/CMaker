<?php
namespace CMaker\components;
use CMaker\Component;

/**
 * Created by PhpStorm.
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class checkbox extends Component
{

    public static function attr(){
        return [
            'label'=>'复选框',
            'helpinfo' => '帮助信息',
            'option' => ['选项一','选项二'],
            'choose' => '',
            'name' => 'defualt',
            'layVerify' => '',

        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;

        //字符串模式 转为数组模式
        if(is_string($attr['option'])){
            //$attr['option'] = explode('|',$attr['option']);
            $attr['option'] = self::handlerValue($attr);

        }

        if(strlen($attr['choose'])){
            $choose = explode(',',$attr['choose']);
        }else{
            $choose = [];
        }


        $input = '';

        foreach($attr['option'] as $k => $v){

            $checked = (in_array($k,$choose)) ?  'checked' :  '';

            $input .= '<input type="checkbox" name="'.$attr['name'].'[]" value="'.$k.'" title="'.$v.'" '.$checked.'> ';
        }

        $dom = <<<EOT
    <div class="layui-form-item" component-name="{$attr['component_name']}">
        <label class="layui-form-label">{$attr['label']}</label>
        <div class="layui-input-block">
            {$input}
        </div>
        <div class="layui-form-mid layui-word-aux">{$attr['helpinfo']}</div>
    </div>
EOT;

        return $dom;
    }

    //处理select的value 和选中的值
    private static function handlerValue($attr){
        //字符串模式 转为数组模式

        $attr['option'] = explode('|',$attr['option']);
        $tem = [];

        foreach($attr['option'] as $k => $v){
            $arg = explode('-',$v);
            if(count($arg) == 1){
                $tem[$k] = $arg[0];
            }else{
                $tem[$arg[0]] = $arg[1];
            }

        }
        $attr['option'] = $tem;




        return $attr['option'];
    }




}