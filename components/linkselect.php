<?php
namespace CMaker\components;
use CMaker\Component;
use CMaker\Maker;


/**
 * 联动选择组件
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 *
{:CMaker('linkselect')
->label('所属分类|所属仓库|选择货品')
->helpinfo('联动选择')
->linkfield('cid|wid|gid')
->serverUrl(url('goodsin/linkselect'))
->param('type - goods_cat,a - b | type - goods_warehouse | type - goods')
->showfield('id - cat_name|wid - wname|id - name')
->value('2|2|5')
->render()}
 */
class linkselect extends Component
{

    /**
     * 设置组件的属性以及初始默认值
     * @return array
     */
    public static function attr(){
        return [
            'label' => '联动选择1|联动选择2',
            'helpinfo' => '',
            'layVerify' => '',
            'name' => 'gid',
            'linkfield' => 'pid|id',
            'serverUrl' => '',
            'param' => '', // key-value | key-value |type-goods
            'showfield' => '', // id,goodsname|wid,wname
            'value' => '',//对应上面的linkfield 数字即可 ，2,2,3

        ];

    }



    /*
     * 设置dom结构
     * @return string
     * */
    public static function dom(){
        $attr = self::$attr;
//        $attr['param'] = self::strToArray($attr['param']);
//        p($attr['param']);

        $label = explode('|',$attr['label']);
        $linkfield = explode('|',$attr['linkfield']);

        $str = '';
        foreach($label as $k => $v){

            $str .= <<<EOT
    <label class="layui-form-label">{$v}</label>
    <div class="layui-input-inline">
        <select name="{$linkfield[$k]}" lay-verify="{$attr['layVerify']}" >
            <option value="">请选择</option>
        </select>
    </div>
EOT;

        }

        $dom = <<<EOT
            <div class="layui-form-item {$attr['component_name']}" component-name="linkselect" id="{$attr['id']}">
                    {$str}
                    <div class="layui-form-mid layui-word-aux">{$attr['helpinfo']}</div>
                </div>
EOT;

        return $dom;
    }



    /*
     * 设置组件的 javascript 脚本
     * @return string
     * */
    public static function script($attr){


        $js =<<<EOT
       
;layui.use(['form','jquery'], function(){
        var form = layui.form;
        var $ = layui.jquery;
          
        var the_last_select = $('#'+attr.uniqid_id).find('select').length - 1;
        var selects = $('#'+attr.uniqid_id).find('select');
       
        
        var allparam = {};
        var value = attr.set.value.split('|');
        
        if(attr.set.param.length > 0){
            var param = strtoobj(attr.set.param);
        }else{
            var param = {};
        }
        if(attr.set.showfield.length > 0){
            var showfield = strtoobj(attr.set.showfield);
        }else{
            var showfield = {};
        }
        
        selects.each(function(i,n){
            var filter = attr.uniqid_id + '_'+ i;
            $(n).attr('lay-filter',filter);
            
            if(i == 0){
                if(!param[i]) return;
                $.post(attr.set.serverUrl,param[i],function(msg){
                    builder_options(n,msg,showfield[i] ,value[i]);
                });
            }
            
            if(i != the_last_select){ //最后一个slelect 无需监听选择事件
                //var filter = attr.set.id + '_'+ i; 
                form.on('select('+ filter +')', function(data){
                   if(i == 0){
                       
                        //第一个选择之后，清空后面的
                        $.each(selects.slice(1) ,function(i,n){
                            $(n).find('option').slice(1).remove();
                        })
                   }
                    param[i+1][data.elem.name] = data.value;
                    allparam[data.elem.name] = data.value
                    var marge_param = $.extend(param[i+1],allparam);
                    $.post(attr.set.serverUrl,marge_param,function(msg){
                        builder_options(selects.eq(i+1),msg,showfield[i+1] ,value[i+1]);
                    });
                });
            }
            
        });
          
        function strtoobj(str){
            var re = {};
            $.each(str.split('|'),function(k,n){
                var _arr = n.split(',');
                var obj = {};
                $.each(_arr ,function(i,n){
                    var _arg = n.split('-');
                    obj[i] = new Function("return {'"+$.trim(_arg[0])+"':'"+$.trim(_arg[1])+"'}")();
                    re[k] = $.extend(re[k],obj[i]);
                })
            })
            return re;
        }
        function builder_options(select_dom ,json , showfield ,value = false){
            $(select_dom).find('option').slice(1).remove();
            var field = [] ;
            $.each(showfield,function(i,v){
                field[0] = i;field[1] = v;
            })
            var option = '';
            $.each(json,function(i,n){
                if(value == n[field[0]]){
                    var selected = 'selected';
                }else{
                    var selected = '';
                }
                option += '<option value="'+ n[field[0]] +'" '+selected+'>'+ n[field[1]] +'</option>';
            })
            $(select_dom).append(option);
            form.render('select');
            if(attr.set.value.length > 0){
                var values = attr.set.value.split('|');
                $(select_dom).next('.layui-unselect').find('dd').each(function(i,n){
                      if(values.indexOf($(n).attr('lay-value')) != '-1'){
                        $(n).trigger('click');
                      }
                });
            } 
        }
        
        
        
        
    })
EOT;
        return $js;
    }


    private static function strToArray($str){
        //字符串模式 转为数组模式

        $arr = explode('|',$str);

        $tem = [];

        foreach($arr as $k => $v){
            $k = trim($k);
            $v = trim($v);

            $arg = explode('-',$v);
            $tem[$k] = [trim($arg[0]) => trim($arg[1])];

        }
        return $tem;
    }

}