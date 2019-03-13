<?php

namespace app\home\controller;
use app\common\controller\Frontend;
use app\common\library\Token;

use \think\Session;
use \think\Cookie;
use fast\Random;
class Login extends Frontend
{

    protected $noNeedLogin = '*';
    protected $noNeedRight = '*';
    protected $layout = '';

    public function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        return $this->view->fetch();
    }

    /**
     * 用户注册
     * @return [type] [description]
     */
    public function register()
    {
        if(request()->isAjax()){
            $m = new \app\home\model\user;
            $res = ['code'=>0,'msg'=>'','id'=>0];
            $mobile = $this->request->post('mobile');
            $passwd = $this->request->post('passwd');
            $code = $this->request->post('code');
            $data['mobile'] = $mobile;
            $salt = Random::alnum(6);
            $data['password'] = md5(md5($passwd).$salt);
            $data['salt'] = $salt;
            $data['status'] = 'normal';
            $uid = $m->where(['mobile'=>$mobile])->field('id')->find();
            if(!empty($uid)){
                $res['msg'] = '手机号已存在';
                return json_encode($res);
                return false;
            }
            if($id = $m->save($data)){
                $res['code'] = 1; 
                $res['msg'] = '用户注册成功';
                $res['id'] = $id;
            }else{
                $res['msg'] = '用户注册失败';
                $res['id'] = $id;
            }
            return json_encode($res);
        }else{
            return $this->view->fetch();
        }
        
    }
    
    /**
     * 用户登录
     */
    public function sign()
    {
        if(request()->isAjax()){
            $m = new \app\home\model\user;
            $res = ['code'=>0,'msg'=>'','id'=>0];
            $mobile = $this->request->post('mobile');
            $passwd = $this->request->post('passwd');
            $user = $m->where(['mobile'=>$mobile])->field('id,username,password,salt,mobile,avatar,user_type,business_id,level')->find();
            if(empty($user)){
                $res['msg'] = '手机号不正确';
                return json_encode($res);
                return false;
            }
            $password = md5(md5($passwd).$user['salt']);
            if($user['password']!=$password){
                $res['msg'] = '密码不正确';
                return json_encode($res);
                return false;
            }
            unset($user['password']);
            Session::set('home_user',$user);
            Cookie::set('home_user',$user,3600*24*30);
            $res['code'] = 1;
            $res['msg'] = '登录成功';
            $res['id'] = $user['id'];
            return json_encode($res);
        }else{
            return $this->view->fetch();
        }
    }

    /**
     * 退出登录
     */
    public function loginOut(){
        Session::set('home_user',null);
        Cookie::delete('home_user');
        echo 1;
    }

}
