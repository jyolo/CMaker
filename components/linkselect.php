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
->value('2|2|5')
->select([
['param' => ['type' => 'goods_cat'] ,'showfield' => 'id,cat_name' ],
['param' => ['type' => 'goods_warehouse'] ,'showfield' => 'wid,wname'],
['param' => ['type' => 'goods'] ,'showfield' => 'id,name']
])
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
            'linkfield' => 'pid|id',
            'value' => '',
            'serverUrl' => '',
            'select' => [],
        ];

    }



    /*
     * 设置dom结构
     * @return string
     * */
    public static function dom(){
        $attr = self::$attr;

        $label = explode('|',$attr['label']);
        $linkfield = explode('|',$attr['linkfield']);

        $str = '';
        foreach($label as $k => $v){

            $str .= <<<EOT
    <label class="layui-form-label">{$v}</label>
    <div class="layui-input-inline">
        <select name="{$linkfield[$k]}" lay-verify="required" lay-filter="{$attr['id']}_{$k}">
            <option value="">请选择</option>
        </select>
    </div>
EOT;

        }

        $dom = <<<EOT
            <div class="layui-form-item" component-name="linkselect" id="{$attr['id']}">
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
        p(attr);
;layui.use(['form','jquery'], function(){
        var form = layui.form;
        var $ = layui.jquery;
          
        var the_last_select = $('#'+attr.uniqid_id).find('select').length - 1;
        var selects = $('#'+attr.uniqid_id).find('select');
        var allparam = {};
        var value = attr.set.value.split('|');
        
        selects.each(function(i,n){
            if(i == 0){
                if(!attr.set.select[i]) return;
                $.post(attr.set.serverUrl,attr.set.select[i].param,function(msg){
                    builder_options(n,msg,attr.set.select[i].showfield ,value[i]);
                });
            }
            
            if(i != the_last_select){ //最后一个slelect 无需监听选择事件
                var filter = attr.set.id + '_'+ i; 
                form.on('select('+ filter +')', function(data){
                   if(i == 0){
                        //第一个选择之后，清空后面的
                        $.each(selects.slice(1) ,function(i,n){
                            $(n).find('option').slice(1).remove();
                        })
                   }
                
                    var param = attr.set.select[i+1].param;
                    param[data.elem.name] = data.value;
                    allparam[data.elem.name] = data.value
                    var marge_param = $.extend(param,allparam);
                    
                    $.post(attr.set.serverUrl,marge_param,function(msg){
                        var showfield = attr.set.select[i+1].showfield;
                        builder_options(selects.eq(i+1),msg,showfield ,value[i+1]);
                    });
                });
            }
            
        });
        
        function builder_options(select_dom ,json , showfield ,value = false){
            
            $(select_dom).find('option').slice(1).remove();
            var field = showfield.split(',');
            
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
            
            $(select_dom).next('.layui-unselect').find('dd').each(function(i,n){
                  if($(n).attr('lay-value') == value){
                    $(n).trigger('click');
                  }
            });
            
            
            
        }
        
        
        
        
    })
EOT;
        return $js;
    }


    

}