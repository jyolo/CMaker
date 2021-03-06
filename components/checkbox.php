<?php
namespace CMaker\components;
use CMaker\Component;

/**
 * 复选框
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class checkbox extends Component
{

    public static function attr(){
        return [
            'label'=>'复选框',
            'helpinfo' => '',
            'option' => ['选项一','选项二'],
            'choose' => '',
            'fields' => false , //多维数组的时候，指定 值 ,显示的 字段
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
            if($attr['fields'] != false){
                $f = explode(',',$attr['fields']);

                $checked = (in_array($v[$f[0]],$choose)) ?  'checked' :  '';
                $input .= '<input type="checkbox" name="'.$attr['name'].'[]" value="'.$v[$f[0]].'" lay-filter="'.$attr['layFilter'].'" lay-verify="'.$attr['layVerify'].'" title="'.$v[$f[1]].'" '.$checked.'> ';
            }else{
                $checked = (in_array($k,$choose)) ?  'checked' :  '';
                $input .= '<input type="checkbox" name="'.$attr['name'].'[]" value="'.$k.'" lay-filter="'.$attr['layFilter'].'" lay-verify="'.$attr['layVerify'].'" title="'.$v.'" '.$checked.'> ';
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

    public static function script($attr){
        $script = <<<EOT
\n;layui.use(['form'], function(){
    var form = layui.form;
    
    var set = attr.set;
    
    p(set.name);
    set.nocheck = false;
    form.on('checkbox('+set.layFilter+')', function(data){
        $('input[name="'+set.name+'[]"]').each(function(i,n){
            if($(n).parent().find('.layui-form-checked').length == 0){
                set.nocheck = true;
            }else{
                set.nocheck = false;
            }
        })
        if(set.nocheck){
            $(data.elem).parent('div').append('<input type="hidden" class="no_check" name="'+set.name+'" value=""/>')
        }else{
            $(data.elem).parent('div').find('.no_check').remove();
        }

    });  
});

EOT;
        return $script;

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