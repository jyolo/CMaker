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
class select extends Component
{

    public static function attr(){
        return [
            'label'=>'下拉选择',
            'helpinfo' => '',
            'option' => '选项一|选项二',//支持 字符串 和 数组 两种形式
            'choose' => '',
            'fields' => false,
            'name' => 'defualt',
            'laySearch' => false,
            'layVerify' => '',
            'layFilter' => '',
        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;

        $option = '<option value="">请选择</option>';

        if(is_string($attr['option'])) {
            $attr['option'] = self::strToArray($attr);
        }

        if(!strlen($attr['choose'])) $attr['choose'] = null;
        //if(!strlen($attr['choose']) || $attr['choose'] == 0)$attr['choose'] = null;

        if(strlen($attr['choose'])){
            if(!isset($attr['option'][$attr['choose']]))$attr['choose'] = '';
        }

        $attr['laySearch'] =  $attr['laySearch'] ? 'lay-search' : '';


        foreach($attr['option'] as $k => $v){
            if($attr['fields'] != false){
                $f = explode(',',$attr['fields']);

                $selected = (strlen($attr['choose']) && $attr['choose'] == $v[$f[0]]) ?  'selected' :  '';
                $option .= '<option value="'.$v[$f[0]].'" '.$selected.'>'.$v[$f[1]].'</option>';
            }else{
                $selected = (strlen($attr['choose']) && $attr['choose'] == $k) ?  'selected' :  '';
                $option .= '<option value="'.$k.'" '.$selected.'>'.$v.'</option>';
            }
        }

        $dom = <<<EOT
    <div class="layui-form-item"  component-name="{$attr['component_name']}">
        <label class="layui-form-label">{$attr['label']}</label>
        <div class="layui-input-inline">
            <select name="{$attr['name']}" lay-verify="{$attr['layVerify']}" lay-filter="{$attr['layFilter']}" {$attr['laySearch']}>
            {$option}
            </select>
        </div>
        <div class="layui-form-mid layui-word-aux">{$attr['helpinfo']}</div>
    </div>
EOT;

        return $dom;
    }

    //处理select的value 和选中的值
    private static function strToArray($attr){
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