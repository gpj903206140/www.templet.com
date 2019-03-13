<?php

namespace app\home\controller;

use app\common\controller\Frontend;
use think\Config;
use think\Cookie;
use think\Hook;
use think\Session;
use think\Validate;

use app\home\model\user as users;
use app\home\model\release_tmplate as template;
use app\home\model\release_material as material;
use app\home\model\release_technology as technology;
use app\home\model\release_device as device;
use app\home\model\release_cases as cases;
use app\home\model\release_video as video;
use app\home\model\release_article as article;
use app\home\model\release_interlocution as interlocution;
use app\home\model\release_cmfbox as cmfbox;
use app\home\model\business as company;

/**
 * 企业
 */
class Release extends Frontend
{

    protected $layout = 'default';
    protected $noNeedLogin = ['login', 'register', 'third'];
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 发布样板
     */
    public function tmplate(){
        $template = new template();
        $uid = $this->uid;
        if(request()->isAjax()){
            $data['user_id'] = $uid;
            $data['business_id'] = $this->business_id;
            $data['number'] = request()->post('number',''); //样板编号
            $business = new company();
            $data['cover_image'] = $business->upload_image('file'); //封面图片
            $data['abstract'] = request()->post('abstract',''); // 简介
            if(!empty($_POST['parameter'])){ //参数
                $parameter = array();
                foreach($_POST['parameter'] as $p){
                    $val = $p[0].$p[1];
                    if(!empty($val)){
                        $parameter[] = $p;
                    }
                }
                if(!empty($parameter)){
                    $data['parameter'] = json_encode($parameter,JSON_UNESCAPED_UNICODE);
                }
                
            }
            $data['material'] = request()->post('material',''); //材料
            $data['consumables'] = request()->post('consumables',''); //耗材
            $data['device'] = request()->post('device',''); //使用设备
            $data['moulding_process'] = request()->post('moulding_process',''); //成型工艺
            $data['surface_process'] = request()->post('surface_process',''); //表面工艺
            $data['join_process'] = request()->post('join_process',''); //连接工艺
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
            
            $result = $template->edit_tmplate($data);
            return $result;
        }else{
            //获取样板信息
            $template_info = $template->get_tmplate($uid);
            $this->view->assign('template_info',$template_info);
            return $this->view->fetch();
        }
    }

    /**
     * 发布材料
     */
    public function material(){
        $material = new material();
        $uid = $this->uid;
        if(request()->isAjax()){
            $data['user_id'] = $uid;
            $data['business_id'] = $this->business_id;
            $data['name'] = request()->post('name',''); //材料名称
            $business = new company();
            $data['cover_image'] = $business->upload_image('file'); //封面图片
            $data['abstract'] = request()->post('abstract',''); // 简介
            if(!empty($_POST['alias'][0])){ //材料别名
                $data['alias'] = json_encode($_POST['alias'],JSON_UNESCAPED_UNICODE);
            }

            if(!empty($_POST['categories'])){ //材料大类
                $data['categories'] = json_encode($_POST['categories'],JSON_UNESCAPED_UNICODE);
            }
            $data['moulding_process'] = request()->post('moulding_process',''); //常用成型工艺
            $data['surface_process'] = request()->post('surface_process',''); //常用表面工艺
            $data['join_process'] = request()->post('join_process',''); //常用连接工艺
            $data['price_accounting'] = request()->post('price_accounting',''); //价格核算
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
            if(!empty($_POST['reference'])){ //参考文献
                $reference = array();
                $_POST['reference'] = array_filter($_POST['reference']);
                foreach($_POST['reference'] as $r){
                    $reference[] = $r;
                }
                if(!empty($reference)){
                    $data['reference'] = json_encode($reference,JSON_UNESCAPED_UNICODE);
                }
            }
            $data['content'] = request()->post('content',''); //内容
            $data['draft'] = request()->post('draft',0); //是否为草稿
            $data['addtime'] = time();
            
            $result = $material->edit_material($data);
            return $result;
        }else{
            
            return $this->view->fetch();
        }
    }

    /**
     * 发布工艺
     */
    public function technology(){
        $technology = new technology();
        $uid = $this->uid;
        if(request()->isAjax()){
            $data['user_id'] = $uid;
            $data['business_id'] = $this->business_id;
            $data['name'] = request()->post('name',''); //工艺名称
            $business = new company();
            $data['cover_image'] = $business->upload_image('file'); //封面图片
            $data['abstract'] = request()->post('abstract',''); // 简介
            if(!empty($_POST['alias'][0])){ //工艺别名
                $data['alias'] = json_encode($_POST['alias'],JSON_UNESCAPED_UNICODE);
            }

            if(!empty($_POST['category'])){ //工艺类别
                $data['category'] = json_encode($_POST['category'],JSON_UNESCAPED_UNICODE);
            }
            $data['point'] = request()->post('point',''); //特点
            $data['design_guide'] = request()->post('design_guide',''); //设计指南
            $data['price_accounting'] = request()->post('price_accounting',''); //价格核算
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
            if(!empty($_POST['reference'])){ //参考文献
                $reference = array();
                $_POST['reference'] = array_filter($_POST['reference']);
                foreach($_POST['reference'] as $r){
                    $reference[] = $r;
                }
                if(!empty($reference)){
                    $data['reference'] = json_encode($reference,JSON_UNESCAPED_UNICODE);
                }
            }
            $data['procedure'] = request()->post('procedure',''); //工艺流程
            $data['draft'] = request()->post('draft',0); //是否为草稿
            $data['addtime'] = time();
            
            $result = $technology->edit_technology($data);
            return $result;
        }else{
            
            return $this->view->fetch();
        }
    }

    /**
     * 发布设备
     */
    public function device(){
        $device = new device();
        $uid = $this->uid;
        if(request()->isAjax()){
            $data['user_id'] = $uid;
            $data['business_id'] = $this->business_id;
            $data['name'] = request()->post('name',''); //设备名称
            $business = new company();
            $data['cover_image'] = $business->upload_image('file'); //封面图片
            $data['abstract'] = request()->post('abstract',''); // 简介
            if(!empty($_POST['alias'][0])){ //设备别名
                $data['alias'] = json_encode($_POST['alias'],JSON_UNESCAPED_UNICODE);
            }

            $data['apply_device'] = request()->post('apply_device',''); //适用工艺
            $data['price_accounting'] = request()->post('price_accounting',''); //价格核算
            if(!empty($_POST['reference'])){ //参考文献
                $reference = array();
                $_POST['reference'] = array_filter($_POST['reference']);
                foreach($_POST['reference'] as $r){
                    $reference[] = $r;
                }
                if(!empty($reference)){
                    $data['reference'] = json_encode($reference,JSON_UNESCAPED_UNICODE);
                }
            }
            $data['content'] = request()->post('content',''); //详情简介
            $data['draft'] = request()->post('draft',0); //是否为草稿
            $data['addtime'] = time();
            
            $result = $device->edit_device($data);
            return $result;
        }else{
            
            return $this->view->fetch();
        }
    }

    /**
     * 发布案例
     */
    public function cases(){
        $cases = new cases();
        $uid = $this->uid;
        if(request()->isAjax()){
            $data['user_id'] = $uid;
            $data['business_id'] = $this->business_id;
            $data['name'] = request()->post('name',''); // 案例名称
            $business = new company();
            $data['cover_image'] = $business->upload_image('file'); //封面图片
            $data['abstract'] = request()->post('abstract',''); // 简介
            $data['template_number'] = request()->post('template_number',''); //样板编号
            $data['material'] = request()->post('material',''); //所用材料
            $data['moulding_process'] = request()->post('moulding_process',''); //成型工艺
            $data['surface_process'] = request()->post('surface_process',''); //表面工艺
            $data['join_process'] = request()->post('join_process',''); //连接工艺
            $data['process_device'] = request()->post('process_device',''); //工艺设备
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
            $data['content'] = request()->post('content',''); //详情
            $data['draft'] = request()->post('draft',0); //是否为草稿
            $data['addtime'] = time();
            
            $result = $cases->edit_cases($data);
            return $result;
        }else{
            
            return $this->view->fetch();
        }
    }

    /**
     * 发布视频
     */
    public function video(){
        $video = new video();
        $uid = $this->uid;
        if(request()->isAjax()){
            $data['user_id'] = $uid;
            $data['business_id'] = $this->business_id;
            $data['name'] = request()->post('name',''); // 视频标题
            $business = new company();
            $data['cover_image'] = $business->upload_image('file'); //封面图片
            $data['abstract'] = request()->post('abstract',''); // 简介
            $data['surface_process'] = request()->post('surface_process',''); //表面工艺
            $data['moulding_process'] = request()->post('moulding_process',''); //成型工艺
            $data['join_process'] = request()->post('join_process',''); //连接工艺
            $data['process_device'] = request()->post('process_device',''); //设备
            $data['other'] = request()->post('other',''); //其他
            $data['content'] = request()->post('content',''); //详情
            $data['draft'] = request()->post('draft',0); //是否为草稿
            $data['addtime'] = time();
            
            $result = $video->edit_video($data);
            return $result;
        }else{
            
            return $this->view->fetch();
        }
    }

    /**
     * 发布文章
     */
    public function article(){
        $article = new article();
        $uid = $this->uid;
        if(request()->isAjax()){
            $data['user_id'] = $uid;
            $data['business_id'] = $this->business_id;
            $data['name'] = request()->post('name',''); // 文章标题
            $business = new company();
            $data['cover_image'] = $business->upload_image('file'); //封面图片
            $data['abstract'] = request()->post('abstract',''); // 简介
            $data['material'] = request()->post('material',''); //材料
            $data['surface_process'] = request()->post('surface_process',''); //表面工艺
            $data['moulding_process'] = request()->post('moulding_process',''); //成型工艺
            $data['join_process'] = request()->post('join_process',''); //连接工艺
            $data['process_device'] = request()->post('process_device',''); //设备
            $data['other'] = request()->post('other',''); //其他
            $data['content'] = request()->post('content',''); //详情
            $data['draft'] = request()->post('draft',0); //是否为草稿
            $data['addtime'] = time();
            
            $result = $article->edit_article($data);
            return $result;
        }else{
            
            return $this->view->fetch();
        }
    }

    /**
     * 发布问答
     */
    public function interlocution(){
        $interlocution = new interlocution();
        $uid = $this->uid;
        if(request()->isAjax()){
            $data['user_id'] = $uid;
            $data['business_id'] = $this->business_id;
            $data['name'] = request()->post('name',''); // 问题标题
            $business = new company();
            $data['cover_image'] = $business->upload_image('file'); //封面图片
            $data['material'] = request()->post('material',''); //材料
            $data['surface_process'] = request()->post('surface_process',''); //表面工艺
            $data['moulding_process'] = request()->post('moulding_process',''); //成型工艺
            $data['join_process'] = request()->post('join_process',''); //连接工艺
            $data['process_device'] = request()->post('process_device',''); //设备
            $data['other'] = request()->post('other',''); //其他
            $data['content'] = request()->post('content',''); //详情
            $data['draft'] = request()->post('draft',0); //是否为草稿
            $data['addtime'] = time();
            
            $result = $interlocution->edit_interlocution($data);
            return $result;
        }else{
            
            return $this->view->fetch();
        }
    }

    /**
     * 发布CMF例子
     */
    public function cmfbox(){
        $cmfbox = new cmfbox();
        $uid = $this->uid;
        if(request()->isAjax()){
            $data['user_id'] = $uid;
            $data['business_id'] = $this->business_id;
            $data['name'] = request()->post('name',''); // 盒子标题
            if(!empty($_POST['property'])){ //属性
                $data['property'] = json_encode($_POST['property'],JSON_UNESCAPED_UNICODE);
            }
            $data['content'] = request()->post('content',''); //详情
            $data['draft'] = request()->post('draft',0); //是否为草稿
            $data['addtime'] = time();
            
            $result = $cmfbox->edit_cmfbox($data);
            return $result;
        }else{
            
            return $this->view->fetch();
        }
    }

}
