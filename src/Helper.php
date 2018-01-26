<?php

function p($var){
    echo '<pre>';
    var_dump($var);
    echo '</pre>';
}

function CMaker($type,$show = 'ShowAll'){
    return \CMaker\Maker::build($type);
}



//模板中调用
function CMakerJs(){
    $js = \CMaker\Maker::createJs();
    return $js;
}