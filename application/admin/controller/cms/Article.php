<?php

namespace app\admin\controller\cms;

use app\common\controller\Backend;

/**
 * CMS文章管理
 *
 * @icon fa fa-circle-o
 */
class Article extends Backend
{
    
    /**
     * Article模型对象
     * @var \app\admin\model\Article
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Article;
        $this->class_model = new \app\admin\model\Articleclass;
        $list = $this->class_model->where([])->select();
        $list = $this->subtree($list,0,0);
        $this->view->assign('list',$list);
    }

    //递归分类
    function subtree($arr,$id=0,$lev=0) {
        static $subs = array(); //子孙数组
        foreach ($arr as $v) {
            if ($v['pid'] == $id) {
                $v['lev'] = $lev;
                $v['levs'] = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;',$lev);
                $subs[] = $v; 
                $this->subtree($arr,$v['class_id'],$lev+1);
            }
        }
        return $subs;
    }
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    

}
