<?php
namespace CMaker\components;
use CMaker\Component;


/**
 * 幻灯片组件
 * User: jyolo
 * Date: 2017/7/6
 * Time: 17:08
 */
class carousel extends Component
{

    /**
     * 设置组件的属性以及初始默认值
     * @return array
     */
    public static function attr(){
        return [
            'width' => '100%',
            'height' => '600px',
            'arrow' => 'always',
            'anim' => 'default',
            'autoplay' => true,
        ];

    }

    /*
     * 设置dom结构
     * @return string
     * */
    public static function dom(){
        $dom_id = self::$id;
        $dom = <<<EOT
            <div class="layui-carousel" id="{$dom_id}">
              <div carousel-item>
                <div>条目1</div>
                <div>条目2</div>
                <div>条目3</div>
                <div>条目4</div>
                <div>条目5</div>
              </div>
            </div>
EOT;

        return $dom;
    }


    /**
     * 组件依赖的js 插件
     */
    public static function relyOnJsPlugin($static_path){

        $arr = [
//            'css' => [
//                [
//                    'path' => "{$static_path}plugin/layui/css/layui.css",
//                    'attr' => ['media' => 'all']
//                ]
//            ],
//            'js' => [
//
//                [
//                    'path' => "{$static_path}plugin/layui/layui.js"
//                ],
//                [
//                    'path' => "{$static_path}plugin/lay-extend-module/config.js"
//                ]
//            ]
        ];


        return $arr;
    }

    /*
     * 设置组件的 javascript 脚本
     * @return string
     * */
    public static function script($attr){


        $srcript =<<<EOT
\n;layui.use('carousel', function(){
  var carousel = layui.carousel;
  //建造实例
  carousel.render({
    elem: '#{$attr['uniqid_id']}'
    ,width: '{$attr['set']['width']}' //设置容器宽度
    ,arrow: '{$attr['set']['arrow']}' //始终显示箭头
    ,anim: '{$attr['set']['anim']}' //切换动画方式
  });
});\r\n
EOT;
        return $srcript;
    }

    

}