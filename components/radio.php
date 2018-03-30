<?php
namespace CMaker\components;
use CMaker\Component;

/**
 * Created by PhpStorm.
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class radio extends Component
{

    public static function attr(){
        return [
            'label'=>'单选框',
            'helpinfo' => '帮助信息',
            'option' => ['选项一','选项二'],
            'choose' => '',
            'fields' => false,
            'name' => 'defualt',
            'layVerify' => '',
            'layFilter' => '',
        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;
        //字符串模式 转为数组模式
        if(is_string($attr['option'])){
            //$attr['value'] = explode('|',$attr['value']);
            $attr['option'] = self::handlerValue($attr);

        }

        if(strlen($attr['choose'])){
            if(!isset($attr['option'][$attr['choose']]))throw new Exception('option中没有该选项');
        }



        $input = '';
        foreach($attr['option'] as $k => $v){
            //$input .= '<input type="radio" name="'.$attr['name'].'"  title="'.$v.'" '.$checked.'> ';
            //fixed  同一个页面多个radio 在name 值相同的情况下 checked 状态会失效
            //未设置name的都为 defualt
            if($attr['name'] == 'defualt')$attr['name'] = $attr['name'].$attr['id'];

            if($attr['fields'] != false){
                $f = explode(',',$attr['fields']);
                $checked = (intval($attr['choose']) == $v[$f[0]]) ?  'checked' :  '';
                $input .= '<input type="radio" name="'.$attr['name'].'" value="'.$v[$f[0]].'"  lay-filter="'.$attr['layFilter'].'" lay-verify="'.$attr['layVerify'].'"  title="'.$v[$f[1]].'" '.$checked.'> ';
            }else{


                $checked = ($attr['choose'] == $k) ?  'checked' :  '';
                $input .= '<input type="radio" name="'.$attr['name'].'" value="'.$k.'"  lay-filter="'.$attr['layFilter'].'" lay-verify="'.$attr['layVerify'].'"  title="'.$v.'" '.$checked.'> ';
            }

        }


        $dom = <<<EOT
    <div class="layui-form-item"  component-name="{$attr['component_name']}">
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