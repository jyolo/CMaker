<?php
namespace CMaker\components;
use CMaker\Component;
use CMaker\Maker;
use think\Db;
use think\Exception;
use Tree\Tree;

/**
 * 关联选择组件
 */
class relation extends Component
{


    /**
     * 设置组件的属性以及初始默认值
     * @return array
     */
    public static function attr(){
        return [
            'label' => '关联选择',
            'helpinfo' => '关联选择，展现形式支持 select,radio,checkbox,treeSelect',
            'showtype' => 'select', //默认的显示方式
            'choose' => '',//默认选中的
            'name' => 'aid',// 表单的name值
            'layVerify' => '',
            //Db类的使用的属性
            'table' => '', //数据表
            'field' => 'id,title' , // 作为值的字段 默认第一个作为表单提交的value 第二个作为显示
            'where' => '',
            'limit' => '',
            'group' => '',

        ];

    }

    /*
     * 设置dom结构
     * @return string
     * */
    public static function dom(){

        if(!in_array(self::$attr['showtype'],['select','checkbox','radio','treeSelect']))
            return 'ralation组件showtype 仅支持select,checkbox,radio,treeSelect 这四种显示方式';


        try{

            if(self::$attr['showtype'] == 'treeSelect'){

                $data = self::get_tree_array(self::$attr);

                self::$attr['showtype'] = 'select'; //无线层级 树形结构 默认展现形式是 select

            }else{

                //获取数据
                $data = self::get_models_data();

            }
        }catch (\Exception $e){
            throw new \Exception($e->getMessage());

        }


        $dom = Maker::build(self::$attr['showtype'])
            ->label(self::$attr['label'])
            ->helpinfo(self::$attr['helpinfo'])
            ->option($data)
            ->name(self::$attr['name'])
            ->choose(self::$attr['choose'])
            ->render();



        $dom = preg_replace('/component-name=\"(.*?)\"/' ,'component-name="relation"',$dom);
        return $dom;
    }

    /**
     * 获取数据
     * @return array
     */
    private static function get_models_data(){
        $attr = self::$attr;
        //表不存在返回空数组
        $sql = 'show tables like \''.config('database.prefix').$attr['table'].'\' ';
        $flag = Db::query($sql);
        if(!count($flag))return [];

<<<<<<< HEAD
        $data = Db::name($attr['table'])
            ->where($attr['where'])
            ->field($attr['field'])
            ->limit($attr['limit'])
            ->group($attr['group'])
            ->select();
=======
        $Db = Db::name($attr['table']);

        if(strlen($attr['where'])) $Db->where($attr['where']);
        if(strlen($attr['field'])) $Db->field($attr['field']);
        if(strlen($attr['limit'])) $Db->limit($attr['limit']);
        if(strlen($attr['group'])) $Db->group($attr['group']);

        $data = $Db->select();
>>>>>>> master

        $return = [];
        $arr = explode(',',$attr['field']);
        //变成一维数组
        foreach($data as $k => $v){
            $return[$v[$arr[0]]] = $v[$arr[1]];
        }
        return $return;
    }


}