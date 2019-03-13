<?php

namespace app\admin\controller\home;

use app\common\controller\Backend;
use app\home\model\user as users;
use app\home\model\business as company;
use app\admin\model\business as companys;
/**
 * 
 *
 * @icon fa fa-circle-o
 */
class Business extends Backend
{
    
    /**
     * Business模型对象
     * @var \app\admin\model\Business
     */
    protected $model = null;

    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\admin\model\Business;
        
        
    }
    
    /**
     * 默认生成的控制器所继承的父类中有index/add/edit/del/multi五个基础方法、destroy/restore/recyclebin三个回收站方法
     * 因此在当前控制器中可不用编写增删改查的代码,除非需要自己控制这部分逻辑
     * 需要将application/admin/library/traits/Backend.php中对应的方法复制到当前控制器,然后进行修改
     */
    /**
     * 查看
     */
    public function index()
    {
        // $this->re
        //设置过滤方法
        $this->request->filter(['strip_tags']);
        if ($this->request->isAjax()) {
            //如果发送的来源是Selectpage，则转发到Selectpage
            $this->relationSearch = true;
            if ($this->request->request('keyField')) {
                return $this->selectpage();
            }
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            $total = $this->model
                ->with(['user'])
                ->where($where)
                ->order($sort, $order)
                ->count();

            $list = $this->model
                ->where($where)
                ->with(['user'])
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select(); 
            $list = collection($list)->toArray();
            $result = array("total" => $total, "rows" => $list);

            return json($result);
        }
        return $this->view->fetch();
    } 

    /**
     * 企业认证审核
     */
    public function business_trial(){
        $companys = new companys();
        $data['trial'] = $this->request->get('trial',0);
        $data['id'] = $this->request->get('id',0);
        $data['user_id'] = $this->request->get('user_id',0);
        return $companys->business_trial($data);
    }

}
