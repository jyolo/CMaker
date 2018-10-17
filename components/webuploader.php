<?php
namespace CMaker\components;
use CMaker\Component;
use think\File;
use think\Image;

/**
 * 上传组件webuploader
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class webuploader extends Component
{

    public static function attr(){
        return [
            'label'=>'图片上传',
            'helpinfo' => '',
            'layVerify' => '',
            'value' => '', //
            'multiple' => false, //多选
            'auto' => false, //自动上传
            'method' => 'post', //请求方法
            'name' => 'thumb', //字段名称
            'uploadtype' => 'image', //[image ,file ]
            'serverUrl' => url('/mvcbuilder/webupload'),
        ];
    }

    //设置dom结构
    public static function dom(){
        $attr = self::$attr;

        $start_upload_botton = ($attr['auto'] == 'on') ? '':'<button type="button" class="layui-btn '.$attr['component_name'].'" id="'.$attr['id'].'_start" >开始上传</button>';

        $value_dom = self::value_preiew();
        //如果value有值的话
        strlen($value_dom) ? $display = 'style="display:block"': $display = '';

        switch($attr['uploadtype']){
            case 'image':
                $typedom = <<<EOT
                 <!---图片预览 demo--->
               <div class="layui-input-block img_preiew" {$display}> 
                {$value_dom}  
               </div>
EOT;
                break;
            case 'file':
                $typedom = <<<EOT
                 <!---文件预览 demo--->
               <div class="layui-input-block file_preiew" {$display}>
                   <table class="layui-table">
                       <tr class="file_list_deme">
                           <th>文件名</th><th>文件大小</th><th>状态</th><th>操作</th>
                       </tr>
                       {$value_dom}
                   </table>
               </div>
EOT;
                break;
        }

        $dom = <<<EOT
        <style>

               .img_preiew{margin-top: 10px;display: none;}
               .pic_list_demo{float:left;padding: 2px;margin-right: 5px;background: #ffffff;height: 100px;display: block;}
               .upinfo{position:relative;top:-27px;;width: 100%;opacity:0.8;filter:Alpha(opacity=50); display:block}
               .upinfo .layui-progress{height: 5px;}

               .preiew_item{float:left;padding: 2px;margin-right: 5px;background: #ffffff;display: block;height: 100px;width: 100px;}
               .preiew_item img{width:100px; height:100px}
               .del_up_pic{position: absolute;top:-3.9px;margin-left:88px;cursor:pointer;}

               .file_preiew{display: none;}
               .file_upinfo{text-align: center}
               
           </style>
           <div class="layui-form-item" component-name="{$attr['component_name']}">
               <label class="layui-form-label">{$attr['label']}</label>
               <div class="layui-input-block">
                   <button type="button" class="layui-btn {$attr['component_name']}" id="{$attr['id']}" style="padding: 0 0;width: 100px;"></button>
                   {$start_upload_botton}
                   <div class="layui-word-aux">{$attr['helpinfo']}</div>
               </div>
                {$typedom}
           </div>

   
EOT;

        return $dom;
    }

    /**
     * 组件依赖的js 插件
     */
    public static function relyOnJsPlugin($static_path){
        return [
            'css' => [
                [
                    'path' => "{$static_path}plugin/webuploader/webuploader.css",
                    'attr' => ['media' => 'all']
                ]
            ],
            'js' => [
                [
                    'path' => "https://cdnjs.cloudflare.com/ajax/libs/jquery/3.0.0/jquery.min.js"
                ],
                [
                    'path' => "{$static_path}plugin/webuploader/webuploader.js"
                ]
            ]
        ];
    }

    /**
     * 设置组件的 javascript 脚本
     * @return string
     * */
    public static function script($attr){

        $js = <<<EOT
        //解决删除图片后 不在传值的问题。
    $('.del_up_pic').unbind('click').bind('click',function(){             
        if($(this).parents('.img_preiew').find('.preiew_item').length-1 == 0){
            $(this).parents('.img_preiew').css('display','none');
        }
        var input = $(this).parent().find('input[type=hidden]').clone();
        input.attr('value','');
        $(this).parent().after(input);
        $(this).parent().remove();
    });  
    var set = attr.set;
    
    set.multiple = (set.multiple == 'on') ? true :false;
    set.auto = (set.auto == 'on') ? true : false;
    var uploader = WebUploader.create({
        // swf文件路径
        swf: '__PLUGIN_PATH__/webuploader/Uploader.swf',
        // 文件接收服务端。
        server: set.serverUrl,
        // 内部根据当前运行是创建，可能是input元素，也可能是flash.
        pick: {
            id:'#'+set.id,
            label:set.label,
            innerHTML:'<i class="layui-icon">&#xe67c;</i>选择文件',
            multiple:set.multiple,
        },
        formData:{action:set.uploadtype},
        fileVal:set.name, //置文件上传域的name。
        method:set.method,
        // 不压缩image, 默认如果是jpeg，文件上传前会压缩一把再上传！
        resize: false,
        auto:set.auto,
    });
    
    //执行上传按钮
    $('#'+set.id+'_start').click(function(){
        if(uploader.getFiles().length == 0){
            layer.msg('请先添加文件');
        }else{
            uploader.upload();
        }
    })


    //文件加入上传队列
    uploader.on('filesQueued',function(files_arr){
        // 优化retina, 在retina下这个值是2
        ratio = window.devicePixelRatio || 1,
        // 缩略图大小
        thumbnailWidth = 110 * ratio;
        thumbnailHeight = 110 * ratio;

        
        switch(set.uploadtype){
            case 'image':
               
                $.each(files_arr ,function(i,n){
                    //如果是单选，删除旧的文件队列以及预览
                    if(set.multiple == false){
                        if($('#'+set.id).parent().siblings('.img_preiew').find('.preiew_item').length > 0){
                            var files = uploader.getFiles();
                            //上传文件队列中删除不是当前的文件
                            $.each(files,function(si,sn){
                                if(n != sn){uploader.removeFile( sn );}
                            })
                            $('#'+set.id).parent().siblings('.img_preiew').find('.preiew_item').eq(0).remove();
                        }
                    }
                    
                    //创建预览缩略图
                    uploader.makeThumb( n , function( error, src ) {
                        if (error) {console.log('文件不是图片 无法预览');return;}
                        var preiew_box = $('#'+set.id).parent().siblings('.img_preiew');
                        var struct = '<div class="">' +
                                            '<i class="layui-icon del_up_pic" >&#x1006;</i><input type="hidden" name="" value="">' +
                                            '<img src="/static/image/nopic.png" />' +
                                            '<div class="upinfo">' +
                                                '<div class="layui-progress" >' +
                                                        '<div class="layui-progress-bar" lay-percent="0%"></div>' +
                                                '</div>' +
                                                '<span class="layui-btn layui-btn-xs" style="width: 100%;color: #FFFFFF;"></span>' +
                                            '</div>'+  
                                    '</div>';
                        var copy = $(struct);
                        
                        copy.find('.upinfo').find('.layui-btn').html('等待上传');
                        copy.attr('id',n.id).addClass('preiew_item').find('img').attr('src',src);
                        
                        copy.find('.del_up_pic').bind('click',function(){
                            
                            if($(this).parents('.img_preiew').find('.preiew_item').length-1 == 0){
                                $(this).parents('.img_preiew').css('display','none');
                            }
                            $(this).parent().remove();
                            //删除文件队列
                            uploader.removeFile( n );
                        });
                        preiew_box.css('display','block');
                        copy.css('display','block');
                        preiew_box.append(copy);

                    }, thumbnailWidth, thumbnailHeight );
                });

                break;
            case 'file':
                $.each(files_arr,function (i,n) {

                    $('.file_preiew').css('display','block');
                    
                    //如果是单选，删除旧的文件队列以及预览
                    if(set.multiple == false){
                      
                        if($('#'+set.id).parent().siblings('.file_preiew').find('tr').length > 1){
                            var files = uploader.getFiles();
                            
                            //上传文件队列中删除不是当前的文件
                            $.each(files,function(si,sn){
                               $('#'+set.id).parent().siblings('.file_preiew').find('.already_upload').remove();
                                if(n != sn){
                                    $('#'+set.id).parent().siblings('.file_preiew').find('#'+sn.id).remove();
                                    uploader.removeFile( sn );
                                }
                                
                            })
                            
                        }
                    }

                    var preiew = '<tr id="'+n.id+'">' +
                            '<td>'+n.name+'</td>' +
                            '<td>'+WebUploader.formatSize( n.size )+'</td>' +
                            '<td class="file_upinfo"><div class="info">等待上传</div><div class="layui-progress" ><div class="layui-progress-bar" lay-percent="0%"></div></div></td>' +
                            '<td><span class="layui-btn del_up_file">删除</span></td>' +
                        '</tr>';

                    $('.file_preiew').find('.layui-table').append(preiew);

                    $('.del_up_file').bind('click',function(){
                        if($(this).parents('tr').siblings().length - 1 == 0){
                            $(this).parents('.file_preiew').css('display','none');
                        }
                        $(this).parents('tr').remove();
                        uploader.removeFile( n );
                    });

                })
                break;
        }



    });
    //文件上传进度
    uploader.on('uploadProgress',function(file,number){

        var precent = Math.round( number * 100 )+"%";
        switch (set.uploadtype){
            case 'image':
                $('#'+file.id).find('.upinfo').css('display','block').find('.layui-progress-bar').attr('lay-percent',precent);
                $('#'+file.id).find('.upinfo').css('display','block').find('.layui-progress-bar').css('width',precent);
                $('#'+file.id).find('.upinfo').find('.layui-btn').html('上传中');
                break;
            case 'file':
                $('#'+file.id).find('.layui-progress-bar').attr('lay-percent',precent);
                $('#'+file.id).find('.layui-progress-bar').css('width',precent);
                $('#'+file.id).find('.info').html('上传中');
                break;
        }

    });
    //上传错误
    uploader.on('uploadError',function(file,reason){
        switch (set.uploadtype){
            case 'image':

                break;
            case 'file':

                break;
        }

    });
    //上传成功
    uploader.on('uploadSuccess',function(file,response){
        if(set.multiple == true){
            var field_name = set.name+'[]';
        }else{
            var field_name = set.name;
        }
        switch (set.uploadtype){
            case 'image':
                if(response.code == 0){
                    $('#'+file.id).find('.upinfo').css('display','block').find('.layui-btn').css('background-color','#e64a1b').html(response.msg);
                    return ;
                }
                $('#'+file.id).find('.upinfo').css('display','block').find('.layui-btn').html(response.msg);
                
                $('#'+file.id).find('input[type=hidden]').attr('name',field_name);
                $('#'+file.id).find('input[type=hidden]').attr('value',response.data.url);
                break;
            case 'file':
                if(response.code == 0){
                    $('#'+file.id).find('.info').html(response.msg);
                }else{
                    $('#'+file.id).find('.info').html(response.msg);
                    $('#'+file.id).append('<input type="hidden" name="'+field_name+'" value="'+response.data.url+'">');
                }
                break;
        }


    });

EOT;
        return $js;
    }

    /**
     * value有值的时候 组装dom
     * @return string
     */
    private static function value_preiew(){
        $attr = self::$attr;

        if(!strlen(self::$attr['value'])) return '';

        if($attr['multiple'] == 'on' || $attr['multiple'] == 1){
            $attr['name'] = $attr['name'].'[]';
        }
        $str = '';
        $arr = explode(',',self::$attr['value']);

        switch ($attr['uploadtype']){
            case 'image':
                foreach($arr as $k => $v){
                    $str .= <<<EOT
<div class="preiew_item already_upload"  style="display: block;">
    <i class="layui-icon del_up_pic">ဆ</i>
    <input type="hidden" name="{$attr['name']}" value="{$v}">
    <img src="{$v}">
    <div class="upinfo" style="display: block;">
        <div class="layui-progress">
            <div class="layui-progress-bar" lay-percent="100%" style="width: 100%;"></div>
        </div>
        <span class="layui-btn layui-btn-xs" style="width: 100%;color: #FFFFFF;">已上传</span>
    </div>
</div>
EOT;

                }
                break;
            case 'file':

                foreach($arr as $k => $v){
                    //文件不存在  跳过
                    if(!file_exists(ROOT_PATH.$v))continue;

                    $File = new File(ROOT_PATH.$v);
                    $size = self::sizecount($File->getSize());
                    $str .=<<<EOT
<tr class="already_upload">
    <td>{$File->getBasename()} </td>
    <td>{$size}</td>
    <td class="file_upinfo">
        <div class="info">已上传</div>
        <div class="layui-progress">
            <div class="layui-progress-bar" lay-percent="100%" style="width: 100%;"></div>
        </div>
    </td>
    <td>
        <span class="layui-btn del_up_file">删除</span>
    </td>
    <input type="hidden" name="{$attr['name']}" value="{$v}"></tr>
EOT;

                    //释放对象
                    unset($File);
                }
                break;
        }

        return $str;
    }

    /**
     * 文件大小转化 固定单位
     * @param $filesize
     * @return string
     */
    private static function sizecount($filesize) {
        if($filesize >= 1073741824) {
            $filesize = round($filesize / 1073741824 * 100) / 100 . ' G';
        } elseif($filesize >= 1048576) {
            $filesize = round($filesize / 1048576 * 100) / 100 . ' M';
        } elseif($filesize >= 1024) {
            $filesize = round($filesize / 1024 * 100) / 100 . ' K';
        } else {
            $filesize = $filesize . ' bytes';
        }
        return $filesize;
    }
}