<?php
namespace CMaker\components;
use CMaker\Component;
use CMaker\Maker;


/**
 * 幻灯片组件
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class linkselect extends Component
{

    /**
     * 设置组件的属性以及初始默认值
     * @return array
     */
    public static function attr(){
        return [
            'label' => '',
            'helpinfo' => '',
            'linkfield' => '',
            'select' => [],
        ];

    }

    public static function get_select_one_option($option){
        $Db = new \think\Db(); //这里的DB 根据不同的框架 调用对应的 Db 类
        $str = '';
        if($option['istree'] == true){
            $res = Component::get_tree_array($option);
            $field = explode(',',$option['field']);

            foreach($res as $k => $v){
                $str .= '<option value="'.$k.'">'.$v.'</option>';
            }

        }else{
            $res = $Db::name($option['table'])->field($option['field'])->select()->toArray();
            $field = explode(',',$option['field']);
            $value_field = $field[0];
            $show_field = $field[1];
            foreach($res as $k => $v){
                $str .= '<option value="'.$v[$value_field].'">'.$v[$show_field].'</option>';
            }
        }




        return $str;
    }

    /*
     * 设置dom结构
     * @return string
     * */
    public static function dom(){
        $attr = self::$attr;


        $select_1 = $attr['select'][0];


        $label = explode('|',$attr['label']);
        $linkfield = explode('|',$attr['linkfield']);

        $str = '';
        foreach($label as $k => $v){
            if($k == 0){
                $option = self::get_select_one_option($select_1);
            }else{
                $option = '';
            }
            $str .= <<<EOT
    <label class="layui-form-label">{$v}</label>
    <div class="layui-input-inline">
        <select name="{$linkfield[$k]}" lay-verify="required" lay-filter="{$attr['id']}_{$k}">
            <option value="">请选择</option>
            {$option}
        </select>
    </div>
EOT;

        }

        $dom = <<<EOT
            <div class="layui-form-item" component-name="linkage" id="{$attr['id']}">
                    {$str}
                    <div class="layui-form-mid layui-word-aux">{$attr['helpinfo']}</div>
                </div>
EOT;

        return $dom;
    }


    /**
     * 组件依赖的js 插件
     */
    public static function relyOnJsPlugin($static_path){


    }

    /*
     * 设置组件的 javascript 脚本
     * @return string
     * */
    public static function script($attr){
        $js =<<<EOT
        p(attr.set.id);
;layui.use(['form'], function(){
        var form = layui.form;
        var next_select_option = all_data =[];

        form.on('select('+ attr.set.id +'_0)', function(data){
            p(attr.set.select[1]);
            $.post('/component/linkselect',attr.set.select[1],function(msg){
                p(msg);
             //  var next_option = '';
             //   $.each(msg,function(i,n){
             //       next_option += '<option value="'+n.wid+'"> '+n.name+' </option>';
            //    });
            //    $('select[lay-filter=linkage_2]').find('option').slice(1).remove();
            //    $('select[lay-filter=linkage_2]').append(next_option);
            //    form.render('select');
            });
            
        });
        form.on('select('+ attr.set.id +'_1)', function(data){
            //p(attr.set);
            //$.post('/component/linkselect',{
            //    'where[cid]': data.value,
            //    'group':'wid',
            //    'field':'wid,b.name'
           // },function(msg){
            //    p(msg);
                //var next_option = '';
                //$.each(msg,function(i,n){
                //    next_option += '<option value="'+n.wid+'"> '+n.name+' </option>';
                //});
                //$('select[lay-filter=linkage_3]').find('option').slice(1).remove();
                //$('select[lay-filter=linkage_3]').append(next_option);
                //form.render('select');
            //});

        });
        
    })
EOT;
        return $js;
    }
    /*
     * 处理路由来的请求
     * @return string
     * */
    public static function requset(){
        $db = new \think\Db(); //这里的DB 根据不同的框架 调用对应的 Db 类
        $model = $db::name($_POST['table']);
        p($_POST);
        $allow_method = ['join','group','limit','field'];

        foreach($_POST as $k => $v){
            if(!in_array($k ,$allow_method))continue;
            if($k == 'join'){
                $v = explode(',',$v);
                $model->$k($v[0],$v[1]);
            }else{
                $model->$k($v);
            }


        }
        $res = $model->select();
        p($res);
        die();
//        $post = $_POST['where'];
//        $where = '';
//        $model = $db::name('goods')->alias('a')->join('warehouse b','a.wid = b.id');
//        if(isset($_POST['where'])){
//            $where = _parseWhere($_POST['where']);
//            $model->where($where);
//        }
//
//        if(isset($_POST['group'])){
//            $model->group( $_POST['group']);
//        }
//        if(isset($_POST['field'])){
//            $model->field($_POST['field']);
//        }
//
//        $res = $model->select();
//
//        return json($res); // 此 json函数 来至 tp ,根据不同的框架 调用对应的函数

    }

    

}