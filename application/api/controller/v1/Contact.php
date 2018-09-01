<?php
/**
 * Created by PhpStorm.
 * Date: 2018/08/18
 * Time: 13:23
 * For: 联系人（关系网）相关控制器
 */
namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use app\common\controller\Base;
use think\Db;

class Contact extends Base
{
	
    const TOKEN='token';//token 表  self::TOKEN
	
	const CONTACT='contact';
	
	const CONTACT_EXT='contact_ext';
	
	const CONTACT_RECORED='contact_record';
	
	const TAG='tag';
	
	const TAG_EXT='tag_ext';
	
	const TAG_RELATION='tag_relation';
	
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
	 
	    public function _initialize()
    {
//        parent::_initialize();
    }
	
    public function index()
    {
 
        //
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
            $ret['data']='{}';
            return json($ret);
        }
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function create()
    {

   	   	if (Request::instance()->isPost()){
        $info = Request::instance()->header();

		$this->checkToken($info['userid'],$info['token']);
		$contact_data=array();
		$contact_data['name'] = $this->request->post('name');
		$contact_data['first'] = getFirstCharter($contact_data['name']);
		$contact_data['gender'] = $this->request->post('gender');
		$contact_data['icon'] = $this->request->post('icon');
		$contact_data['remark'] = $this->request->post('remark');
		$contact_data['created_time'] = getMillisecond();
		$contact_data['birthday'] = intval($this->request->post('birthday'));
		$contact_data['address'] = $this->request->post('address');
		$contact_data['mobile'] = $this->request->post('mobile');
		$contact_data['qq'] = $this->request->post('qq');
		$contact_data['email'] = $this->request->post('email');
		// $contact_data['is_default_accept'] = $this->request->post('isDefaultAccept');
		$contact_data['user_id'] = $info['userid'];
		$tags = $this->request->post('tags');
		

		$custParams = $this->request->post('custParams');
		
		$contact_data['custParams']="";
		
		if(!empty($custParams)){
			
			
	    foreach($custParams as $key=>$value){
			
			$contact_data['custParams'].=$key."-".$value;
			
		}		
			
		}


		// $contact_data['extra'] = $this->request->post('extra');
		

		
		$contact_id=Db::name(self::CONTACT)->insertGetId($contact_data);
		
		if($contact_id){
			
		
		if(!empty($tags)){
			$tags=explode(',',$tags);
					 foreach($tags as $tag){
		
	 $tag_data=array();
	 
     $tag_data['user_id']=$info['userid'];

     $tag_data['object_id']=$contact_id;	 

     $tag_data['type']=2;
	 
	 $tag_data['tag_id']=$tag;

     $tag_data['relation_time']=getMillisecond();	
	

     Db::name(self::TAG_RELATION)->insert($tag_data);	 
	 
	 
		
		}
		
		
 
	 }	
	 $retdata="{}";
		
		}else{
		
		$retdata="{}";
			
		}
 
		 $ret['code']=1;
         $ret['message']='Not post passing value';
         $ret['data']= $retdata;
	 
		
		
		
		}else{
			
			 $ret['code']=0;
     $ret['message']='Not post passing value';
     $ret['data']='{}';		
		}
	   return json($ret);
    }
	
	
	    public function import()
    {
		
  	if (Request::instance()->isPost()) {
		
		$info = Request::instance()->header();
		
		$this->checkToken($info['userid'],$info['token']);
		
		$displayName = $this->request->post('displayName');
		$firstName = $this->request->post('firstName');
		$middleName = $this->request->post('middleName');
		$lastName = $this->request->post('lastName');
		$icon = $this->request->post('icon');
		$remark = $this->request->post('remark');
		$birthday = $this->request->post('birthday');
		$address = $this->request->post('address');
		$mobile = $this->request->post('mobile');
		// $tags = $this->request->post('tags');
		// $extra = $this->request->post('extra');
		
		$contact_data=array();
		
		$contact_data['display_name']=$displayName;
		$contact_data['first_name']=$firstName;
		$contact_data['middle_name']=$middleName;
		$contact_data['last_name']=$lastName;
		
		$contact_data['icon']=$icon;
		$contact_data['remark']=$remark;
		$contact_data['birthday']=$birthday;
		
		$contact_data['address']=$address;
		
		$contact_data['mobile']=$mobile;
		
		$contact_id=Db::name(self::CONTACT_RECORED)->insertGetId($contact_data);
		
		
     $ret['code']=1;
     $ret['message']='Not post passing value';
     $ret['data']='{}';	
		
	}else{
		
	 $ret['code']=0;
     $ret['message']='Not post passing value';
     $ret['data']='{}';	
	 
	}
	
	return json($ret);
	
	}


    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function detail()
    {
		
	if (Request::instance()->isPost()) {
		
		 $info = Request::instance()->header();
		
		$this->checkToken($info['userid'],$info['token']);
		
		$id = $this->request->post('id');
		
		$contact=Db::name(self::CONTACT)->where(array('id'=>$id))->find();
		
	if(empty($contact)){
		
		$retdata="{}";
	}else{
		
	$retdata['id']=$contact['id'];
$retdata['name']=$contact['name'];
$retdata['icon']=$contact['icon'];
$retdata['remark']=$contact['remark'];
$retdata['gender']=$contact['gender'];
$retdata['createdTime']=$contact['created_time'];
$retdata['birthday']=$contact['birthday'];
$retdata['address']=$contact['address'];
$retdata['mobile']=$contact['mobile'];
$retdata['email']=$contact['email'];
$retdata['isDefaultAccept']=$contact['is_default_accept'];
$retdata['qq']=$contact['qq'];
$retdata['tags']=array();
	 $tags=Db::name(self::TAG_RELATION)->where(array('user_id'=>$info['userid'],'object_id'=>$contact['id'],'type'=>2))->select();
 
	 if($tags){
	 $tagData=array();
		 foreach($tags as $tag){
	 $tagModel=Db::name(self::TAG)->where(array('id'=>$tag['tag_id']))->find();
 
 if(empty($tagModel)){ continue; }
 $tag_data=array();
     $tag_data['id']=$tagModel['id'];

     $tag_data['userId']=$tagModel['user_id'];
	 
	 $tag_data['title']=$tagModel['tag_name'];

     $tag_data['createdTime']=$tagModel['create_time'];	 
	 
	 $tagData[]=$tag_data;
		 
	 }
	 
 

  
	  $retdata['tags']=$tagData;
	  
	 }
	 
	
	 
 


$retdata['custParams']="";
$retdata['extra']="";
		
	}	
		 $ret['code']=1;
         $ret['message']='Not post passing value';
         $ret['data']= $retdata;
		 return json($ret);
		
		
	}else{
		
	 $ret['code']=0;
     $ret['message']='Not post passing value';
     $ret['data']='{}';		
		
	}
	
	return json($ret);
   
	
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update()
    {
       
	   	if (Request::instance()->isPost()) {
		
		 $info = Request::instance()->header();
		$this->checkToken($info['userid'],$info['token']);
		
		$user_id = $info['userid'];
			
		$contact_data=array();
		$id = $this->request->post('id');
		$contact_data['name'] = $this->request->post('name');
		$contact_data['icon'] = $this->request->post('icon');
		$contact_data['remark'] = $this->request->post('remark');
		$contact_data['gender'] = $this->request->post('gender');
		$contact_data['created_time'] = $this->request->post('createdTime');
		$contact_data['birthday'] = $this->request->post('birthday');
		$contact_data['address'] = $this->request->post('address');
		$contact_data['mobile'] = $this->request->post('mobile');
		$contact_data['is_default_accept'] = $this->request->post('isDefaultAccept');
		$tags = $this->request->post('tags');
		$custParams = $this->request->post('custParams');
		
		$contact_data['custParams']="";
		
		foreach($custParams as $key=>$value){
			
			$contact_data['custParams'].=$key."-".$value;
			
		}
		
		// $contact_data['extra'] = $this->request->post('extra');
		
		$contact_id=Db::name(self::CONTACT)->update($contact_data,$id);
	
		
		if($contact_id){
			
		$retdata=$contact_id;
		
		if(!empty($tags)){
			
	 $originTagIds=Db::name(self::TAG_RELATION)->fields('id')->where(array('user_id'=>$user_id,'object_id'=>$contact_id,'type'=>2))->select();	
	 
	 Db::name(self::TAG_RELATION)->delete($originTagIds);
	 $tags=explode(',',$tags);
	 foreach($tags as $tag){
		
	 $tag_data=array();
	 
     $tag_data['user_id']=$user_id;

     $tag_data['object_id']=$contact_id;	 

     $tag_data['type']=2;
	 
	 $tag_data['tag_id']=$tag;

     $tag_data['relation_time']=getMillisecond();	

     Db::name(self::TAG_RELATION)->insert($tag_data);	 
		 
	 }		
			
			
		}
		
$retdata=$contact_data;
 
		}else{
		
		$retdata="{}";
			
		}
 
		 $ret['code']=1;
         $ret['message']='Not post passing value';
         $ret['data']= $retdata;
	 
		
		
		
		}else{
			
			 $ret['code']=0;
     $ret['message']='Not post passing value';
     $ret['data']='{}';		
		}
	   return json($ret);
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete()
    {
    
		   	if (Request::instance()->isPost()) {
		
		 $info = Request::instance()->header();
		$this->checkToken($info['userid'],$info['token']);
		
		$id = $this->request->post('id');
		
		$user_id=$info['userid'];
		
		Db::name(self::CONTACT)->delete($id);
		
		$tags=Db::name(self::TAG_RELATION)->where(array('user_id'=>$user_id,'object_id'=>$id,'type'=>2));	 
		
		foreach($tags as $tag){
			
			Db::name(self::TAG_RELATION)->delete($tag);
			
		}
								 $ret['code']=1;
     $ret['message']='Not post passing value';
     $ret['data']='{}';	
		
			}else{
						 $ret['code']=0;
     $ret['message']='Not post passing value';
     $ret['data']='{}';	
				
			}
			 return json($ret);
	
    }
	
	    public function getTagsByUserId()
    {
 
       
	   	if (Request::instance()->isPost()) {
		
		 $info = Request::instance()->header();

		$this->checkToken($info['userid'],$info['token']);
		
		$user_id=$info['userid'];
 	

		$tags=Db::query("select tag_id,count(*) as num from fa_tag_relation where user_id='$user_id' and type=2 group by tag_id");	

	 
		if(!empty($tags)){
			
			foreach($tags as $key=>$tag){
				
				
		    $tagData=Db::name(self::TAG)->where(array('id'=>$tag['tag_id']))->find();
			
			$tags[$key]['tag_name']=$tagData['tag_name'];
				
			}
			
	 $ret['code']=1;
     $ret['message']='Not post passing value';
     $ret['data']=$tags;	
			
		}else{
			
	 $ret['code']=0;
     $ret['message']='无数据';
     $ret['data']='{}';		
			
		}

		
		
		}else{
							 $ret['code']=0;
     $ret['message']='Not post passing value';
     $ret['data']='{}';		
		}
		
		return json($ret);
		
		}
		
			    public function deleteTagByTagId()
    {
       
	   	if (Request::instance()->isPost()) {
		
		 $info = Request::instance()->header();
		 
		$this->checkToken($info['userid'],$info['token']);
		
		$tag_id = $this->request->post('tag_id');
		
		$tagData=Db::name(self::TAG_RELATION)->where(array('user_id'=>$info['userid'],'tag_id'=>$tag_id))->select();
		
		foreach($tagData as $tag){
			
	    Db::name(self::TAG_RELATION)->delete($tag['tag_id']);
			
		}
		
			 $ret['code']=1;
     $ret['message']='Not post passing value';
     $ret['data']='{}';	
		}else{
				 $ret['code']=0;
     $ret['message']='Not post passing value';
     $ret['data']='{}';	
			
		}
		return json($ret);
		}
		
	public function getContactByUserId(){
		
		       
	   	if (Request::instance()->isPost()) {
			
		$info = Request::instance()->header();
		
		$this->checkToken($info['userid'],$info['token']);

		
		$contactData=Db::name(self::CONTACT)->where(array('user_id'=>$info['userid']))->select();
		
		if(!empty($contactData)){
				$retdata=array();
		
		foreach($contactData as $key=>$val){
			
	    $retdata[$key]['id']=$val['id'];
		 $retdata[$key]['name']=$val['name'];
		  $retdata[$key]['first']=$val['first'];
		  $retdata[$key]['icon']=$val['icon'];
 
 
		  $retdata[$key]['mobile']=$val['mobile'];
 
 
		  
 
			
		}
		
		$res=array();
		
		foreach($retdata as $key=>$val){
			
			$res[$val['first']][]=$val;
			
		}
		
		ksort($res);
		
		$result=array();
		
		foreach($res as $k=>$v){
			
			   $result[]=array('first'=>$k,'content'=>$v);
 
		}
	

			 $ret['code']=1;
     $ret['message']='Not post passing value';
     $ret['data']=$result;			
			
		}else{
			
	 $ret['code']=0;
     $ret['message']='Not post passing value';
     $ret['data']='{}';	
	 
		}
		

		

		}else{
				 $ret['code']=0;
     $ret['message']='Not post passing value';
     $ret['data']='{}';	
			
		}
		return json($ret);
		
	}
	
		
		
}
