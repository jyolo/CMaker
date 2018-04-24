<?php
namespace CMaker\components;
use CMaker\Component;


/**
 * 时间选择器
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class datepicker extends Component
{

    /**
     * 设置组件的属性以及初始默认值
     * @return array
     */
    public static function attr(){
        return [
            'label' => '时间选择器',
            'name' => '',
            'placeholder' => '时间选择器',
            'classname' => '',
            'value' => '',
            'helpinfo' => '',
            'layVerify' => '',
            // year	年选择器	只提供年列表选择
            // month 年月选择器	只提供年、月选择
            //date	日期选择器	可选择：年、月、日。type默认值，一般可不填
            //time	时间选择器	只提供时、分、秒选择
            //datetime	日期时间选择器	可选择：年、月、日、时、分、秒
            'type' => 'date',
            //是否开启范围选择
            'range' => '',
            //format - 自定义格式
            'format' => '',
            //语言cn（中文版）、en（国际版，即英文版）
            'lang' => 'cn',
            //主题 default（默认简约）、molv（墨绿背景）、#颜色值（自定义颜色背景）、grid（格子主题）
            'theme' => 'default',
            'display' => 'inline', //根据layui的表单形式 block 和 inline
            'readonly' => '1',
        ];
    }


    /*
     * 设置dom结构
     * @return string
     * */
    public static function dom(){
        $attr = self::$attr;
        $id = self::$attr['id'];

        $attr['readonly'] = ($attr['readonly'] == '1') ? 'readonly' : '';

        $dom = <<<EOT
            <div class="layui-form-item" component-name="{$attr['component_name']}">
                <label class="layui-form-label">{$attr['label']}</label>
                <div class="layui-input-{$attr['display']}">
                    <input id="{$id}"  lay-verify="{$attr['layVerify']}" class="layui-input {$attr['component_name']} {$attr['classname']}" value="{$attr['value']}" name="{$attr['name']}" placeholder="{$attr['placeholder']}" {$attr['readonly']}>
                </div>
                <div class="layui-form-mid layui-word-aux">{$attr['helpinfo']}</div>
            </div>
EOT;

        return $dom;
    }




    /*
     * 设置组件的 javascript 脚本
     * 防止多组件直接的变量冲突 ,系统会自动把组件的js 脚本转化为function(attr){//code}()
     * 函数会自动注入 该组件的设置 在js 脚本内 可以直接使用 attr.uniqid_id 或者 attr.component_name
     * @return string
     * */
    public static function script($attr){

        $srcript =<<<EOT
    var set = attr.set;
    
    layui.use('laydate', function(){
        var laydate = layui.laydate;
        var option = {
                elem: '#'+attr.uniqid_id
                ,type: ''+set.type+''
                ,lang: ''+set.lang+''
                ,theme: ''+set.theme+''
                ,range: ''+set.range+'' //或 range: '~' 来自定义分割字符
            };
        if(set.format.length > 0 ) {
            option['format'] = set.format;
        }    
        if(set.range != false){delete option['format']};
        
        laydate.render(option);
});
EOT;
        return $srcript;
    }

    

}