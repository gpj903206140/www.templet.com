<?php

namespace app\home\controller;

use app\common\controller\Frontend;
use think\Config;
use think\Cookie;
use think\Hook;
use think\Session;
use think\Validate;
use think\Db;

use app\home\model\User as users;
use app\home\model\Business;
use app\common\model\Area;
use app\home\model\Release;

/**
 * 会员中心
 */
class User extends Frontend
{

    protected $layout = 'default';
    protected $noNeedLogin = ['login', 'register', 'third'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 空的请求
     * @param $name
     * @return mixed
     */
    public function _empty($name)
    {
        $data = Hook::listen("user_request_empty", $name);
        foreach ($data as $index => $datum) {
            $this->view->assign($datum);
        }
        return $this->view->fetch('user/' . $name);
    }
    
    /**
     * 获取城市或地区
     */
    public function get_city(){
        $pid = request()->post('pid',0);
        $area_model = new Area();
        $city = $area_model->getArea($pid);
        return json_encode($city);
    }
    /**
     * 个人主页
     */
    public function user_home()
    {
        $user_id = request()->get('user_id',0);
        if(empty($user_id)){
            $uid = $this->uid;
        }else{
            $uid = $user_id;
        }
        $user = new users();
        //获取个人信息
        $user_info = $user->where(['id'=>$uid])->find();
        //家乡
        if(!empty($user_info['hometown'])){
            $user_info['hometown'] = json_decode($user_info['hometown'],true);
        }
        
        //现居
        if(!empty($user_info['nhom'])){
            $user_info['nhom'] = json_decode($user_info['nhom'],true);
        }
        
        //领域
        if(!empty($user_info['domain'])){
            $user_info['domain'] = json_decode($user_info['domain'],true);
            $user_info['domain_count'] = count($user_info['domain']);
        }

        //获取企业信息
        $business = new Business();
        $user_info['business'] = $business->field('id,name')->where(['id'=>$user_info['business_id']])->find();
        $this->view->assign('user_id',$user_id);
        $this->view->assign('user_info',$user_info);
        return $this->view->fetch();
    }

    /**
     * 个人信息
     */
    public function user_info()
    {
        //获取个人信息
        $user = new users();
        $user_id = request()->get('user_id',0);
        if(empty($user_id)){
            $uid = $this->uid;
        }else{
            $uid = $user_id;
        }
        //获取个人信息
        $user_info = $user->where(['id'=>$uid])->find();
        //家乡
        if(!empty($user_info['hometown'])){
            $user_info['hometown'] = json_decode($user_info['hometown'],true);
        }
        
        //现居
        if(!empty($user_info['nhom'])){
            $user_info['nhom'] = json_decode($user_info['nhom'],true);
        }
        
        //领域
        if(!empty($user_info['domain'])){
            $user_info['domain'] = json_decode($user_info['domain'],true);
            $user_info['domain_count'] = count($user_info['domain']);
        }

        //获取企业信息
        $business = new Business();
        $user_info['business'] = $business->field('id,name')->where(['id'=>$user_info['business_id']])->find();
        $this->view->assign('user_id',$user_id);
        $this->view->assign('user_info',$user_info);
        return $this->view->fetch();
    }

    /**
     * 资料编辑
     */
    public function data_editor()
    {
        $user = new users();
        $uid = $this->uid;
        if(request()->isAjax()){//print_r($_POST);exit;
            $data['username'] = request()->post('username','');
            $data['vocation'] = request()->post('vocation','');
            //$data['hometown'] = request()->post('hometown');
            $hometown = json_encode($_POST['hometown']);
            $data['hometown'] = $hometown;
            $data['gender'] = request()->post('gender','');
            //$data['nhom'] = request()->post('nhom','');
            $nhom = json_encode($_POST['nhom']);
            $data['nhom'] = $nhom;
            //$data['domain'] = request()->post('domain','');
            $domain = json_encode($_POST['domain']);
            $data['bio'] = request()->post('bio','');
            $data['domain'] = $domain;
            $data['phone'] = request()->post('phone','');
            $data['mobile'] = request()->post('mobile','');
            $data['email'] = request()->post('email','');
            $data['qq'] = request()->post('qq','');
            $data['wechat'] = request()->post('wechat','');
            $data['updatetime'] = time();
            
            $result = $user->user_update($data,$uid);
            return $result;
        }else{
            $area_model = new Area();
            //获取个人信息
            $user_info = $user->where(['id'=>$uid])->find();
            //家乡
            $user_info['area1'] = array();
            $user_info['city1'] = array();
            if(!empty($user_info['hometown'])){
                $user_info['hometown'] = json_decode($user_info['hometown'],true);
                $user_info['area1'] = $area_model->getArea($user_info['hometown']['province1']);
                $user_info['city1'] = $area_model->getArea($user_info['hometown']['area1']);
            }
            
            //现居
            $user_info['area2'] = array();
            $user_info['city2'] = array();
            if(!empty($user_info['nhom'])){
                $user_info['nhom'] = json_decode($user_info['nhom'],true);
                $user_info['area2'] = $area_model->getArea($user_info['nhom']['province2']);
                $user_info['city2'] = $area_model->getArea($user_info['nhom']['area2']);
            }
            
            //领域
            if(!empty($user_info['domain'])){
                $user_info['domain'] = json_decode($user_info['domain'],true);
                $user_info['domain_count'] = count($user_info['domain']);
            }
            $this->view->assign('user_info',$user_info);
            //获取城市信息
            $area = $area_model->getArea();
            $this->view->assign('area',$area);
            return $this->view->fetch();
        }
        
    }

    /**
     * 修改头像
     */
    public function avatar_editor(){
        $user = new users();
        $uid = $this->uid;
        if(request()->isAjax()){
            $res = $user->avatar_editor($uid);
            return json_encode($res);
        }else{
            //获取头像
            $user_info = $user->field('id,avatar')->where(['id'=>$this->uid])->find();
            $this->view->assign('user_info',$user_info);
            return $this->view->fetch();
        }
        
    }

    /**
     * 个人主页-发布的信息
     */
    public function user_release(){
        
        $uid = $this->uid;
        if(request()->isAjax()){
            
        }else{
            $user = new users();
            $business = new business();
            $release = new Release();
            //获取个人信息
            $user_info = $user->where(['id'=>$uid])->find();
            
            //领域
            if(!empty($user_info['domain'])){
                $user_info['domain'] = json_decode($user_info['domain'],true);
                $user_info['domain_count'] = count($user_info['domain']);
            }

            //获取企业信息
            $business = new Business();
            $user_info['business'] = $business->field('id,name')->where(['id'=>$user_info['business_id']])->find();

            $this->view->assign('user_info',$user_info);
            //获取发布信息
            $release_type = request()->get('release_type',0);
            $where['user_id'] = $uid;
            $where['part'] = $release_type;
            //获取我的盒子
            $box = $release->get_release(['user_id'=>$uid,'part'=>9],1,'id,name');
            $more_box = array(); //盒子
            $release_info = $release->get_release($where,1);
            foreach($release_info as $k=>$v){
                $release_info[$k]['user'] = $user->field('username,avatar')->where(['id'=>$v['user_id']])->find();
                $release_info[$k]['business'] = $business->field('name')->where(['id'=>$v['business_id']])->find();

                //获取盒子是否添加
                if(!empty($box)){
                    foreach($box as $key=>$b){
                        $more_box[$key] = $box[$key];
                        $more_box[$key]['count2'] = Db::table('yb_attitude')->where(['release_id'=>$v['id'],'user_id'=>$uid,'sid'=>$b['id'],'status'=>4])->count();
                    }
                    $release_info[$k]['more_box'] = $more_box;
                }
            }
            //获取发布信息支应的rul
            $release_rul = $release->gettypeUrl();
            $this->view->assign('release_rul',$release_rul);
            $this->view->assign('release_info',$release_info);
            $this->view->assign('release_type',$release_type);
            return $this->view->fetch();
        }
    }

    
}
