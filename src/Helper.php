<?php

function p($var){
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}
/*
 * 快速调用CMaker 创建组件
 * @param string component name
 */
function CMaker($type){
    return \CMaker\Maker::build($type);
}

//模板中调用
function CMakerJs(){
    $js = \CMaker\Maker::createJs();
    return $js;
}