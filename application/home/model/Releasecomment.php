<?php

namespace app\home\model;

use think\Model;
use app\home\model\Releasecomment as comment;
class Releasecomment extends Model
{
    // 表名
    protected $name = 'release_comment';
    
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
     * 添加评论
     */
    public function add_comment($data){
        $res = ['msg'=>'','code'=>0,'id'=>0];
        $comment = new comment();
        $comment->startTrans();
        try {
            $data['level']==0?$msg = '评论':$msg = '回复评论';
            $data['type']==1?$msg = '错误/歧义反馈提交':'';
            $result = $comment->save($data);
            $id = $comment->id;
            $comment->commit();
            if($result){
                $res['msg'] = $msg.'成功,审核中...';
                $res['code'] = 1;
                $res['id'] = $id;
            }else{
                $res['msg'] = $msg.'失败';
            }
        } catch (\Exception $e) {
            //throw $e;
            $res['msg'] = $e;
            $comment->rollback();
        }
        return json_encode($res,JSON_UNESCAPED_UNICODE);
    }
    
    /**
     * 查询评论
     * @param  [type] $where 查询条件
     * @return [type]        [description]
     */
    public function get_comment($where){
        $comment_list = db('release_comment')
        ->alias('m')
        ->join('home_user a','m.user_id=a.id','left')
        ->join('business b','a.business_id=b.id')
        ->where($where)
        ->order('m.id desc')
        ->field(['m.*','a.username,a.avatar,a.vocation','b.id as business_id,b.name as business_name,b.company_type'])
        ->select();
        return $comment_list;
    }


}
