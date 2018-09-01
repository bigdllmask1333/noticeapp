<?php
/**
 * Created by PhpStorm.
 * Date: 2018/08/18
 * Time: 13:23
 * For: 标签控制器
 */
namespace app\api\controller\v1;

use think\Request;
use app\common\controller\Base;
use think\Db;

class Tag extends Base
{
    const TOKEN='token';//token 表  self::TOKEN
    const TAG='tag';//tag 表  self::TAG

    public function _initialize()
    {
//        parent::_initialize();
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
     * 显示创建标签资源表单页.
     *
     * @return \think\Response
     */
    public function createTag()
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

            $tag = $this->request->post('tag');
            $map['tag_name'] = $tag;
            $map['user_id'] =$info['userid'];
            $check=Db::name(self::TAG)->where($map)->find();

            if(!$check){
                $newinfo['tag_name']=$tag;
                $newinfo['user_id']= $info['userid'];
                $newinfo['create_time']= getMillisecond();
                $newinfo['update_time']= getMillisecond();
                $cc=Db::name(self::TAG)->insert($newinfo);
                if($cc){
                    $ret['code']=1;
                    $ret['message']='insert success';
                    $ret['data']='{}';
                }else{
                    $ret['code']=0;
                    $ret['message']='insert error';
                    $ret['data']='{}';
                }
            }


//            $tags = $this->request->post('tags');
//            $newtags=json_decode($tags,'true');
            /*if(empty($newtags)){
                $ret['code']=0;
                $ret['message']='tag value can not be empty';
                $ret['data']='{}';
            }else{
                $rettag=array();
                foreach ($newtags as $val){
                    $map['tag_name'] = $val;
                    $map['user_id'] =$info['userid'];
                    $check=Db::name(self::TAG)->where($map)->find();
                    if(!$check){
                        $newinfo['tag_name']=$val;
                        $newinfo['user_id']= $info['userid'];
                        $newinfo['create_time']= getMillisecond();
                        $newinfo['update_time']= getMillisecond();
                        Db::name(self::TAG)->insert($newinfo);
                        array_push($rettag,$val);
                    }
                }

                if(empty($rettag)){
                    $rettag='{}';
                    $ret['code']=1;
                    $ret['message']='The submitted data exists or is submitted empty';
                    $ret['data']=$rettag;
                }else{
                    $ret['code']=1;
                    $ret['message']='Label added successfully';
                    $ret['data']=$rettag;
                }
            }*/
        }else{
            $ret['code']=0;
            $ret['message']='Not post passing value';
            $ret['data']='{}';
        }
        return json($ret);
    }

    public function ss(){
        $dd=array('familay','classmate','collage');
        return json_encode($dd);
    }

    /**
     * 获取标签列表.
     *
     * @return \think\Response
     */
    public function tagList(){
        if (Request::instance()->isPost()) {
            $info = Request::instance()->header();


            $cc=$this->checkToken($info['userid'],$info['token']);
            if(!$cc){
                $ret['code']=0;
                $ret['message']='token Invalid,Please login again';
                $ret['data']='{}';
                return json($ret);
            }



            $datas=Db::name(self::TAG)->where('user_id',$info['userid'])->select();
            $rettag=array();
            foreach ($datas as $val){
                $temporary['id']=$val['id'];
                $temporary['userId']=$val['user_id'];
                $temporary['title']=$val['tag_name'];
                $temporary['createdTime']=$val['create_time'];
                array_push($rettag,$temporary);
            }

            if(empty($rettag)){
                $ret['code']=1;
                $ret['message']='Not post passing value';
                $ret['data']='{}';
                return json($ret);
            }else{
                $ret['code']=1;
                $ret['message']='query was successful';
                $ret['data']=$rettag;
                return json($ret);
            }


        }else{
            $ret['code']=0;
            $ret['message']='Not post passing value';
            $ret['data']='{}';
        }
        return json($ret);
    }
}
