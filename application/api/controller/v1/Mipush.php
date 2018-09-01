<?php
/**
 * Created by PhpStorm.
 * Date: 2018/8/31
 * Time: 14:01
 */

namespace app\api\controller\v1;

use think\Controller;
use think\Db;
use think\Request;

class Mipush   extends Controller
{
    const  MIPUSH_MESSAGE ='xb_mipush_message';       //测试表*这个表不YYYYYYYY能随便删除*
    const TEST = 'xb_test';
    const T_MEMBERS ='xb_device_info';       //设备表—用户表
    const key  ='c52f2e43cc7d5875a1b13c607d7640d5';            //应用key
    const tableid  ='5b3b03a5305a2a66889d7c31';                //表ID

    public function __construct($key='', $tableid='') {
        header("Content-type:text/html;charset=utf-8");
    }

    /*查询当前设备周边指定半径的设备并推送数据消息*/
    public function mipush(){
        $pushdata='';
        $this->iospush($pushdata);
        $this->androidpush($pushdata);

        $res_data['status'] = 1;
        $res_data['message'] = 'ok';
        $res_data['data'] =$pushdata;    //发送对象
        $res_data['pushresult'] ="ok";   //发送结果
        header('Content-type: application/json');
        return json($res_data);
    }

    /*IOS推送*/
    //    http://order.16820.com/Amaplibrary/iospush
    public function iospush($pushdata,$desc){
        Vendor('sdk.iospush');
        $push = new \sdk\iospush();
        /*TODD，在这找到对应的订单！然后拼接返回*/
        $where['id']=1;
        $resdata = Db::table('xb_borrow')->where($where)->find();
//        $desc = '亲，附近有人发起了借款申请';
        $res=array();
        $res['type']=1;   /*1 借款  2 贷款*/
        $res['id']=1;      /*auditstate审核通过1*/
        $payload = json_encode($res);
        return $push->index($pushdata,$desc,$payload);
    }
    /*安卓推送*/
    public function androidpush($pushdata){
        Vendor('sdk.androidpush');
        $push = new \sdk\androidpush();
        $title = '新消息';
        $desc = "附近有人发起了借款申请";
        $where['id']=1;
        $resdata = Db::table('xb_borrow')->where($where)->find();
        $payload = json_encode($resdata);     //携带的数据
//        $payload = '{"content":"88888888888888888888"}';     //携带的数据
        return $push->index($pushdata,$title,$desc,$payload);

    }


    //检测消息发送情况
    public function checkStaues(){
        $id     = addslashes(I("post.id", '', 'trim'));
        $url='https://api.xmpush.xiaomi.com/v1/trace/message/status';
        $datag['msg_id']=$id;
        $data=$this->https_request($url,$datag,true);

        $res_data['status'] = 1;
        $res_data['message'] = 'ok';
        $res_data['data'] =$data;    //发送对象
        header('Content-type: application/json');
        return json($res_data);
    }

    /**
     * curl
     *
     * @param      <type>  $url    The url
     * @param      <type>  $data   The data
     * @param      <type>  $isget  是否为get请求
     *
     * @return     <type>  ( description_of_the_return_value )
     */
    public function https_request($url, $data = null, $isget = false)
    {
        $headers = array(
            'Cache-Control:no-cache',
            'Authorization:key=UOV5CfPdJNblE6Xa5mc79Q==',
            'Pragma:no-cache'
        );

        if($isget){
            $url .= '?';
            if(is_array($data)){
                $url .= http_build_query($data);
            }elseif(is_string($data)) {
                $url .= $data;
            }
        }
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($curl, CURLOPT_TIMEOUT,3);
        if (!empty($data) && !$isget){
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

}