<?php
/**
 * Created by PhpStorm.
 * Date: 2018/08/18
 * Time: 13:23
 * For: 个人中心
 */
namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use think\Db;

class Account extends Controller
{
    const ACCOUNT='account';// account 用户表   self::ACCOUNT
    const TOKEN='token';//token 表  self::TOKEN
    const SMS='sms';// account 用户表   self::SMS
    const BASE_URL='http://notice.16820.com';// BASE_URL 基础url   self::BASE_URL
    const DEVICE='device';// device 设备信息表   self::DEVICE
    const BACKIMG='backimg';// backimg  背景图片   self::BACKIMG


    public function __construct(Request $request = null)
    {
        // 指定允许其他域名访问
        header('Access-Control-Allow-Origin:*');
        // 响应类型
        header('Access-Control-Allow-Methods:POST');
        // 响应头设置
        header('Access-Control-Allow-Headers:x-requested-with,content-type');

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


    /*
     * 设备号-用户名
     */
    public function device($info){


        $where['deviceId']=$info['deviceid'];
        $where['userid']=$info['userid'];
        $check=Db::name(self::DEVICE)->where($where)->find();
        if(!$check){
            $where['create_time']=time();
            Db::name(self::DEVICE)->insert($where);
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

            $this->device($info);  /*设备号绑定*/


            $cc=$this->checkToken($info['userid'],$info['token']);
            if(!$cc){
                $ret['code']=0;
                $ret['message']='token Invalid,Please login again';
                $ret['data']='{}';
                return json($ret);
            }

            $datas=Db::name(self::ACCOUNT)->where('id',$info['userid'])->find();

            if($datas){
//                $backid=$datas['backimg'];
//                $backimg=Db::name(self::BACKIMG)->where('id',$backid)->find();
                $retdata['id']=$datas['id'];
                $retdata['nickname']=checkNull($datas['nickname']);
                $retdata['mobile']=checkNull($datas['mobile']);
                $retdata['registerTime']=checkNull($datas['regist_time']);
                $retdata['email']=checkNull($datas['email']);
                $retdata['gender']=$datas['sex'];
                $retdata['birthday']=checkNull($datas['birthday']);
                $retdata['icon']=checkNull($datas['head_img']);
                $retdata['token']=$info['token'];
                $retdata['backurl']=checkNull($datas['backimg']);

//                if($backimg){
//                    $retdata['backurl']=$backimg['url'];
//                }else{
//                    $retdata['backurl']="";
//                }
                $ret['code']=1;
                $ret['message']='query was successful';
                $ret['data']=$retdata;
            }else{
                $ret['code']=0;
                $ret['message']='query error';
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
     * 保存更新的资源
     * 涉及到图片上传
     * @param  \think\Request  $request
     * @param  int  $id
     * @return \think\Response
     */
    public function update()
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

            /*TODD
            下面是post传参，更新数据（性别，生日，昵称（真实姓名怎么弄））
            */
            $data=Request::instance()->post();  /*获取所有数据*/
            $intdata=array();
            if(isset($data['hadeImg'])){
                $intdata['head_img']=$data['hadeImg'];    /*头像*/
            }
            if(isset($data['sex'])){
                $intdata['sex']=$data['sex'];    /*性别*/
            }
            if(isset($data['birthday'])){
                $intdata['birthday']=$data['birthday'];   /*生日*/
            }
            if(isset($data['nickname'])){
                $intdata['nickname']=$data['nickname'];   /*昵称*/
            }
            /*正式注册位置*/

            $recheck=Db::name(self::ACCOUNT)->where('id', $info['userid'])->update($intdata);


            $datas=Db::name(self::ACCOUNT)->where('id',$info['userid'])->find();
//            $backid=$datas['backimg'];
//            $backimg=Db::name(self::BACKIMG)->where('id',$backid)->find();
            $retdatas['id']=$datas['id'];
            $retdatas['nickname']=checkNull($datas['nickname']);
            $retdatas['mobile']=checkNull($datas['mobile']);
            $retdatas['registerTime']=checkNull($datas['regist_time']);
            $retdatas['email']=checkNull($datas['email']);
            $retdatas['gender']=$datas['sex'];
            $retdatas['birthday']=checkNull($datas['birthday']);
            $retdatas['icon']=checkNull($datas['head_img']);
            $retdatas['token']=$info['token'];
            $retdatas['backurl']=checkNull($datas['url']);
//            if($backimg){
//                $retdatas['backurl']=$backimg['url'];
//            }else{
//                $retdatas['backurl']="";
//            }


            if(!empty($retdatas)){
                $rets=$retdatas;
            }else{
                $rets='{}';
            }

            if($recheck){
                $ret['code']=1;
                $ret['message']='edit success';
                $ret['data']=$rets;
            }else{
                $ret['code']=0;
                $ret['message']='edit error';
                $ret['data']='{}';
            }
        }else{
            $ret['code']=0;
            $ret['message']='Not post passing value';
            $ret['data']='{}';
        }
        return json($ret);
    }

    /*
     * 修改密码
     */
    public function changePsw(){

        if (Request::instance()->isPost()) {
            $info = Request::instance()->header();

            $cc=$this->checkToken($info['userid'],$info['token']);
            if(!$cc){
                $ret['code']=0;
                $ret['message']='token Invalid,Please login again';
                $ret['data']='{}';
                return json($ret);
            }

            $code = input('post.code');
            $password = input('post.new_password');
            $password2 = input('post.new_password2');
            if($password!=$password2){
                $ret['code']=0;
                $ret['message']='Two password inconsistency';
                $ret['data']='{}';
                return json($ret);
            }

            $checkmeminfo = Db::name(self::ACCOUNT)->where('id',$info['userid'])->find();

            if($checkmeminfo){

                /*先验证验证码*/
                $where['mobile']=$checkmeminfo['mobile'];
                $where['code']=$code;
                $where['type']=3;     /*0、快捷登录/注册；1、注册；2、找回密码；3,忘记密码 999、其他*/


                $checksms=Db::name(self::SMS)->where($where)->find();

                if($checksms){
                    /*在这更新密码*/
                    $updates=Db::name(self::ACCOUNT)
                        ->where('id', $info['userid'])
                        ->update(['password' => $password]);
                    if($updates){
                        $ret['code']=1;
                        $ret['message']='Update success';
                        $ret['data']='{}';
                    }else{
                        $ret['code']=0;
                        $ret['message']='Update error';
                        $ret['data']='{}';
                    }
                }else{
                    /*验证码错误*/
                    $ret['code']=0;
                    $ret['message']='Verification code error';
                    $ret['data']='{}';
                }
            }else{
                /*用户不存在还改屁的密码*/
                $ret['code']=0;
                $ret['message']='user does not exist';
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
     * 头像上传
     *
     * @return \think\Response
     */
    public function upimg()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        // 移动到框架应用根目录/public/uploads/ 目录下
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
                $str=$info->getSaveName();
                $fg= substr($str , 8 , 1); /*分隔符*/
                $cc1=explode($fg,$str); /*分割结果*/
//                echo 'http://apis.com/'.'uploads/'.$cc1[0].'/'.$cc1[1];
                $retimg='/uploads/'.$cc1[0].'/'.$cc1[1];

                $baseurl=self::BASE_URL;

                $ret['code']=1;
                $ret['message']='success';
                $ret['data']=$baseurl.$retimg;

            }else{
                $ret['code']=0;
                $ret['message']='error';
                $ret['data']=$file->getError();
                // 上传失败获取错误信息
            }
        }else{
            $ret['code']=0;
            $ret['message']='not file';
            $ret['data']='{}';
        }

        return json($ret);
        //
    }

    /**
     * 获取所有背景图片
     *
     * @param  int  $id
     * @return \think\Response
     */
    public function allBackgroundImg()
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

            /*查询数据库的背景图片*/
            $where['id']=$info['userid'];
            $infos=Db::name(self::ACCOUNT)->where($where)->find();


            $retimg=array();
            $data=Db::name(self::BACKIMG)->where([])->select();
            foreach ($data as $val){
                if($val['url']==$infos['backimg']){
                    $val['isSelected']=1;
                }else{
                    $val['isSelected']=0;
                }
                array_push($retimg,$val);
            }


            if(empty($retimg)){
                $retimg='{}';
            }

            $ret['code']=1;
            $ret['message']='success';
            $ret['data']=$retimg;

        }else{
            $ret['code']=0;
            $ret['message']='Not post passing value';
            $ret['data']='{}';
        }
        return json($ret);

    }

    /*设置背景图片*/
    public function setBackimg(){
        if (Request::instance()->isPost()) {
            $info = Request::instance()->header();

            $cc=$this->checkToken($info['userid'],$info['token']);
            if(!$cc){
                $ret['code']=0;
                $ret['message']='token Invalid,Please login again';
                $ret['data']='{}';
                return json($ret);
            }


            $backimgurl=input('post.backurl');

            /*在这个地方修改用户信息的背景图片*/
            $check=Db::name(self::ACCOUNT)->where('id', $info['userid'])->update(['backimg' => $backimgurl]);
            if($check){
                $ret['code']=1;
                $ret['message']='edit success';
                $ret['data']='{}';
            }else{
                $ret['code']=0;
                $ret['message']='no you do error';
                $ret['data']='{}';
            }
        }else{
            $ret['code']=0;
            $ret['message']='Not post passing value';
            $ret['data']='{}';
        }
        return json($ret);
    }

    /*静态页面H5跳转*/
    public function h5page(){
        $data=array();
        $data['fwtk']['name']='服务条款';
        $data['fwtk']['url']='http://www.baidu.com';

        $data['yhxy']['name']='用户协议';
        $data['yhxy']['url']='http://www.baidu.com';

        $data['rmzn']['name']='入门指南';
        $data['rmzn']['url']='http://www.baidu.com';

        $data['jjjq']['name']='进阶技巧';
        $data['jjjq']['url']='http://www.baidu.com';

        $data['xsscxxp']['name']='新手手册学习篇';
        $data['xsscxxp']['url']='http://www.baidu.com';


        $data['sjgljjp']['name']='时间管理进阶篇';
        $data['sjgljjp']['url']='http://www.baidu.com';

        $data['wfsdtx']['name']='无法收到提醒？';
        $data['wfsdtx']['url']='http://www.baidu.com';

        $data['gnjs']['name']='功能介绍？';
        $data['gnjs']['url']='http://www.baidu.com';

        $data['wwmpf']['name']='为我们评分';
        $data['wwmpf']['url']='http://www.baidu.com';

        $ret['code']=1;
        $ret['message']='success';
        $ret['data']=$data;

        return json($ret);

    }
}
