<?php
namespace CMaker\components;
use CMaker\Component;

/**
 * switch开关
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
            'onvalue' => '1',
            'offvalue' => '0',
            'layVerify' => '',
            'layFilter' => '',
            'disabled' => '',
        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;
        $attr['disabled'] = $attr['disabled'] ? 'disabled' : ''; //是否禁用
        $attr['open'] = (($attr['open'] === 'on') || ($attr['open'] == '1')) ? 'checked' : ''; //是否开启


        $value = ($attr['open'] == 'checked') ? $attr['onvalue']: $attr['offvalue'] ; //开启时候的值 ，关闭则无值
        $attr['layFilter'] = $attr['layFilter'] ? $attr['layFilter'] : $attr['id'];


        $dom = <<<EOT
    <div class="layui-form-item" component-name="{$attr['component_name']}">
        <label class="layui-form-label">{$attr['label']}</label>
        <div class="layui-input-inline">
            <input id="{$attr['id']}_hidden" type="hidden" name="{$attr['name']}" value="{$value}">
            <input id="{$attr['id']}" type="checkbox" lay-skin="switch" value="" lay-filter="{$attr['layFilter']}" lay-text="{$attr['text']}" {$attr['open']} {$attr['disabled']}>
        </div>
        <div class="layui-form-mid layui-word-aux">{$attr['helpinfo']}</div>
    </div>
EOT;

        return $dom;
    }

    public static function script($attr){
        $script = <<<EOT
        
\n;layui.use(['form'], function(){
    var set = attr.set;
    var form = layui.form;
    var $ = layui.jquery;
   
    $('#'+set.id).attr('name','');
    set.layFilter = set.layFilter ? set.layFilter : set.id;
    
    form.on('switch('+set.layFilter+')', function(data){
        //开关是否开启，true或者false
      if(data.elem.checked == true)
      {
        $('#'+set.id+'_hidden').attr('value' ,set.onvalue);
      }else{
        $('#'+set.id+'_hidden').attr('value' ,set.offvalue);
      }
    });  
})
EOT;
    return $script;

    }


}