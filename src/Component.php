<?php
/**
 *  可自定义表单生成的方案的 生成器
 *  切换生成方案只需要在配置文件中定义 form_dom_plan => '方案名称' 即可
 *
 */

namespace CMaker;

interface Transport{

    /*
     * 设置默认的组件属性信息
     * @return array
     * */
    public static function attr();
    /*
     * 设置组件的html dom
     * @return array
     * */
    public static function dom();

    /**
     * 设置组件依赖的js插件
     * @param $static_path
     * @return mixed
     */
    public static function relyOnJsPlugin($static_path);

    /**
     * 设置组件调用的js
     * @param $attr
     * @return mixed
     */
    public static function script($attr);
}

/**
 * 组件的父类 组件的公共类
 * Class component
 * @package FormMaker
 */
abstract class Component implements Transport
{
    //组件的attr属性
    public static $attr = [];
    //组件的唯一id
    public static $id = null;

    /**
     * 全局的组件所需依赖的 js|css 文件
     * @param $static_path
     * @return array|mixed
     */
    public static function relyOnJsPlugin($static_path){
        return [
            'css' => [
                [
                    'path' => "{$static_path}plugin/layui/css/layui.css",
                    'attr' => ['media' => 'all']
                ]
            ],
            'js' => [
                ['path' => "{$static_path}plugin/layui/layui.js"],
                ['path' => "{$static_path}plugin/lay-extend-module/config.js"],
            ]
        ];
    }

    /**
     * 全局的组件 javascript 脚本 默认值
     * @param $attr
     * @return mixed|void
     */
    public static function script($attr){return ;}
}

