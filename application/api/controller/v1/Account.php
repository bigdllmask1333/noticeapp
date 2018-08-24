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

        trace('**************************************************************');
        trace($info);
        trace('**************************************************************');

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
                $retdata['id']=$datas['id'];
                $retdata['nickname']=checkNull($datas['nickname']);
                $retdata['mobile']=checkNull($datas['mobile']);
                $retdata['registerTime']=checkNull($datas['regist_time']);
                $retdata['email']=checkNull($datas['email']);
                $retdata['gender']=$datas['sex'];
                $retdata['birthday']=checkNull($datas['birthday']);
                $retdata['icon']=checkNull($datas['head_img']);
                $retdata['gender']=$info['token'];
                /*$retdata['password']=checkNull($datas['password']);
                $retdata['personality_signature']=checkNull($datas['personality_signature']);
                $retdata['age']=checkNull($datas['age']);
                $retdata['remind_times']=checkNull($datas['remind_times']);
                $retdata['update_time']=checkNull($datas['update_time']);*/

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

            if(isset($data['headImg'])){
                $intdata['head_img']=$data['headImg'];    /*头像*/
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

            if(!empty($intdata)){
                $retdata=$intdata;
            }else{
                $retdata='{}';
            }

            if($recheck){
                $ret['code']=1;
                $ret['message']='edit success';
                $ret['data']=$retdata;
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

            $code = $this->request->post('code');
            $password = $this->request->post('new_password');
            $password2 = $this->request->post('new_password2');
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
                        ->update(['password' => md5(md5($password))]);
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
    public function allBackgroundImg($id)
    {
        $data=array('http://notice.16820.com/backimg/1.jpg');

    }
}
