<?php
/**
 * Created by PhpStorm.
 * Date: 2018/08/18
 * Time: 13:23
 * For: 登录&验证码&忘记密码&注册&快捷登录
 */


namespace app\api\controller\v1;

use think\Controller;
use think\Request;
use fast\Random;
use think\Validate;
use think\Db;
use app\api\controller\Sms;

class User extends Controller
{
    const TOKEN='token';//token 表  self::TOKEN
    const ACCOUNT='account';// account 用户表   self::ACCOUNT
    const SMS='sms';// account 用户表   self::SMS

    /*获取token*/
    public function create_token($uid){
        $token = Random::uuid();
        $newinfo['token']=$token;
        $newinfo['user_id']=$uid;
        $newinfo['createtime']= time();
        $newinfo['expiretime']= strtotime('+1 day');

        $recheck=Db::name(self::TOKEN)->insert($newinfo);
        if($recheck){
            return true;
        }else{
            return false;
        }
    }
    /**
     * 登录
     * @return \think\Response
     */
    public function login()
    {
        if (Request::instance()->isPost()) {
            $mobile = $this->request->post('mobile');
            $password = $this->request->post('password');
            $validate = new Validate([
                'mobile'  => 'require|length:6,30',
//                'password'  => 'require|length:6,30',
            ]);
            $data = [
                'mobile'  => $mobile,
//                'password'  => $password,
            ];
            if (!$validate->check($data)) {
                $ret['code']=0;
                $ret['message']=$validate->getError();
                $ret['data']='{}';
            }else{
                $map['mobile'] = $mobile;
                $map['password'] =$password;
//                $map['password'] =md5(md5($password));
                $check=Db::name(self::ACCOUNT)->where($map)->find();
                if($check){
                    $retdata['id']=$check['id'];
                    $retdata['nickname']=checkNull($check['nickname']);
                    $retdata['mobile']=checkNull($check['mobile']);
                    $retdata['registerTime']=checkNull($check['regist_time']);
                    $retdata['email']=checkNull($check['email']);
                    $retdata['gender']=(int)$check['sex'];
                    $retdata['icon']=checkNull($check['head_img']);
                    $retdata['birthday']=checkNull($check['birthday']);

                    $this->create_token($check['id']);   /*添加一条token信息*/
//                    $tokeninfo1=Db::name(self::TOKEN)->where('user_id',$check['id'])->find();
                    $tokeninfo=Db::name(self::TOKEN)->where('user_id',$check['id'])->order('createtime desc')->find();
                    $retdata['token']=$tokeninfo['token'];

                    if(empty($retdata)){
                        $retdata='{}';
                    }

                    $ret['code']=1;
                    $ret['message']='login success';
                    $ret['data']=$retdata;
                }else{
                    $ret['code']=0;
                    $ret['message']='User name or password error';
                    $ret['data']='{}';
                }
            }
        }else{
            $ret['code']=0;
            $ret['message']='Not post passing value';
            $ret['data']='{}';
        }
        return json($ret);
    }
    /**
     * 快捷登录.
     *  TODD  快捷登录（没有账号就注册，有账号就登录） /用户注册  （都返回用户信息+token）
     * @return \think\Response
     */
    public function fastLogin()
    {
        if (Request::instance()->isPost()) {

            $mobile = $this->request->post('mobile');
            $code = $this->request->post('code');
//            $password = $this->request->post('password');   快速登录
            $checkmeminfo=Db::name(self::ACCOUNT)->where('mobile',$mobile)->find();

            if($checkmeminfo){
                /*用户存在,直接验证code,验证成功返回用户信息*/
                $where['mobile']=$mobile;
                $where['code']=$code;
                $where['type']=0;     /*0、快捷登录/注册；1、注册；2、找回密码；999、其他*/
//                $checksms=Db::name(self::SMS)->where($where)->find();
                $checksms=1;
                if($checksms){
                    $retdata['id']=$checkmeminfo['id'];
                    $retdata['nickname']=checkNull($checkmeminfo['nickname']);
                    $retdata['mobile']=checkNull($checkmeminfo['mobile']);
                    $retdata['registerTime']=checkNull($checkmeminfo['regist_time']);
                    $retdata['email']=checkNull($checkmeminfo['email']);
                    $retdata['gender']=(int)$checkmeminfo['sex'];
                    $retdata['icon']=checkNull($checkmeminfo['head_img']);
                    $retdata['birthday']=checkNull($checkmeminfo['birthday']);

                    $this->create_token($checkmeminfo['id']);   /*添加一条token信息*/
                    $tokeninfo=Db::name(self::TOKEN)->where('user_id',$checkmeminfo['id'])->order('createtime desc')->find();
                    $retdata['token']=$tokeninfo['token'];

                    if(empty($retdata)){
                        $retdata='{}';
                    }

                    $ret['code']=1;
                    $ret['message']='login success';
                    $ret['data']=$retdata;
                }else{
                    /*验证码错误*/
                    $ret['code']=0;
                    $ret['message']='Verification code error';
                    $ret['data']='{}';
                }
            }else{
                /*用户不存在，直接走注册渠道*/
                /*数据简单验证*/
                $validate = new Validate([
                    'mobile'  => 'require|length:6,30',
//                    'password'  => 'require|length:6,30',
                    'code'  => 'require|length:0,6',
                ]);
                $data = [
                    'mobile'  => $mobile,
//                    'password'  => $password,
                    'code'  => $code,
                ];
                if (!$validate->check($data)) {
                    /*基本规则验证失败*/
                    $ret['code']=0;
                    $ret['message']=$validate->getError();
                    $ret['data']='{}';
                }else{
                    /*先验证验证码*/
                    $where['mobile']=$mobile;
                    $where['code']=$code;
                    $where['type']=0;     /*0、快捷登录/注册；1、注册；2、找回密码；999、其他*/
//                    $checksms=Db::name(self::SMS)->where($where)->find();
                    $checksms=1;
                    if($checksms){
                        /*基本规则验证成功后再往下走*/
                        $check=Db::name(self::ACCOUNT)->where('mobile',$mobile)->find();
                        if($check){
                            $ret['code']=1;
                            $ret['message']='User already exists';
                            $ret['data']='{}';
                        }else{
                            /*正式注册位置*/
                            $newinfo['mobile']=$mobile;
//                            $newinfo['password']=md5(md5($password));
                            $newinfo['regist_time']= getMillisecond();
                            $newinfo['update_time']= getMillisecond();
                            $newinfo['nickname']= generate_username();

                            $recheck=Db::name(self::ACCOUNT)->insert($newinfo);
                            if($recheck){

                                $searchinfo=Db::name(self::ACCOUNT)->where('mobile',$mobile)->find();

                                $retdata['id']=$searchinfo['id'];
                                $retdata['nickname']=checkNull($searchinfo['nickname']);
                                $retdata['mobile']=checkNull($searchinfo['mobile']);
                                $retdata['registerTime']=checkNull($searchinfo['regist_time']);
                                $retdata['email']=checkNull($searchinfo['email']);
                                $retdata['gender']=(int)$searchinfo['sex'];
                                $retdata['icon']=checkNull($searchinfo['head_img']);
                                $retdata['birthday']=checkNull($searchinfo['birthday']);

                                $this->create_token($searchinfo['id']);   /*添加一条token信息*/
                                $tokeninfo=Db::name(self::TOKEN)->where('user_id',$searchinfo['id'])->order('createtime desc')->find();
                                $retdata['token']=$tokeninfo['token'];

                                if(empty($retdata)){
                                    $retdata='{}';
                                }

                                $ret['code']=1;
                                $ret['message']='login success';
                                $ret['data']=$retdata;
//                                //注册数据插入完成，返回用户信息

                            }else{
                                $ret['code']=0;
                                $ret['message']='Insert failure';
                                $ret['data']='{}';
                            }
                        }
                    }else{
                        /*验证码错误*/
                        $ret['code']=0;
                        $ret['message']='Verification code error';
                        $ret['data']='{}';
                    }
                }
            }
        }else{
            $ret['code']=0;
            $ret['message']='Not post passing value';
            $ret['data']='{}';
        }
        return json($ret);
    }

    /*
     * 注册  type=1
     * 0、快捷登录/注册；1、注册；2、找回密码；999、其他
     */
    public function register(){
        if (Request::instance()->isPost()) {
            $mobile = $this->request->post('mobile');
            $code = $this->request->post('code');
            $password = $this->request->post('password');
            $checkmeminfo=Db::name(self::ACCOUNT)->where('mobile',$mobile)->find();

            if($checkmeminfo){
                $ret['code']=0;
                $ret['message']='The account has been registered';
                $ret['data']=null;
            }else{
                /*用户不存在，直接注册*/
                /*用户不存在，直接走注册渠道*/
                /*数据简单验证*/
                $validate = new Validate([
                    'mobile'  => 'require|length:6,30',
//                    'password'  => 'require|length:6,30',
                    'code'  => 'require|length:0,6',
                ]);
                $data = [
                    'mobile'  => $mobile,
//                    'password'  => $password,
                    'code'  => $code,
                ];
                if (!$validate->check($data)) {
                    /*基本规则验证失败*/
                    $ret['code']=0;
                    $ret['message']=$validate->getError();
                    $ret['data']='{}';
                }else{
                    /*先验证验证码*/
                    $where['mobile']=$mobile;
                    $where['code']=$code;
                    $where['type']=1;     /*0、快捷登录/注册；1、注册；2、找回密码；999、其他*/
//                    $checksms=Db::name(self::SMS)->where($where)->find();

                    $checksms=1;
                    if($checksms){
                        /*正式注册位置*/
                        $newinfo['mobile']=$mobile;
                        $newinfo['password']=$password;
//                        $newinfo['password']=md5(md5($password));
                        $newinfo['regist_time']= getMillisecond();
                        $newinfo['update_time']= getMillisecond();
                        $newinfo['nickname']= generate_username();
                        $recheck=Db::name(self::ACCOUNT)->insert($newinfo);
                        if($recheck){

                            $searchinfo=Db::name(self::ACCOUNT)->where('mobile',$mobile)->find();

                            $retdata['id']=$searchinfo['id'];
                            $retdata['nickname']=checkNull($searchinfo['nickname']);
                            $retdata['mobile']=checkNull($searchinfo['mobile']);
                            $retdata['registerTime']=checkNull($searchinfo['regist_time']);
                            $retdata['email']=checkNull($searchinfo['email']);
                            $retdata['gender']=(int)$searchinfo['sex'];
                            $retdata['icon']=checkNull($searchinfo['head_img']);
                            $retdata['birthday']=checkNull($searchinfo['birthday']);

                            $this->create_token($searchinfo['id']);   /*添加一条token信息*/
                            $tokeninfo=Db::name(self::TOKEN)->where('user_id',$searchinfo['id'])->order('createtime desc')->find();
                            $retdata['token']=$tokeninfo['token'];

                            if(empty($retdata)){
                                $retdata='{}';
                            }

                            $ret['code']=1;
                            $ret['message']='login was successful';
                            $ret['data']=$retdata;
//                                //注册数据插入完成，返回用户信息

                        }else{
                            $ret['code']=0;
                            $ret['message']='Insert failure';
                            $ret['data']='{}';
                        }
                    }else{
                        /*验证码错误*/
                        $ret['code']=0;
                        $ret['message']='Verification code error';
                        $ret['data']='{}';
                    }
                }
            }
        }else{
            $ret['code']=0;
            $ret['message']='Not post passing value';
            $ret['data']='{}';
        }
        return json($ret);
    }

    /*
     * 忘记密码
     */
    public function forgetPassword(){
        if (Request::instance()->isPost()) {
            $mobile = $this->request->post('mobile');
            $code = $this->request->post('code');
            $password = $this->request->post('password');
            $checkmeminfo = Db::name(self::ACCOUNT)->where('mobile', $mobile)->find();
            if($checkmeminfo){
                /*先验证验证码*/
                $where['mobile']=$mobile;
                $where['code']=$code;
                $where['type']=2;     /*0、快捷登录/注册；1、注册；2、找回密码；999、其他*/
//                $checksms=Db::name(self::SMS)->where($where)->find();
                $checksms=1;
                if($checksms){
                    /*在这更新密码*/
                    $updates=Db::name(self::ACCOUNT)
                        ->where('mobile', $mobile)
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
            /*都不是post传值*/
            $ret['code']=0;
            $ret['message']='Not post passing value';
            $ret['data']='{}';
        }
        return json($ret);
    }

    /*
     * 获取手机验证码
     * $mobile   手机号码
     * $type     验证码类型
     */
    public function getCode(){
        if (Request::instance()->isPost()) {
            $mobile = $this->request->post('mobile');
            $type = $this->request->post('type');   /*0、快捷登录/注册；1、注册；2、找回密码；999、其他*/

            //60秒内只能读一次
            $where['mobile']=$mobile;
            $where['mobile']=$mobile;
            $time=3600*24;
            $lasttime=time()+$time;
            $checksms=Db::name(self::SMS)
                ->order('createtime desc')
                ->where('mobile',$mobile)
                ->where('createtime','lt',$lasttime)
                ->select();

            if($checksms){
                if(time()-$checksms[0]['createtime']<60){
                    $ret['code']=0;
                    $ret['message']='The frequency of the verification code is too fast';
                    $ret['data']='{}';
                }else if(count($checksms)>10){
                    $ret['code']=0;
                    $ret['message']='You can only send 3 SMS verification codes within 24 hours';
                    $ret['data']='{}';
                }else{
                    $ret=$this->smsobj($mobile,$type);
                }
            }else{
                $ret=$this->smsobj($mobile,$type);
            }
        }else{
            $ret['code']=0;
            $ret['message']='Not post passing value';
            $ret['data']='{}';
        }
        return json($ret);
    }
    /*封装了一个验证码发送类*/
    public function smsobj($mobile,$type){
        $code=mt_rand(000000,999999);
        $msg='尊敬的用户，您的验证码为：'.$code.'，工作人员不会索取，请勿泄露';
        $cc=new Sms();  /*不写静态方法就必须实例化*/
        $ccs=$cc->sendSMS( $mobile, $msg, $needstatus = 'true');
        if($ccs){
            $smsdata['mobile']=$mobile;
            $smsdata['type']=$type;
            $smsdata['createtime']= time();
            $smsdata['code']= $code;
            Db::name(self::SMS)->insert($smsdata);  /*记录数据插入*/

            $ret['code']=1;
            $ret['message']='Authentication code sent successfully';
            $ret['data']='{}';
        }else{
            $ret['code']=0;
            $ret['message']='Authentication code sent failure';
            $ret['data']='{}';
        }
        return $ret;
    }
}
