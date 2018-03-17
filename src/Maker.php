<?php
/**
 *  组件生成器 生成器
 *  切换生成方案只需要定义 templatePlan => '方案名称' 即可
 */

namespace CMaker;

use think\facade\Session;


class Maker
{
    public static $set = [];  //所有设置的key value
    public static $uniqid_id = ''; //组件的唯一id
    public static $components = [];    //记录当前页面调用过的组件容器 ，组件名称，组件id，组件的设置
    public static $instance = null;       //单例
    public static $templatePlan = 'components'; //模板方案
    public static $static_path = '/static/';
    public static $run_time = 0;

    /**
     * 初始化
     * @param $type
     * @return FormMaker|null
     */
    public static function build($component_name){
        if(is_null(self::$instance)){
            self::$instance = new self;
        }

        self::$set['component_name'] = $component_name;

        //组件的唯一id
        self::$uniqid_id = $component_name.'_'.self::$run_time;

        //当前页面的调用的次数
        self::$run_time++;

        return self::$instance;
    }


    /**
     * 渲染返回
     * @return string
     * @throws Exception
     */
    public function render(){

        //获取组件的class
        $class = self::getClass(self::$set['component_name']);

        //获取组件的属性
        $component_attr = $class::attr();
        //外部调用设定代替默认值
        //赋值一个id的属性
        self::$set['id'] = self::$uniqid_id;
        $class::$attr = array_merge($component_attr ,self::$set);


        //记录当前页面使用了哪些组件
        array_push(self::$components ,[
            'component_name' => self::$set['component_name'] ,
            'uniqid_id' => self::$uniqid_id,
            'set' => $class::$attr
        ]);

        $dom = $class::dom();

        //销毁静态变量 保留了 self::$components 用于 调用组件的js
        self::$set = $class::$attr = $class::$id = null;

        return $dom;
    }

    /**
     * 创建js
     */
    public static function createJs($type = 'all'){
        $script = self::getComponentScript($type);
        return $script;
    }



    /**
     * 获取组件class
     * $param called 组件的名称
     * $param buildplan = 组件路径/方案
     * @return string namespace
     */
    public static function getClass($called ){
        $arr = explode('\\',__CLASS__);
        $buildPlan = self::$templatePlan;

        array_pop($arr);
        array_push($arr ,$buildPlan ,$called);

        $class = trim(join('\\',$arr));

        if(!class_exists($class)) throw new \ErrorException($class.' 组件不存在');
        return $class;
    }


    /**
     * 按需加载组建需要用的插件
     * $showtype  'all' 显示插件的应用 ,'plugin' 只显示插件的引用  ,'script' 只显示 js脚本
     * @return bool|string
     */
    private static function getComponentScript($showtype){

        //检查容器如果当前页面一个组件都没有调用 ，直接返回
        if(!count(self::$components)) return ;

        //获取组建依赖的插件
        $JsPlugin = self::getRelyOnJsPlugin()."\r\n";

        //获取组件的js 脚本
        $start = '<script>'."\r\n";

        $script = '';

        foreach(self::$components as $k => $v){
            //如果是编辑的内容 则转义一下 否则json.parse的时候会报错
            if($v['component_name'] == 'ueditor'){
                $v['set']['value'] = htmlentities($v['set']['value']);
            }
            $class = self::getClass($v['component_name']);
            //初始化组件的 唯一id
            //系统会生成 组件名称的全局函数 以及 全局的设置的对象
            //示例
            // var datarange_`uniqid_id`_attr = 设置的对象
            // var daterange_`uniqid_id` = function(attr){}
            //如果需要重新渲染组件 获取组件的原始uniqid_id [第一次渲染的uniqid_id] ，并修改对应的setting 执行全局函数即可 daterange_`uniqid_id`(new_setting)
            $component_script = $class::script($v);

            if(strlen($component_script )){

                //$json = json_encode($v);
                //将有js的组件的设置 存入 cookie变量中
                $__component_set[$v['uniqid_id']] =  $v;

                $script .= 'var '.$v['uniqid_id'].'_attr = component_set.'.$v['uniqid_id'].' ; '."\r\n\r\n";
                $script .= 'var '.$v['uniqid_id'].' = function (attr){'."\r\n";
                $script .= $component_script;
                $script .= "\r\n\r\n".'};'."\r\n\r\n";
                $script .= ''.$v['uniqid_id'].'('.$v['uniqid_id'].'_attr)'.";\r\n\r\n";
            }
        }

        $end = "\r\n</script>\r\n";


        //所有的组件设置均存入cookie
        if(isset($__component_set) && count($__component_set)){

            $frist_line = 'var component_set = JSON.parse(\''.json_encode($__component_set ,true).'\');'."\r\n\n".''."\r\n\n";
        }else{
            $frist_line = '';
        }


        //如果脚本为空则等于空
        if(strlen($script)){
            $script = $start.$frist_line.$script.$end;
        }

        //销毁当前页面组件调用的记录
        self::$components = [];

        switch ($showtype){
            case 'all':
                return $JsPlugin.$script;
                break;
            case 'plugin':
                return $JsPlugin;
                break;
            case 'script':
                return $script;
                break;
            default:
                return;
                break;
        }




    }
    /**
     * 获取组件的依赖的js 插件 并 去掉重复的 引用
     */
    private static function getRelyOnJsPlugin(){

        $component = $arr = [];
        foreach(self::$components as $k => $v){
            $component[$k] = $v['component_name'];
        }
        $component_unique = array_unique($component);


        foreach($component_unique as $k => $v){
            $class = self::getClass($v);

            if($class::relyOnJsPlugin(self::$static_path)){

                //初始化组件的 唯一id
                $arr[] = $class::relyOnJsPlugin(self::$static_path);
            }

        }



        $box  = [];
        //组件css 和 js 的引用
        foreach($arr as $k => $v){

            if(!is_array($v))throw new \ErrorException($class.' relyOnJsPlugin 方法 返回值需要是数组');

            foreach($v as $sk => $sv){
                switch($sk){
                    case 'css':
                        $tag = ['link','href'];
                        break;
                    case 'js':

                        $tag = ['script','src'];
                        break;
                }
                array_walk($sv,function($a)use(&$str, $tag,$sk,&$box){
                    $str ='<'.$tag[0].' ';
                    if($sk == 'css'){
                        $a['attr']['rel'] = isset($a['attr']['rel']) ? $a['attr']['rel'] : 'stylesheet';
                    }
                    if($sk == 'js'){
                        $a['attr']['type'] = isset($a['attr']['type']) ? $a['attr']['type'] : 'text/javascript';
                    }
                    foreach($a as $K => $V){
                        if($K == 'path'){
                            $str .= ' '.$tag[1].'="'.$V.'" ';
                        }else{
                            foreach($V as $kk => $vv){
                                $str .= $kk .'="'.$vv.'" ';
                            }
                        }
                    }
                    if($sk == 'css'){
                        $str .= ' />';
                    }
                    if($sk == 'js'){
                        $str .= '></'.$tag[0].'>';
                    }
                    $box[] = $str;
                });
            }
        }

        //去掉重复的 引用
        $box = array_unique($box);
        //var_dump($box);
        return join("\r\n",$box);


    }

    /**
     * 为组建设定的属性赋值
     * @param $name
     * @param $arguments
     * @return $this
     */
    public function __call($name, $arguments)
    {
        $class = self::getClass(self::$set['component_name'],self::$templatePlan);

        if(!in_array($name,array_keys($class::attr())))throw new \ErrorException(self::$set['component_name'].'组件 attr 方法中 没有定义该"'.$name.'"属性');

        //特殊的relation 组件 where 可以向 tp 中连续使用 where
        if(self::$set['component_name'] == 'relation' && $name == 'where'){

            if(!isset(self::$set[$name]))self::$set[$name] = [];
            //压入数组
            if(count($arguments))array_push(self::$set[$name],$arguments);

        }else{
            self::$set[$name] = isset($arguments[0]) ? $arguments[0] :'';
        }


        return $this;
    }
}

