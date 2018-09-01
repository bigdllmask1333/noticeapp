<?php
namespace sdk;
use xmpush\Builder;
use xmpush\HttpBase;
use xmpush\Sender;
use xmpush\Constants;
use xmpush\Stats;
use xmpush\Tracer;
use xmpush\Feedback;
use xmpush\DevTools;
use xmpush\Subscription;
use xmpush\TargetedMessage;

include_once(dirname(__FILE__) . '/autoload.php');


class androidpush
{
    public function index($aliasList,$title,$desc,$payload){
        Constants::useOfficial();   //在正式环境下使用push服务， useSandbox在测试环境中使用push服务不会影响线上用户
        $secret = '8q6P7dr0mWkmHsSDez1aTg==';
        $package = 'com.yikuaiqian.shiye.beta';  /*测试环境*/
        // 常量设置必须在new Sender()方法之前调用
        Constants::setPackage($package);
        Constants::setSecret($secret);
        $sender = new Sender();
        $message1 = new Builder();
        $message1->title($title);  // 通知栏的title
        $message1->description($desc); // 通知栏的descption
        $message1->passThrough(0);  // 这是一条通知栏消息，如果需要透传，把这个参数设置成1,同时去掉title和descption两个参数
        $message1->notifyType(2); //  1// 使用默认提示音提示  2 使用默认震动提示   4使用默认led灯光提示
        $message1->payload($payload); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
        $message1->extra(Builder::notifyForeground, 1); // 应用在前台是否展示通知，如果不希望应用在前台时候弹出通知，则设置这个参数为0
        $message1->notifyId(2); // 通知类型。最多支持0-4 5个取值范围，同样的类型的通知会互相覆盖，不同类型可以在通知栏并存
        $message1->build();
        $targetMessage = new TargetedMessage();
        $targetMessage->setTarget('alias1', TargetedMessage::TARGET_TYPE_ALIAS); // 设置发送目标。可通过regID,alias和topic三种方式发送
        $targetMessage->setMessage($message1);
        $sender->sendToAliases($message1, $aliasList)->getRaw();
//        print_r($sender->broadcastAll($message1)->getRaw());  /*向所有设备推送*/
    }










    public function index1($aliasList,$title,$desc,$payload){
        Constants::useOfficial();   //在正式环境下使用push服务， useSandbox在测试环境中使用push服务不会影响线上用户
        $secret = '8q6P7dr0mWkmHsSDez1aTg==';
        $package = 'com.yikuaiqian.shiye.beta';  /*测试环境*/
//        $package = 'com.example.admin.xmplugin';  /*正式环境*/
//        $data=array('content'=>'这里是内容','con'=>'，当然如果你需要就送穿我这也可以给你传');

// 常量设置必须在new Sender()方法之前调用
        Constants::setPackage($package);
        Constants::setSecret($secret);
//        $aliasList = array('0e423e7a-0707-3109-becb-1e7505b72006', 'alias2');

        $sender = new Sender();
        // message1 演示自定义的点击行为
        $message1 = new Builder();
        $message1->title($title);  // 通知栏的title
        $message1->description($desc); // 通知栏的descption
        $message1->passThrough(0);  // 这是一条通知栏消息，如果需要透传，把这个参数设置成1,同时去掉title和descption两个参数
        $message1->notifyType(2); //  1// 使用默认提示音提示  2 使用默认震动提示   4使用默认led灯光提示
        $message1->payload($payload); // 携带的数据，点击后将会通过客户端的receiver中的onReceiveMessage方法传入。
        $message1->extra(Builder::notifyForeground, 1); // 应用在前台是否展示通知，如果不希望应用在前台时候弹出通知，则设置这个参数为0
        $message1->notifyId(2); // 通知类型。最多支持0-4 5个取值范围，同样的类型的通知会互相覆盖，不同类型可以在通知栏并存
        $message1->build();
        $targetMessage = new TargetedMessage();
        $targetMessage->setTarget('alias1', TargetedMessage::TARGET_TYPE_ALIAS); // 设置发送目标。可通过regID,alias和topic三种方式发送
        $targetMessage->setMessage($message1);
        print_r($sender->sendToAliases($message1, $aliasList)->getRaw());
//        print_r($sender->broadcastAll($message1)->getRaw());  /*向所有设备推送*/
    }
}