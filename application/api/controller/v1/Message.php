<?php
/**
 * Created by PhpStorm.
 * Date: 2018/08/18
 * Time: 13:23
 * For: 消息中心
 */
namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use think\Db;

class Message extends Controller
{
    const ACCOUNT='account';// account 用户表   self::ACCOUNT
    const MESSAGE='message';// message 消息表   self::MESSAGE
    const TOKEN='token';//token 表  self::TOKEN


    /*检测token是否有效*/
    public function checkToken($id,$token){
        $where['user_id']=$id;
        $where['token']=$token;
        $check=Db::name(self::TOKEN)->where($where)->order('createtime desc')->find();
        if($check){
            $time=time();
            if($time>$check['expiretime']){
                return false;
            }else{
                return true;
            }
        }else{
            return false;
        }
    }


    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        if (Request::instance()->isPost()) {
            $info = Request::instance()->header();


            $cc=$this->checkToken($info['userid'],$info['token']);
            if(!$cc){
                $ret['code']=0;
                $ret['message']='token Invalid,Please login again';
                $ret['data']='{}';
                return json($ret);
            }

            $page = $this->request->post('page');
            $newpage=$page+1;
            $pageSize = $this->request->post('pageSize');

            $where['initiator_id']=$info['userid'];
            $where['is_del']=1;
            $list=Db::name(self::MESSAGE)->where($where)->order('id desc,read asc')->page($newpage,$pageSize)->select();

            $newslist=array();
            if($list){
                foreach ($list as $val){
                    $jk=array();
                    $jk['id']=$val['id'];      /*主键*/
                    $jk['title']=$val['title'];  /*标题*/
                    $jk['message']=$val['message'];  /*内容*/
                    $jk['type']=$val['type'];     /*消息类型*/
                    $jk['linkType']=$val['linktype'];   /*消息动作类型*/
                    $jk['params']=$val['params'];   /*消息动作参数*/
                    $jk['createTime']=$val['add_time'];   /*消息添加时间*/
                    $jk['isRead']=$val['read'];       /*消息是否已读*/
                    array_push($newslist,$jk);
                }
            }

            if(empty($newslist)){
                $newslist='{}';
            }

            $ret['code']=1;
            $ret['message']='query successful';
            $ret['data']=$newslist;

        }else{
            $ret['code']=0;
            $ret['message']='Not post passing value';
            $ret['data']='{}';
        }
        return json($ret);
    }




    /**
     * 修改消息状态.
     *
     * @return \think\Response
     */
    public function changeMsg()
    {
        if (Request::instance()->isPost()) {
            $info = Request::instance()->header();


            $cc=$this->checkToken($info['userid'],$info['token']);
            if(!$cc){
                $ret['code']=0;
                $ret['message']='token Invalid,Please login again';
                $ret['data']='{}';
                return json($ret);
            }

            $message_id = $this->request->post('message_id');
            $read = $this->request->post('read');
            $delete = $this->request->post('delete');

            if(isset($message_id)){
                if (isset($read)){
                    /*更新当前用户所有消息为已读*/

                }else{
                    if(isset($delete)){
                        /*删除当前用户所有消息*/
                    }
                }
            }else{

            }

        }else{
            $ret['code']=0;
            $ret['message']='Not post passing value';
            $ret['data']='{}';
        }
        return json($ret);
    }

    /**
     * 保存新建的资源
     *
     * @param  \think\Request  $request
     * @return \think\Response
     */
    public function save(Request $request)
    {
        //
    }

    /**
     * 显示指定的资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function read($id)
    {
        //
    }

    /**
     * 显示编辑资源表单页.
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * 保存更新的资源
     *
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * 删除指定资源
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function delete($id)
    {
        //
    }
}
