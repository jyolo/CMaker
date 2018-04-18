<?php



function CMaker($ComponentName){
    $cmaker =  \CMaker\Maker::build($ComponentName);
    //默认拓展组件的根 命名空间 为 \component\someComponent
    // 可根据自定义
    $cmaker->setExtendsRootNamespace('component');
    return $cmaker;
}



//模板中调用
function CMakerJs(){
    $js = \CMaker\Maker::createJs();
    return $js;
}