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
use app\home\model\Release as release_model;
use app\home\model\Business as company;
use app\home\model\Effect;
use app\home\model\Releasecomment as comment;

/**
 * 发布信息展示
 */
class Master extends Frontend
{

    protected $layout = 'default';
    protected $noNeedLogin = ['login', 'register', 'third'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }
    
    /**
     * 样板列表
     */
    public function tmplate_list(){
        if(request()->isAjax()){
            
        }else{
            $name = request()->get('name','');
            if(!empty($name)){
                $name = trim($name,' ');
                $where['effect'] = array('like','%'.$name.'%');
            }
            $models = new release_model();
            $user = new users();
            $business = new company();
            //获取分页样板信息
            $where['part'] = 1;
            $release_info = $models->where($where)->paginate(15);
            if(!empty($release_info)){
                foreach($release_info as $k=>$v){
                    $release_info[$k]['user'] = $user->field('username,avatar')->where(['id'=>$v['user_id']])->find();
                    $release_info[$k]['business'] = $business->field('name')->where(['id'=>$v['business_id']])->find();
                }
            }
            $page = $release_info->render();
            //获取标签
            $effect = new Effect();
            $effect_lsit = $effect->field('*')->where([])->group('name')->order('addtime des')->select();
            //print_r($effect_lsit);
            $this->view->assign('effect_lsit',$effect_lsit);
            $this->view->assign('release_info',$release_info);
            $this->view->assign('page',$page);
            return $this->view->fetch();
        }
    }

    /**
     * 样板详情
     */
    public function tmplate_detail(){
        if(request()->isAjax()){
            
        }else{
            $user = new users();
            $business = new company();
            $uid = $this->uid;
            //获取样板信息
            $id = request()->get('id',0);
            $where['id'] = $id;
            $models = new release_model();
            $release_detail = $models->get_release($where);
            //添加浏览记录
            $is_browse = $this->browse($id);
             //获取发布者信息
            $release_detail['user'] = $user->field('id,username,avatar,vocation')->where(['id'=>$release_detail['user_id']])->find();
            $release_detail['business'] = $business->field('id,name')->where(['id'=>$release_detail['business_id']])->find();
            $this->view->assign('release_detail',$release_detail);
            //获取我的盒子
            $box = $models->get_release(['user_id'=>$uid,'part'=>9],1,'id,name');
            foreach($box as $key=>$b){
                $box[$key]['count'] = Db::table('yb_attitude')->field('id')->where(['release_id'=>$id,'user_id'=>$uid,'sid'=>$b['id'],'status'=>4])->count();
            }
            $this->view->assign('box',$box);
            //他发布的
            $andwhere['user_id'] = $release_detail['user_id'];
            $andwhere['part'] = 1;
            $andwhere['id'] = array('neq',$release_detail['id']);
            $more_lsit = $models->get_release($andwhere,1,'*',5);
            $more_box = array(); //盒子
            if(!empty($more_lsit)){
                foreach($more_lsit as $k=>$v){
                    $more_lsit[$k]['user'] = $user->field('username,avatar')->where(['id'=>$v['user_id']])->find();
                    $more_lsit[$k]['business'] = $business->field('name')->where(['id'=>$v['business_id']])->find();
                    $more_lsit[$k]['is_status1'] = Db::table('yb_attitude')->where(['release_id'=>$v['id'],'user_id'=>$uid,'status'=>1])->count();
                    $more_lsit[$k]['is_status2'] = Db::table('yb_attitude')->where(['release_id'=>$v['id'],'user_id'=>$uid,'status'=>2])->count();
                    $more_lsit[$k]['is_status3'] = Db::table('yb_attitude')->where(['release_id'=>$v['id'],'user_id'=>$uid,'status'=>3])->count();
                    $more_lsit[$k]['is_status4'] = Db::table('yb_attitude')->where(['release_id'=>$v['id'],'user_id'=>$uid,'status'=>4])->count();
                    $more_lsit[$k]['is_status5'] = Db::table('yb_attitude')->where(['release_id'=>$v['id'],'user_id'=>$uid,'status'=>6])->count();

                    //获取盒子是否添加
                    if(!empty($box)){
                        foreach($box as $key=>$b){
                            $more_box[$key] = $box[$key];
                            $more_box[$key]['count2'] = Db::table('yb_attitude')->where(['release_id'=>$v['id'],'user_id'=>$uid,'sid'=>$b['id'],'status'=>4])->count();
                            $more_lsit[$k]['more_box'] = $more_box;
                        }
                    }
                }
            }
            $this->view->assign('more_lsit',$more_lsit);
            
            //得到我是否点赞...等情况
            $is_status[0] = Db::table('yb_attitude')->where(['release_id'=>$id,'user_id'=>$uid,'status'=>1])->count();
            $is_status[1] = Db::table('yb_attitude')->where(['release_id'=>$id,'user_id'=>$uid,'status'=>2])->count();
            $is_status[2] = Db::table('yb_attitude')->where(['release_id'=>$id,'user_id'=>$uid,'status'=>3])->count();
            $is_status[3] = Db::table('yb_attitude')->where(['release_id'=>$id,'user_id'=>$uid,'status'=>4])->count();
            $is_status[5] = Db::table('yb_attitude')->where(['release_id'=>$id,'user_id'=>$uid,'status'=>6])->count();
            $this->view->assign('is_status',$is_status);
            //获取当前用户信息
            $user_info = $user->field('id,username,vocation,avatar,business_id')->where(['id'=>$uid])->find();
            $user_info['business'] = $business->field('id,name')->where(['id'=>$user_info['business_id']])->find();
            $this->view->assign('user_info',$user_info);
            //获取用户评论
            $comment = new comment();
            $comment_list = $comment->get_comment(['a.status'=>0]);
            foreach($comment_list as $ck=>$c){
                $comment_list[$ck]['company_type'] = json_decode($c['company_type'],true);
                $comment_list[$ck]['is_praise'] = Db::table('yb_attitude')->where(['user_id'=>$uid,'status'=>7,'tid'=>$c['id']])->count();
            }
            //print_r($comment_list);
            $this->view->assign('comment_list',$comment_list);
            return $this->view->fetch();
        }

    }

    /**
     * 踩 赞....
     */
    public function attitude(){
        $data['user_id'] = $this->uid;
        $data['status'] = request()->get('status',0);
        $data['release_id'] = request()->get('release_id',0);
        $data['addtime'] = time();
        $field = '';
        $where = ['release_id'=>$data['release_id'],'user_id'=>$data['user_id'],'status'=>$data['status']];
        switch($data['status']){
            case 1: //踩
               $field = 'trample';
               break;
            case 2: //赞
               $field = 'praise';
               break;
            case 3: //收藏
               $field = 'collection';
               break;
            case 4: //加入盒子
               $field = 'addbox';
               $data['sid'] = request()->get('sid',0);
               $where['sid'] = $data['sid'];
               break;
        }
        $release = new release_model();
        $status = Db::table('yb_attitude')->field('id')->where($where)->find();
        if($status){
            if(Db::table('yb_attitude')->where(['id'=>$status['id']])->delete()){
                $release->where(['id'=>$data['release_id']])->setDec($field,1);
                echo 3;
            }else{
                echo 4;
            }
        }else{
            if(Db::table('yb_attitude')->insert($data)){
                $release->where(['id'=>$data['release_id']])->setInc($field,1);
                echo 1;
            }else{
                echo 2;
            }
        }
        
    }

    /**
     * 浏览
     */
    public function browse($release_id=0){
        $data['user_id'] = $this->uid;
        $data['status'] = 5;
        $data['release_id'] = $release_id;
        $data['addtime'] = time();
        $field = 'browse';
        $release = new release_model();
        Db::table('yb_attitude')->startTrans();
        $reslut = Db::table('yb_attitude')->insert($data);
        $release->where(['id'=>$data['release_id']])->setInc($field,1);
        if($reslut){
            Db::table('yb_attitude')->commit();
            return true;
        }else{
            Db::table('yb_attitude')->rollback();
           return false;
        }    
    }

    /**
     * 关注
     */
    public function follow(){
        $data['user_id'] = $this->uid;
        $data['status'] = request()->get('status',0);
        $data['release_id'] = request()->get('release_id',0);
        $data['sid'] = request()->get('sid',0);
        $data['addtime'] = time();
        $field = '';
        $where = ['release_id'=>$data['release_id'],'user_id'=>$data['user_id'],'status'=>$data['status'],'sid'=>$data['sid']];
        $user = new users();
        $status = Db::table('yb_attitude')->field('id')->where($where)->find();
        if($status){
            if(Db::table('yb_attitude')->where(['id'=>$status['id']])->delete()){
                $user->where(['id'=>$data['user_id']])->setDec('fans',1);
                echo 3;
            }else{
                echo 4;
            }
        }else{
            if(Db::table('yb_attitude')->insert($data)){
                $user->where(['id'=>$data['user_id']])->setInc('fans',1);
                echo 1;
            }else{
                echo 2;
            }
        }
        
    }

    //评论
    public function comment(){
        $uid = $this->uid;
        $data['reply_id'] = request()->post('reply_id',0);
        $data['release_id'] = request()->get('release_id',0);
        $data['level'] = request()->post('level',0);
        $data['content'] = request()->post('content','');
        $data['user_id'] = $uid;
        $data['addtime'] = time();
        $comment = new comment();
        $res = $comment->add_comment($data);
        return $res;
    }

    //评论赞
    public function praise(){
        $data['user_id'] = $this->uid;
        $data['status'] = request()->get('status',0);
        $data['sid'] = request()->get('sid',0);
        $data['tid'] = request()->get('tid',0);
        $data['release_id'] = request()->get('release_id',0);
        $data['addtime'] = time();
        $field = '';
        $where = ['tid'=>$data['tid'],'status'=>$data['status']];
        $comment = new comment();
        $status = Db::table('yb_attitude')->field('id')->where($where)->find();
        if($status){
            if(Db::table('yb_attitude')->where(['id'=>$status['id']])->delete()){
                $comment->where(['id'=>$data['tid']])->setDec('praise',1);
                echo 3;
            }else{
                echo 4;
            }
        }else{
            if(Db::table('yb_attitude')->insert($data)){
                $comment->where(['id'=>$data['tid']])->setInc('praise',1);
                echo 1;
            }else{
                echo 2;
            }
        }
    }

}
