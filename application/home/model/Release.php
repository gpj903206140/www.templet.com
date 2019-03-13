<?php

namespace app\home\model;

use think\Model;
use think\Session;
use app\home\model\User as users;
use app\home\model\Business;
use app\home\model\Release;
use app\home\model\Effect;
class release extends Model
{
    // 表名
    protected $name = 'release';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = false;

    // 定义时间戳字段名
    protected $createTime = false;
    protected $updateTime = false;
    
    // 追加属性
    protected $append = [
        'status_text'
    ];
    
    /**
     * 修改信息对应发布信息对应的url
     */
    public function gettypeUrl(){
        $arr[0] = [
           1=>'home/release/tmplate',
           2=>'home/release/material',
           3=>'home/release/technology',
           4=>'home/release/device',
           5=>'home/release/cases',
           6=>'home/release/video',
           7=>'home/release/article',
           8=>'home/release/interlocution',
           9=>'home/release/cmfbox',
        ];
        $arr[1] = [
            1=>'样板',
            2=>'材料',
            3=>'工艺',
            4=>'工艺设备',
            5=>'案例',
            6=>'视频',
            7=>'文章',
            8=>'问答',
            9=>'CMF盒子',
        ];
        return $arr;
    }
    /**
     * 发布样板
     */
    public function edit_release($data,$msg='',$name='名称'){
        $res = ['msg'=>'','code'=>0,'id'=>0];
        $msg = '发布'.$msg;
        $release = new release();
        $release->startTrans();
        try {

            if(empty($data['id'])){
                $count = $release->where(['name'=>$data['name']])->count();
                if($count>0){
                   $res['msg'] = $msg.$name.'已存在,请重新输入';
                   $res['code'] = 3;
                   return json_encode($res,JSON_UNESCAPED_UNICODE);
                }
                $result = $release->save($data);
                $id = $release->id;
            }else{
                $count = $release->field('id,name')->where(['name'=>$data['name']])->find();
                if(!empty($count)&&$count['name']!=$data['name']){
                   $res['msg'] = $msg.$name.'已存在,请重新输入';
                   $res['code'] = 3;
                   return json_encode($res,JSON_UNESCAPED_UNICODE);
                }
                $save_id = $id = $data['id'];
                unset($data['id']);
                $result = $release->save($data,['id'=>$id]);
                $msg = '修改'.$msg;
            }

            //添加或修改标签库
            if(isset($data['effect'])&&!empty($data['effect'])){
                $arr = json_decode($data['effect'],true);
                foreach($arr as $k=>$v){
                  $row[$k]['release_id'] = $id;
                  $row[$k]['name'] = $v;
                  $row[$k]['addtime'] = time();
                }
                $effect = new Effect();
                if(isset($save_id)){
                   $effect->where('release_id',$id)->delete();
                }
                $effect->saveAll($row);
            }
            $release->commit();
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
            $release->rollback();
        }
        return json_encode($res,JSON_UNESCAPED_UNICODE);
    }

    /**
     * 获取样板信息
     */
    public function get_release($where,$state=0,$field='*',$limit=false,$is_del=0){
        $where['is_del'] = $is_del;
        $release = new release();
        if($state==0){
           $release_info = $release->field($field)->where($where)->find();
           if(!empty($release_info)){ //把取到信息的json数据转成数组
                  isset($release_info['alias'])? $release_info['alias'] = json_decode($release_info['alias'],true):''; //别名
                  isset($release_info['parameter'])? $release_info['parameter'] = json_decode($release_info['parameter'],true):''; //参数
                  isset($release_info['category'])? $release_info['category'] = json_decode($release_info['category'],true):''; //类别/适用工艺/材料大类
                  isset($release_info['material'])? $release_info['material'] = json_decode($release_info['material'],true):''; //材料
                  isset($release_info['consumables'])? $release_info['consumables'] = json_decode($release_info['consumables'],true):''; //耗材/样板编号/其他
                  isset($release_info['device'])? $release_info['device'] = json_decode($release_info['device'],true):''; //设备
                  isset($release_info['moulding_process'])? $release_info['moulding_process'] = json_decode($release_info['moulding_process'],true):''; //成型工艺
                  isset($release_info['surface_process'])? $release_info['surface_process'] = json_decode($release_info['surface_process'],true):''; //表面工艺
                  isset($release_info['join_process'])? $release_info['join_process'] = json_decode($release_info['join_process'],true):''; //连接工艺
                  isset($release_info['effect'])? $release_info['effect'] = json_decode($release_info['effect'],true):''; //标签
                  isset($release_info['reference'])? $release_info['reference'] = json_decode($release_info['reference'],true):''; //参考文献
                  
           }
       }elseif($state==1){
           if(!$limit){
               $release_info = $release->field($field)->where($where)->select();
           }else{
               $release_info = $release->field($field)->where($where)->limit($limit)->select();
           } 
           if(!empty($release_info)){ //把取到信息的json数据转成数组
               foreach($release_info as $key=>$val){
                    isset($release_info[$key]['alias'])? $release_info[$key]['alias'] = json_decode($release_info[$key]['alias'],true):''; //别名
                    isset($release_info[$key]['parameter'])? $release_info[$key]['parameter'] = json_decode($release_info[$key]['parameter'],true):''; //参数
                    isset($release_info[$key]['category'])? $release_info[$key]['category'] = json_decode($release_info[$key]['category'],true):''; //类别/适用工艺/材料大类
                    isset($release_info[$key]['material'])? $release_info[$key]['material'] = json_decode($release_info[$key]['material'],true):''; //材料

                    isset($release_info[$key]['consumables'])? $release_info[$key]['consumables'] = json_decode($release_info[$key]['consumables'],true):''; //耗材/样板编号/其他
                    isset($release_info[$key]['device'])? $release_info[$key]['device'] = json_decode($release_info[$key]['device'],true):''; //设备
                    isset($release_info[$key]['moulding_process'])? $release_info[$key]['moulding_process'] = json_decode($release_info[$key]['moulding_process'],true):''; //成型工艺
                    isset($release_info[$key]['surface_process'])? $release_info[$key]['surface_process'] = json_decode($release_info[$key]['surface_process'],true):''; //表面工艺

                    isset($release_info[$key]['join_process'])? $release_info[$key]['join_process'] = json_decode($release_info[$key]['join_process'],true):''; //连接工艺
                    isset($release_info[$key]['effect'])? $release_info[$key]['effect'] = json_decode($release_info[$key]['effect'],true):''; //标签
                    isset($release_info[$key]['reference'])? $release_info[$key]['reference'] = json_decode($release_info[$key]['reference'],true):''; //参考文献

               }
           }
       }
       
       return $release_info;
    }

}
