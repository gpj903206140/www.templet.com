<?php

namespace app\home\model;

use think\Model;
use think\Session;
use app\home\model\User as users;
use app\home\model\Business as company;
class business extends Model
{
    // 表名
    protected $name = 'business';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'status_text'
    ];
    

    
    public function getStatusList()
    {
        return ['0' => __('Status 0'),'1' => __('Status 1')];
    }     


    public function getStatusTextAttr($value, $data)
    {        
        $value = $value ? $value : (isset($data['status']) ? $data['status'] : '');
        $list = $this->getStatusList();
        return isset($list[$value]) ? $list[$value] : '';
    }

    /**
     * 偏好设置
     */
    public function preference_set(){
        $arr = [
            1=>['id'=>1,'name'=>'样板'], 2=>['id'=>2,'name'=>'材料'],3=>['id'=>3,'name'=>'工艺'],4=>['id'=>4,'name'=>'设备'],
            5=>['id'=>5,'name'=>'案例'], 6=>['id'=>6,'name'=>'视频'],7=>['id'=>7,'name'=>'文章'],8=>['id'=>8,'name'=>'问答'],
            9=>['id'=>9,'name'=>'CMF盒子'], 10=>['id'=>10,'name'=>'企业简介'],11=>['id'=>11,'name'=>'团队成员'],12=>['id'=>12,'name'=>'企业粉丝'],
            13=>['id'=>13,'name'=>'联系方式']
        ];
        return $arr;
    }
    /**
     * 企业认证提交
     */
    public function card_business($data,$uid){
        $res = ['msg'=>'','code'=>0,'id'=>0];
        if(empty($data['license_image'])){
            $res['msg'] = '营业执照上传失败,请重新上传';
            $res['code'] = 2;
            return json_encode($res);
        }
        if(empty($data['id_just'])){
            $res['msg'] = '身份证正面照上传失败,请重新上传';
            $res['code'] = 3;
            return json_encode($res);
        }
        if(empty($data['id_back'])){
            $res['msg'] = '身份证反面照上传失败,请重新上传';
            $res['code'] = 2;
            return json_encode($res);
        }
        $preference = Session::get('preference_set');
        if(!empty($preference)){
            $preference_set = $this->preference_set();
            foreach($preference as $v){
                $preference_count[] = $preference_set[$v];
                
            }
            $data['preference'] = json_encode($preference_count,JSON_UNESCAPED_UNICODE);
        }
        $business = new business();
        $business->startTrans();
        $user = new users();
        try {
            $result = $business->save($data);
            if($result){
                $business_id = $business->getLastInsID();
                $user->save(['business_id'=>$business_id],['id'=>$uid]);
                $res['msg'] = '企业认证提交成功,审核中...';
                $res['code'] = 1;
                $business->commit();
            }else{
                $res['msg'] = '企业认证提交失败';
                $business->rollback();
            }
        } catch (\Exception $e) {
            $business->rollback();
            //throw $e;
            $res['msg'] = $e;
        }
        return json_encode($res);
    }
    
    /**
     * 企业认证提交
     */
    public function business_editor($data,$id){
        $res = ['msg'=>'','code'=>0,'id'=>0];
        $business = new business();
        try {
            $result = $business->save($data,['id'=>$id]);
            if($result){
                $business_id = $business->getLastInsID();
                $res['msg'] = '企业信息编辑成功';
                $res['code'] = 1;
            }else{
                $res['msg'] = '企业信息编辑失败';
            }
        } catch (\Exception $e) {
            //throw $e;
            $res['msg'] = $e;
        }
        return json_encode($res);
    }
    
    /**
     * 获取企业信息
     */
    public function business_info($business_id,$uid){
        $business = new company();
        $user = new users();
        $field = '*';
        $business_info = $business->field($field)->where(['id'=>$business_id])->find();
        $business_info['company_type'] = !empty($business_info['company_type'])?json_decode($business_info['company_type'],true):$business_info['company_type'];
        $business_info['machining'] = !empty($business_info['machining'])?json_decode($business_info['machining'],true):$business_info['machining'];
        $business_info['machining_count'] = count($business_info['machining']); 
        $business_info['device'] = !empty($business_info['device'])?json_decode($business_info['device'],true):$business_info['device'];
        $business_info['device_count'] = count($business_info['device']); 
        $user_info = $user->field('username,bio,popularity,fans,release,avatar')->where(['id'=>$uid])->find();
        $business_info['user'] = $user_info;
        return $business_info;
    }
    /**
     * 上传图片
     */
    public function upload_image($name){
        $file = request()->file($name);
        $moveFile = ROOT_PATH . 'public/uploads/';
        $imgUrl = '';
        if($file){
            $info = $file->move($moveFile);
            if($info){
                $user = new users();
                $imgUrl = '/uploads/'.date('Ymd').'/'.$info->getFilename();
                /*// 成功上传后 获取上传信息
                // 输出 jpg
                echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getFilename(); */
            }else{
                // 上传失败获取错误信息
                //$res['msg'] = $file->getError();
            }
        }
        return $imgUrl;
        
    }

}
