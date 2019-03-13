<?php

namespace app\home\model;

use think\Model;

class user extends Model
{
    // 表名
    protected $name = 'home_user';
    
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
     * 修改个人信息
     */
    public function user_update($data,$uid=0){
        $res = ['msg'=>'','code'=>0,'id'=>0];
        $user = new user;
        $result = $user->save($data,['id'=>$uid]);
        if($result){
            $res['msg'] = '个人信息修改成功';
            $res['code'] = 1;
            $res['id'] = $uid;
        }else{
            $res['msg'] = '个人信息修改失败';
            $res['id'] = $uid;
        }
        return json_encode($res);
    }

    /**
     * 编辑头像
     */
    public function avatar_editor($uid){
        $file = request()->file('file');
        $moveFile = ROOT_PATH . 'public/uploads/';
        $res = ['msg'=>'头像上传失败','code'=>0,'id'=>$uid];
        if($file){
            
            $info = $file->move($moveFile);
            if($info){
                $user = new user();
                $imgUrl = '/uploads/'.date('Ymd').'/'.$info->getFilename();
                $avatar = $user->field('avatar')->where(['id'=>$uid])->find();
                $root = ROOT_PATH . 'public';
                if($user->save(['avatar'=>$imgUrl],['id'=>$uid])){
                    if(file_exists($root.$avatar['avatar'])&&!empty($avatar['avatar'])){
                        unlink($root.$avatar['avatar']);
                    }
                    $res['msg'] = '头像编辑成功';
                    $res['code'] = 1;
                    $res['id'] = $uid;
                }else{
                     $res['msg'] = '头像编辑失败';
                     $res['id'] = $uid;
                }
                /*// 成功上传后 获取上传信息
                // 输出 jpg
                echo $info->getExtension();
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getSaveName();
                // 输出 42a79759f284b767dfcb2a0197904287.jpg
                echo $info->getFilename(); */
            }else{
                // 上传失败获取错误信息
                $res['msg'] = $file->getError();
            }
        }
        return $res;
        
    }


}
