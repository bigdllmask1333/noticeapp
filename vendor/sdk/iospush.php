<?php
namespace sdk;
use xmpush\IOSBuilder;
use xmpush\Sender;
use xmpush\Constants;
use xmpush\Stats;
use xmpush\Tracer;

include_once(dirname(__FILE__) . '/autoload.php');


class iospush
{
    public function index($aliasList,$desc,$payload){
        Constants::useSandbox();   //在正式环境下使用push服务， useSandbox在测试环境中使用push服务不会影响线上用户
        $secret = '1pQEVfEEsTWb6MRXR9nfjg==';
        $bundleId = 'com.AnhuiIndustry.DevRemind';
        Constants::setBundleId($bundleId);
        Constants::setSecret($secret);
        $message = new IOSBuilder();
        $message->description($desc);
        $message->soundUrl('default');
        $message->badge('4');
        $message->extra('payload', $payload);
        $message->build();
        $sender = new Sender();
        $sender->sendToAliases($message, $aliasList)->getRaw();
    }


    public function index1($aliasList){
        Constants::useSandbox();   //在正式环境下使用push服务， useSandbox在测试环境中使用push服务不会影响线上用户
        $secret = 'UOV5CfPdJNblE6Xa5mc79Q==';
        $bundleId = 'com.AnhuiIndustry.Emoney';
        Constants::setBundleId($bundleId);
        Constants::setSecret($secret);
        $desc = '韩师兄，你喜欢看毛片吗？需要多看！';
        $payload = '{"newdata":"韩师兄，你喜欢看毛片吗？需要多看！"}';
        $message = new IOSBuilder();
        $message->description($desc);
        $message->soundUrl('default');
        $message->badge('4');
        $message->extra('payload', $payload);
        $message->build();
        $sender = new Sender();
        $sender->sendToAliases($message, $aliasList)->getRaw();


        //        $sender->sendToAliases($message, $aliasList)->getRaw();
//        Array ( [result] => ok [trace_id] => Xco001615306919670155h [code] => 0 [data] => Array ( [id] => tco00161530691967016Wr ) [description] => 成功 [info] => Received push messages for 1 TOPIC )
//        $sender->sendToAliases($message, $aliasList)->getRaw();
//        print_r($sender->sendToAliases($message, $aliasList)->getRaw());
//        $ret=$sender->sendToAliases($message, $aliasList)->getRaw();
//        return $ret;
//        TODD这个地方需要把数据返回到控制器！然后保存这个数据中的消息id，以备存入数据库，等待查询消息发送状态
//        print_r($sender->broadcastAll($message)->getRaw());  /*向所有设备推送*/

//        Array ( [result] => ok [trace_id] => Xco001615306919670155h [code] => 0 [data] => Array ( [id] => tco00161530691967016Wr ) [description] => 成功 [info] => Received push messages for 1 TOPIC )

//        print_r($sender->sendToAliases($message, $aliasList)->getRaw());
//        print_r($sender->broadcastAll($message)->getRaw());  /*向所有设备推送*/
    }
}