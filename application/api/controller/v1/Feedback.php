<?php

/**
 * Created by PhpStorm.
 * Date: 2018/08/18
 * Time: 13:23
 * For: 意见反馈
 */

namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use think\Db;

class Feedback extends Controller
{


    const ACCOUNT='account';// account 用户表   self::ACCOUNT
    const TOKEN='token';//token 表  self::TOKEN
    const FEEDBACK='feedback';// feedback 意见反馈表   self::FEEDBACK
    const BASE_URL='http://notice.16820.com';// BASE_URL 基础url   self::BASE_URL
    const DEVICE='device';// device 设备信息表   self::DEVICE
    /**
     * 显示资源列表
     *
     * @return \think\Response
     */
    public function index()
    {
        $code = input('post.code');
        trace($code);
    }


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
     * 显示反馈列表.
     *
     * @return \think\Response
     */
    public function createData()
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

            $content = input('post.content');
            $phone = input('post.phone');

            $stccc = sstrlen($content,'utf-8');
            $stddd = sstrlen($phone,'utf-8');
            if($stccc>200){
                $ret['code']=0;
                $ret['message']='Feedback content should not exceed 200';
                $ret['data']='{}';
                return json($ret);
            }

            if($stddd>50){
                $ret['code']=0;
                $ret['message']='Contact should not exceed 50';
                $ret['data']='{}';
                return json($ret);
            }

            $newinfo['content']=$content;
            $newinfo['coninfo']=$phone;
            $newinfo['create_time']= time();
            $recheck=Db::name(self::FEEDBACK)->insert($newinfo);

            if($recheck){
                $ret['code']=1;
                $ret['message']='insert success';
                $ret['data']='{}';
            }else{
                $ret['code']=0;
                $ret['message']='no fuck error';
                $ret['data']='{}';
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
