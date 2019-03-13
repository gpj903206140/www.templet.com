<?php

namespace app\home\controller;

use app\common\controller\Frontend;
use think\Config;
use think\Cookie;
use think\Hook;
use think\Session;
use think\Validate;

use app\home\model\User as users;
use app\home\model\Release as release_model;
use app\home\model\Releasecmfbox as cmfbox;
use app\home\model\Business as company;

/**
 * 企业
 */
class Release extends Frontend
{

    protected $layout = 'default';
    protected $noNeedLogin = ['login', 'register', 'third'];
    protected $noNeedRight = ['*'];
    protected $models = false;

    public function _initialize()
    {
        parent::_initialize();
        $this->models = new release_model();
    }
    /**
     * 循环并重新排序数组
     */
    public function isset_sort($data,$name){
        if(!empty($_POST[$name])){ //参数
            $parameter = array();
            foreach($_POST[$name] as $p){
                $parameter[] = $p;
            }
            if(!empty($parameter)){
                $data[$name] = json_encode($parameter,JSON_UNESCAPED_UNICODE);
                
            }
        }
        return $data;
    }
    /**
     * 获取前台提交的发布的信息
     */
    public function getRelease($part=0,$part_name=''){
        $uid = $this->uid;
        $data['user_id'] = $uid;
        $data['part'] = $part;
        $data['part_name'] = $part_name;
        $data['business_id'] = $this->business_id;
        $data['id'] = request()->post('id',0);
        $data['name'] = request()->post('name',''); //样板编号
        $business = new company();
        if(!empty($_FILES['file']['tmp_name'])){
            $data['cover_image'] = $business->upload_image('file'); //封面图片
        }
        $data['abstract'] = request()->post('abstract',''); // 简介
        if(isset($_POST['parameter'])&&!empty($_POST['parameter'])){ //参数
            $parameter = array();
            /*foreach($_POST['parameter'] as $p){
                $val = $p[0].$p[1];
                if(!empty($val)){
                    $parameter[] = $p;
                }
            }*/
            $parameter = array_filter(array_combine($_POST['parameter']['id'],$_POST['parameter']['name']));
            if(!empty($parameter)){
                $data['parameter'] = json_encode($parameter,JSON_UNESCAPED_UNICODE);
            }
        }
        $data = $this->isset_sort($data,'material'); //材料
        $data = $this->isset_sort($data,'consumables'); //耗材/样板编号/其他
        $data = $this->isset_sort($data,'device'); //使用设备
        $data = $this->isset_sort($data,'moulding_process'); //成型工艺
        $data = $this->isset_sort($data,'surface_process'); //表面工艺
        $data = $this->isset_sort($data,'join_process'); //连接工艺
        if(!empty($_POST['effect'])){ //效果
            $effect = array();
            $_POST['effect'] = array_filter($_POST['effect']);
            foreach($_POST['effect'] as $e){
                $effect[] = $e;
            }
            if(!empty($effect)){
                $data['effect'] = json_encode($effect,JSON_UNESCAPED_UNICODE);
            }
        }
        $data['content'] = request()->post('content',''); //内容
        $data['draft'] = request()->post('draft',0); //是否为草稿
        $data['addtime'] = time();
        //非样板加的
        if(isset($_POST['alias'][0])&&!empty($_POST['alias'][0])){ //别名
            $data['alias'] = json_encode($_POST['alias'],JSON_UNESCAPED_UNICODE);
        }

        $data = $this->isset_sort($data,'category');
        $data['point'] = request()->post('point',''); //特点
        $data['design_guide'] = request()->post('design_guide',''); //设计指南
        $data['price_accounting'] = request()->post('price_accounting',''); //价格核算
        if(isset($_POST['reference'])&&!empty($_POST['reference'])){ //参考文献
            $reference = array();
            $_POST['reference'] = array_filter($_POST['reference']);
            foreach($_POST['reference'] as $r){
                $reference[] = $r;
            }
            if(!empty($reference)){
                $data['reference'] = json_encode($reference,JSON_UNESCAPED_UNICODE);
            }
        }
        return $data;
    }

    /**
     * 获取发布信息
     */
    public function getRelease_info(){
        $state = request()->post('state',0);
        $type = request()->post('type',0);
        $val = request()->post('val','');
        if(empty($val)){
            return false;
        }
        if($type>0){
            $where['part'] = $type;
        }
        $where['name|alias'] = array('like','%'.$val.'%');
        if($state==0){
           $release_info = $this->models->get_release($where,1,'id,alias,name'); 
        }elseif($state==1){
           $release_info = $this->models->get_release($where,1,'id,alias,name',5); 
        }
        
        $release_array = array();
        if(!empty($release_info)){
            foreach($release_info as $k=>$v){
                $release_array[$k]['name'] = $v['name'];
                $release_array[$k]['alias'] = $v['alias'];
                $release_array[$k]['id'] = $v['id'];
            }
        }
        
        return json_encode($release_array,JSON_UNESCAPED_UNICODE);//exit;
    }
    /**
     * 发布样板
     */
    public function tmplate(){
        if(request()->isAjax()){
            $release_info = $this->getRelease(1,'样板','编号');
            $result = $this->models->edit_release($release_info,'样板');
            return $result;
        }else{
            //获取样板信息
            $id = request()->get('id',0);
            $where['id'] = $id;
            $release_info = $this->models->get_release($where);
            $this->view->assign('release_info',$release_info);
            return $this->view->fetch();
        }
    }

    /**
     * 发布材料
     */
    public function material(){
        if(request()->isAjax()){
            $release_info = $this->getRelease(2,'材料');
            $result = $this->models->edit_release($release_info,'材料');
            return $result;
        }else{
            $id = request()->get('id',0);
            $where['id'] = $id;
            $release_info = $this->models->get_release($where);
            $i=0;
            $j=0;
            $reference = array();
            if(!empty($release_info['reference'])){
                foreach($release_info['reference'] as $v){
                    $reference[$j][] = $v;
                    if($i%2){
                        $j++;
                    }
                    $i++;
                }
                $release_info['reference'] = $reference;
            }
            
            $this->view->assign('release_info',$release_info);
            return $this->view->fetch();
        }
    }

    /**
     * 发布工艺
     */
    public function technology(){
        if(request()->isAjax()){
            $release_info = $this->getRelease(3,'工艺');
            $result = $this->models->edit_release($release_info,'工艺');
            return $result;
        }else{
            $id = request()->get('id',0);
            $where['id'] = $id;
            $release_info = $this->models->get_release($where);
            $i=0;
            $j=0;
            $reference = array();
            if(!empty($release_info['reference'])){
                foreach($release_info['reference'] as $v){
                    $reference[$j][] = $v;
                    if($i%2){
                        $j++;
                    }
                    $i++;
                }
                $release_info['reference'] = $reference;
            }
            $this->view->assign('release_info',$release_info);
            return $this->view->fetch();
        }
    }

    /**
     * 发布设备
     */
    public function device(){
        if(request()->isAjax()){
            $release_info = $this->getRelease(4,'设备');
            $result = $this->models->edit_release($release_info,'设备');
            return $result;
        }else{
            $id = request()->get('id',0);
            $where['id'] = $id;
            $release_info = $this->models->get_release($where);
            $i=0;
            $j=0;
            $reference = array();
            if(!empty($release_info['reference'])){
                foreach($release_info['reference'] as $v){
                    $reference[$j][] = $v;
                    if($i%2){
                        $j++;
                    }
                    $i++;
                }
                $release_info['reference'] = $reference;
            }
            $this->view->assign('release_info',$release_info);
            return $this->view->fetch();
        }
    }

    /**
     * 发布案例
     */
    public function cases(){
        if(request()->isAjax()){
            $release_info = $this->getRelease(5,'案例');
            $result = $this->models->edit_release($release_info,'案例');
            return $result;
        }else{
            $id = request()->get('id',0);
            $where['id'] = $id;
            $release_info = $this->models->get_release($where);
            $this->view->assign('release_info',$release_info);
            return $this->view->fetch();
        }
    }

    /**
     * 发布视频
     */
    public function video(){
        if(request()->isAjax()){
            $release_info = $this->getRelease(6,'视频','标题');
            $result = $this->models->edit_release($release_info,'视频');
            return $result;
        }else{
            $id = request()->get('id',0);
            $where['id'] = $id;
            $release_info = $this->models->get_release($where);
            $this->view->assign('release_info',$release_info);
            return $this->view->fetch();
        }
    }

    /**
     * 发布文章
     */
    public function article(){
        if(request()->isAjax()){
            $release_info = $this->getRelease(7,'文章','标题');
            $result = $this->models->edit_release($release_info,'文章');
            return $result;
        }else{
            $id = request()->get('id',0);
            $where['id'] = $id;
            $release_info = $this->models->get_release($where);
            $this->view->assign('release_info',$release_info);
            return $this->view->fetch();
        }
    }

    /**
     * 发布问答
     */
    public function interlocution(){
        if(request()->isAjax()){
            $release_info = $this->getRelease(8,'问答','标题');
            $result = $this->models->edit_release($release_info,'问答');
            return $result;
        }else{
            $id = request()->get('id',0);
            $where['id'] = $id;
            $release_info = $this->models->get_release($where);
            $this->view->assign('release_info',$release_info);
            return $this->view->fetch();
        }
    }

    /**
     * 发布CMF盒子
     */
    public function cmfbox(){
        if(request()->isAjax()){
            $release_info = $this->getRelease(9,'CMF盒子','标题');
            $result = $this->models->edit_release($release_info,'CMF盒子');
            return $result;
        }else{
            $id = request()->get('id',0);
            $where['id'] = $id;
            $release_info = $this->models->get_release($where);
            $this->view->assign('release_info',$release_info);
            return $this->view->fetch();
        }
    }

    /**
     * 删除发布的信息
     */
    public function del_release(){
        $id = request()->get('id',0);
        if($this->models->save(['is_del'=>1],['id'=>$id])){
            echo 1;
        }else{
            echo 2;
        }
    }

}
