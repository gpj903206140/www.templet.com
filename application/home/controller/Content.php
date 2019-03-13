<?php

namespace app\home\controller;

use app\common\controller\Frontend;
use think\Config;
use think\Cookie;
use think\Hook;
use think\Session;
use think\Validate;

use app\admin\model\Article;
use app\admin\model\Articleclass;
use app\admin\model\Userfeedback;
use app\home\model\user as users;

/**
 * 会员中心
 */
class Content extends Frontend
{

    protected $layout = 'default';
    protected $noNeedLogin = ['login', 'register', 'third'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 帮助中心
     */
    public function help(){
        //获取帮助中心内容
        $article_model = new Article();
        $class_model = new Articleclass();
        $list = $class_model->field('class_id,name')->where(['pid'=>12])->select();
        foreach($list as $k=>$v){
            $list[$k]['article'] = $article_model->field('article_id,title,class_id,class_name,content')->where(['class_id'=>$v['class_id']])->order('sort')->select();
        }

        $this->view->assign('list',$list);
        return $this->view->fetch();
    }

    /**
     * 用户协议
     */
    public function protocol(){
        //获取帮助中心内容
        $article_model = new Article();
        $class_model = new Articleclass();
        $protocol = $article_model->field('article_id,title,content')->where(['class_id'=>13])->find();

        $this->view->assign('protocol',$protocol);
        return $this->view->fetch();
    }

    /**
     * 用户反馈
     */
    public function userfeedback(){
        if(request()->isAjax()){
           $uid = $this->uid;
           $user = new users();
           $userInfo = $user->field('username')->where(['id'=>$uid])->find();
           $res = ['msg'=>'','code'=>0,'id'=>0];
           $data['user_id'] = $uid;
           $data['user_name'] = $userInfo['username'];
           $data['issue_type'] = request()->post('issue_type','');
           $data['content'] = request()->post('content','');
           $data['contacts'] = request()->post('contacts','');
           $data['email'] = request()->post('email','');
           $data['tel'] = request()->post('tel','');
           $data['addtime'] = date('Y-m-d H:i:s');
           $userfeedback = new userfeedback();
           if($userfeedback->save($data)){
               $res['msg'] = '问题/反馈提交成功';
               $res['code'] = 1;
           }else{
               $res['msg'] = '问题/反馈提交失败';
           }
           return json_encode($res);
        }else{
           return $this->view->fetch(); 
        }
        
    }
    
    /**
     * 关于我们
     */
    public function about_us(){
        //获取帮助中心内容
        $article_model = new Article();
        $class_model = new Articleclass();
        $data = $article_model->field('article_id,title,content')->where(['class_id'=>14])->find();

        $this->view->assign('data',$data);
        return $this->view->fetch();
    }

    /**
     * 诚聘英才
     */
    public function recruit(){
        //获取帮助中心内容
        $article_model = new Article();
        $class_model = new Articleclass();
        $data = $article_model->field('article_id,title,content')->where(['class_id'=>15])->select();
        foreach($data as $k=>$v){
            $v['content'] = explode('||=||',$v['content']);//print_r(count($v['content']));exit;
            $v['count'] = count($v['content']);
            $data[$k] = $v;
        }
        $this->view->assign('data',$data);
        return $this->view->fetch();
    }

    /**
     * 联系我们
     */
    public function contact(){
        //获取帮助中心内容
        $model = new \app\admin\model\Contact;
        $data = $model->field('*')->where([])->select();
        $this->view->assign('data',$data);
        return $this->view->fetch();
    }
    
}
