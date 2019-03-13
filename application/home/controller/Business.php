<?php

namespace app\home\controller;

use app\common\controller\Frontend;
use think\Config;
use think\Cookie;
use think\Hook;
use think\Session;
use think\Validate;

use app\home\model\User as users;
use app\home\model\Business as company;

/**
 * 企业
 */
class Business extends Frontend
{

    protected $layout = 'default';
    protected $noNeedLogin = ['login', 'register', 'third'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 我的企业
     */
    public function my_business(){
        $user = new users();
        $uid = $this->uid;
        if(request()->isAjax()){
            
        }else{
            return $this->view->fetch();
        }
    }

    /**
     * 偏好设置
     */
    public function preference_set(){
        $user = new users();
        $uid = $this->uid;
        if(request()->isAjax()){
            $set = request()->post(false);
            Session::set('preference_set',$set['set']);
            echo 1;
        }else{
            $business = new company();
            $arr = $business->preference_set();
            $this->view->assign('arr',$arr);
            return $this->view->fetch();
        }
    }

    /**
     * 企业认证
     */
    public function card_business(){
        $user = new users();
        $uid = $this->uid;
        if(request()->isAjax()){
            $data['user_id'] = $uid;
            $data['name'] = request()->post('name','');
            $data['mobile'] = request()->post('mobile','');
            $data['email'] = request()->post('email','');
            $data['qq'] = request()->post('qq','');
            $data['wechat'] = request()->post('wechat','');
            $business = new company();
            $data['logo'] = $business->upload_image('logo');
            $data['license_image'] = $business->upload_image('license_image');
            $data['id_just'] = $business->upload_image('id_just');
            $data['id_back'] = $business->upload_image('id_back');
            
            $data['addtime'] = time();
            $result = $business->card_business($data,$uid);
            return $result;
        }else{
            //print_r(Session::get('preference_set'));
            return $this->view->fetch();
        }
    }

    /**
     * 企业主页
     */
    public function business_home(){
        $user = new users();
        $business = new company();
        $uid = $this->uid;
        $business_id = $this->business_id;
        if(request()->isAjax()){
            
        }else{
            $business_info = $business->business_info($business_id,$uid);
            
            $this->view->assign('business_info',$business_info);
            return $this->view->fetch();
        }
    }

    /**
     * 企业信息
     */
    public function business_info(){
        $user = new users();
        $business = new company();
        if(request()->isAjax()){
            
        }else{
            $business_id = request()->get('business_id',0);
            $user_id = request()->get('user_id',0);
            if(empty($business_id)){
                $business_id = $this->business_id;
                $uid = $this->uid;
            }else{
                $business_id = $business_id;
                $uid = $user_id;
            }
            $business_info = $business->business_info($business_id,$uid);
            $this->view->assign('user_id',$user_id);
            $this->view->assign('business_id',$business_id);
            $this->view->assign('business_info',$business_info);
            return $this->view->fetch();
        }
    }

    /**
     * 企业信息编辑
     */
    public function business_editor(){
        $user = new users();
        $business = new company();
        $uid = $this->uid;
        $business_id = $this->business_id;
        if(request()->isAjax()){
            $data['name'] = request()->post('name','');
            $data['profile'] = request()->post('profile','');
            $data['found_date'] = request()->post('found_date','');
            $data['enroll'] = request()->post('enroll','');
            $data['enroll_address'] = request()->post('enroll_address','');
            $data['main_products'] = request()->post('main_products','');
            if(isset($_POST['company_type'])&&!empty($_POST['company_type'])){
                $company_type = array_filter($_POST['company_type']);
                $type = array();
                foreach($company_type as $v){
                    $type[] = $v;
                }
                $data['company_type'] = json_encode($type,JSON_UNESCAPED_UNICODE);
            }
            if(isset($_POST['machining'])&&!empty($_POST['machining'][0])){
                $data['machining'] = json_encode(array_filter($_POST['machining']),JSON_UNESCAPED_UNICODE);
            }
            if(isset($_POST['device'])&&!empty($_POST['device'][0])){
                $data['device'] = json_encode(array_filter($_POST['device']),JSON_UNESCAPED_UNICODE);
            }

            $data['main_market'] = request()->post('main_market','');
            $data['staff_num'] = request()->post('staff_num','');
            $data['office_area'] = request()->post('office_area','');
            $data['major_customers'] = request()->post('major_customers','');
            
            $reslut = $business->business_editor($data,$business_id);
            return $reslut;
        }else{
            $business_info = $business->business_info($business_id,$uid);
            
            $this->view->assign('business_info',$business_info);
            return $this->view->fetch();
        }
    }

}
