<?php
require_once '../vendor/autoload.php';


?>




<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CMaker example</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Title</title>
    <link rel="stylesheet" href="./static/plugin/layui/css/layui.css">
</head>
<body>

<blockquote class="layui-elem-quote layui-text">
    表单的示例
</blockquote>

<fieldset class="layui-elem-field layui-field-title" style="margin-top: 20px;">
    <legend>表单集合演示</legend>
</fieldset>

<form class="layui-form" action="">

<?php
        echo CMaker("text")->render();
        echo CMaker("select")->option('aa|bb')->render();
        echo CMaker("radio")->option('aa|bb')->render();
        echo CMaker("switchs")->text('Y|N')->render();
        echo CMaker("checkbox")->option('aa|bb')->render();

        echo CMaker("table")->filter("article")->cols([
            ['type'=>'checkbox'] ,
            ['field' => "id",'title' => 'id','sort' => true ],
            ['field' => 'title','title' => '标题','sort' => true ] ,
            ['field' => 'istuijian','title' => '是否推荐','sort' => true ] ,
            ['field' => 'istop','title' => '是否置顶','sort' => true ] ,
            ['field' => 'pinglun','title' => '开启评论','sort' => true ] ,
            ['field' => 'tags','title' => '标签','sort' => true ] ,
            ['toolbar' => '#actionTpl' ,'title' => '操作','fixed' => 'right']
        ])->page(true)->limit(10)->url('http://local.tp51.com/admin.php/blog/article/index.html')->render();
?>

</form>


<script type="text/javascript" src="./static/plugin/layui/layui.js"></script>
<script>
    layui.use(['form', 'layedit', 'laydate'], function(){
        var form = layui.form;
        form.render();
    })
</script>

<?php
echo CMakerJs();
?>
</body>
</html>