<?php

namespace app\admin\model;

use think\Model;
use app\home\model\user as users;
use app\admin\model\business as companys;
class Business extends Model
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
        'addtime_text'
    ];


    public function getAddtimeTextAttr($value, $data)
    {
        $value = $value ? $value : (isset($data['addtime']) ? $data['addtime'] : '');
        return is_numeric($value) ? date("Y-m-d H:i:s", $value) : $value;
    }

    protected function setAddtimeAttr($value)
    {
        return $value && !is_numeric($value) ? strtotime($value) : $value;
    }

    public function user()
    {
        return $this->belongsTo("Homeuser",'user_id','id')->setEagerlyType(0);
    }

    /**
     * 企业认证审核
     */
    public function business_trial($data){
        $companys = new companys();
        $companys->startTrans();
        try{
            $res = ['msg'=>'','code'=>0,'id'=>0];
            $msg = '';
            if($data['trial']==1){
               $msg = '通过';
            }else if($data['trial']==2){
               $msg = '拒绝';
            }
            $user = new users();
            $result = $companys->save(['trial'=>$data['trial']],['id'=>$data['id']]);
            if($result){
                if($data['trial']==1){
                   $edituser = $user->save(['business_id'=>$data['id'],'user_type'=>1],['id'=>$data['user_id']]); 
                }
                $res['msg'] = '企业认证审核已'.$msg;
                $res['code'] = 1;
                $companys->commit();
            }else{
                $res['msg'] = '企业认证审核'.$msg.'请求失败';
                $companys->rollback();
            }
        } catch(\Exception $e){
            $companys->rollback();
            //throw $e;
            $res['msg'] = $e;
        }
        return json_encode($res);
        
    }

}
