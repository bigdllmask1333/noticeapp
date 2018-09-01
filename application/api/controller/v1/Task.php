<?php

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\Base;
use think\Db;

class Task extends Base
{
    const TOKEN='token';//token 表  self::TOKEN
	
	const TASK='task';
	
	const TASK_EXT='task_ext';
	
	const TASK_COOPERATION='task_cooperation';
	
	const TASK_META='task_meta';
	
	const TASK_NOTE='task_note';
	
	const TAG='tag';
	
	const TAG_EXT='tag_ext';
	
	const TAG_RELATION='tag_relation';
 
    public function _initialize()
    {
//        parent::_initialize();
    }

    /*检测token是否有效*/
    public function checkToken($id,$token){
        $where['user_id']=$id;
        $where['token']=$token;
        $check=Db::name(self::TOKEN)->where($where)->order('createtime desc')->find();
        $time=getMillisecond();
        if($time>$check['expiretime']){
            $ret['code']=0;
            $ret['message']='token Invalid,Please login again';
            $ret['data']=null;
            return json($ret);
        }
    }


    /**
     * 显示创建标签资源表单页.
     *
     * @return \think\Response
     */
    public function getTaskList()
    {
        if (Request::instance()->isPost()) {
            $info = Request::instance()->header();
            $this->checkToken($info['userid'],$info['token']);

            $updateTime = $this->request->post('updateTime');
			
			$retdata=array();
			
			/* 未完成事项 */
			
	        $unfinished=Db::query('select * from fa_task as t right join fa_task_note as tn on t.id=tn.task_id where tn.status=0');
			
			if($unfinished){
			
            $res=array();
			
            foreach($unfinished as $key=>$val){
			
			$res[$key]['id']=$val['id'];
			
            $res[$key]['taskId']=$val['task_id'];			
				
			$res[$key]['noticeTime']=$val['notice_time'];

            $res[$key]['noticeVoice']=$val['notice_voice'];

            $res[$key]['noticeMode']=$val['notice_mode'];

            $res[$key]['status']=$val['status'];

            $res[$key]['extra']="{}";			
			
			}
				
			$retdata['unfinished']=$res;	
				
			}
			
			
			/* 已完成事项 */
			
            $finished=Db::query('select * from fa_task as t right join fa_task_note as tn on t.id=tn.task_id where tn.status=1');
            
			if($finished){
				
			$res=array();
			
            foreach($finished as $key=>$val){
			
			$res[$key]['id']=$val['id'];	
			
            $res[$key]['taskId']=$val['task_id'];			
				
			$res[$key]['noticeTime']=$val['notice_time'];

            $res[$key]['noticeVoice']=$val['notice_voice'];

            $res[$key]['noticeMode']=$val['notice_mode'];

            $res[$key]['status']=$val['status'];

            $res[$key]['extra']="{}";			
			
			}
				
			$retdata['finished']=$res;
			
			}
			
			
            if(empty($retdata)){
                $retdata=null;
            }

            $ret['code']=0;
            $ret['message']='返回成功';
            $ret['data']=$retdata;
        }else{
            $ret['code']=0;
            $ret['message']='无效操作';
            $ret['data']=null;
        }
		
        return json($ret);
    }
	
    /**
     * 显示资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */

    public function create()
    {

     if (Request::instance()->isPost()) {
		 
	  $info = Request::instance()->header();
		 
	 $this->checkToken($info['userid'],$info['token']);
	 
	 $is_cycle = $this->request->post('isCycle')?$this->request->post('isCycle'):0;
	 
	 $title = $this->request->post('title');
	 
	 $remark = $this->request->post('remark');
	 
	 $notice_time = $this->request->post('noticeTime');
	 
	 $tags = $this->request->post('tags');
	 
	 $notice_voice = $this->request->post('noticeVoice');
	 
	 $notice_mode = $this->request->post('noticeMode');
	 
	 $created_time = getMillisecond();
	 
	 $user_id = $info['userid'];
	
	
	 
	 /* 普通事务 */
 
	 if($is_cycle==0){
 
	 $tag_data=array('title'=>$title,'created_time'=>$created_time,'remark'=>$remark,'notice_time'=>$notice_time,'notice_voice'=>$notice_voice,'notice_mode'=>$notice_mode,'event_cycle'=>$is_cycle,'user_id'=>$user_id);

	 $task_id=Db::name(self::TASK)->insertGetId($tag_data);
	 
	 $tag_note_data=array('task_id'=>$task_id,'notice_time'=>$notice_time,'notice_voice'=>$notice_voice,'notice_mode'=>$notice_mode,'status'=>0);
 
	 Db::name(self::TASK_NOTE)->insert($tag_note_data);
 
	 }elseif($is_cycle==1){
		 
     /* 周期事务 */
     
	 $period_completetime = $this->request->post('periodCompleteTime');
	 
	 $period_duration = $this->request->post('periodDuration');
	 
     $period_noticetime = $this->request->post('periodNoticeTime'); 
	 
	 $period_type = $this->request->post('periodType');
	 
	 $is_custom = $this->request->post('isCustom');
	 
	 $custom_type = $this->request->post('customType');
	 
	 if($is_custom==0){
		
     $current_date=date('Y-m-d');
	 
	 $notice_time=strtotime($current_date." ".$notice_time." 00");

	 $notice_time=$notice_time*1000;	
	 
	 $tag_data=array('title'=>$title,'remark'=>$remark,'notice_time'=>$notice_time,'notice_voice'=>$notice_voice,'notice_mode'=>$notice_mode,'user_id'=>$user_id,'period_completetime'=>$period_completetime,'period_duration'=>$period_duration,'period_noticetime'=>$period_noticetime,'period_type'=>$period_type);
	 
	 $task_id=Db::name(self::TASK)->insert($tag_data);
 
	 $tag_note_data=array('task_id'=>$task_id,'notice_time'=>$notice_time,'notice_voice'=>$notice_voice,'notice_mode'=>$notice_mode,'status'=>0);
	 
	 Db::name(self::TASK_NOTE)->insert($tag_note_data);
		 
	 }elseif($is_custom==1){
		 
     $notice_time=time()*1000;  
				
	 }
		
		 
	 }
	 
	 if($tags){
		 
		 foreach($tags as $tag){
		
	 $tag_data=array();
	 
     $tag_data['user_id']=$user_id;

     $tag_data['object_id']=$task_id;	 

     $tag_data['type']=1;
	 
	 $tag_data['tag_id']=$tag;

     $tag_data['relation_time']=time();	

     Db::name(self::TAG_RELATION)->insert($tag_data);	 
		 
	 }	 
		 
	 }

	 
	 $ret['code']=1;
     $ret['message']='保存成功';
     $ret['data']=null;
		 
	 }else{
		 
     $ret['code']=0;
     $ret['message']='无效操作';
     $ret['data']= null;	
		
		 
	 }	
		
	return json($ret);
	
	}
	
    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit()
    {
		
	if (Request::instance()->isPost()) {
		$info = Request::instance()->header();
		    $this->checkToken($info['userid'],$info['token']);

            $id = $this->request->post('id');
			
			$task_note=Db::name(self::TASK_NOTE)->where(array('id'=>$id))->find();
			
			if(empty($task_note)){
				
				$retdata=null;
				
			}else{
			
			$task=Db::name(self::TASK)->where(array('id'=>$task_note['task_id']))->find();
				
			$retdata=array();
			
			$retdata['id']=$task_note['id'];
			
			$retdata['taskId']=$task_note['task_id'];
			
			
			$retdata['noticeTime']=$task_note['notice_time'];
			
			// $retdata['createdTime']=$task['created_time'];
			
			$retdata['noticeVoice']=$task_note['notice_voice'];
			
			$retdata['noticeMode']=$task_note['notice_mode'];
			
			// $retdata['type']=$task['type'];
			
			// $retdata['members']=$task['members'];
			
			// $retdata['tags']=$task['tags'];
			
			// $retdata['extra']=$task['extra'];
			
			$retdata['status']=$task_note['status'];
			
			 $retdata['extra']="{}";
			
		
				
			}
			
			    $ret['code']=1;
                $ret['message']='Not post passing value';
                $ret['data']= $retdata;
				return json($ret);
			
		
	}else{
            $ret['code']=0;
            $ret['message']='无效操作';
            $ret['data']='{}';
    }
	
    return json($ret);	
       
    }
	
	public function editDone(){
		
	     if (Request::instance()->isPost()) {
            $info = Request::instance()->header();
            $this->checkToken($info['userid'],$info['token']);

            $synchronization = $this->request->post('synchronization');
			$id = $this->request->post('id');
			$noticeTime = $this->request->post('noticeTime');
			$noticeVoice = $this->request->post('noticeVoice');
			$noticeMode = $this->request->post('noticeMode');
			$periodCompleteTime = $this->request->post('periodCompleteTime');
			$periodDuration = $this->request->post('periodDuration');
			
	        $task_note=Db::name(self::TASK_NOTE)->where(array('id'=>$id))->find();
 
            if(empty($task_note)){
				
                $retdata='{}';
				$ret['code']=0;
                $ret['message']='事务不存在';
                $ret['data']=null;
				return json($ret);
				
            }
			
		    if($synchronization==0){
						
			$task_update=array('notice_time'=>$noticeTime,'notice_voice'=>$noticeVoice,'notice_mode'=>$noticeMode,'period_completetime'=>$periodCompleteTime);
			
			$task_note_update=array('notice_time'=>$noticeTime,'notice_voice'=>$noticeVoice,'notice_mode'=>$noticeMode);
			
		    Db::name(self::TASK)->update($task_update,array('id'=>$task_note['task_id']));
			
			Db::name(self::TASK_NOTE)->update($task_note_update,array('id'=>$id));
			
            $taskObj=Db::name(self::TASK)->where(array('id'=>$task_note['task_id']))->find();
			
			$taskNoteObj=Db::name(self::TASK_NOTE)->where(array('id'=>$id))->find();

			$retdata=array();
			
			$retdata['id']=$id;
			
			$retdata['taskId']=$task_note['task_id'];
			
			$retdata['noticeTime']=$taskNoteObj['notice_time'];
			
			$retdata['noticeVoice']=$taskNoteObj['notice_voice'];
			
			$retdata['noticeMode']=$taskNoteObj['notice_mode'];
			
			$retdata['status']=$taskNoteObj['status'];
			
			$retdata['extra']="{}";
			
            // $retdata['task']=$taskObj;	

            // $taskNoteObj=Db::name(self::TASK_NOTE)->where(array('task_id'=>$task_note['task_id'],'status'=>0))->select();			

            // $retdata['taskNotes']=$taskNoteObj;			
				
			}elseif($synchronization==1){
				
				
			$retdata=null;
			
			}
 
			
			$ret['code']=1;
            $ret['message']='保存成功';
            $ret['data']=$retdata;


        }else{
            $ret['code']=0;
            $ret['message']='无效操作';
            $ret['data']=null;
        }
        return json($ret);
		
	}

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
		
	if (Request::instance()->isPost()) {
		
	$info = Request::instance()->header();
	
    $this->checkToken($info['userid'],$info['token']);

    $id = $this->request->post('id');	
	
	$type = $this->request->post('type');
	
	if($type==1){
	
    $task_note=Db::name(self::TASK_NOTE)->where(array('id'=>$id))->find();
	
	if(empty($task_note)){
		
	 $ret['code']=0;
     $ret['message']='事务不存在！';
     $ret['data']=null;
	 
	 return json($ret);
	 
	}
	
	$task=Db::name(self::TASK)->where(array('id'=>$task_note['task_id']))->find();
	 
	if($task['is_cycle']==0){
		
	Db::name(self::TASK)->where(array('id'=>$task['id']))->delete();	
	
	}
 
	}elseif($type==2){
		
	$task_note=Db::name(self::TASK_NOTE)->where(array('id'=>$id))->find();
	
	Db::name(self::TASK_NOTE)->where(array('id'=>$id))->delete();
	
	$unfinished=Db::name(self::TASK_NOTE)->where(array('task_id'=>$task_note['task_id'],'status'=>0))->select();	
	
	
	foreach($unfinished as $task){
	
    Db::name(self::TASK_NOTE)->where(array('id'=>$task['id']))->delete();	
		
	}
	
    Db::name(self::TASK)->where(array('id'=>$task_note['task_id']))->delete();
    	
		
	}
	
	 $ret['code']=1;
     $ret['message']='删除成功！';
     $ret['data']=null;
		
	}else{
		
	 $ret['code']=0;
     $ret['message']='无效操作';
     $ret['data']=null;
			
	}
	
	return json($ret);
    
    }
	
 
	
}
