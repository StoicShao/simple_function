<?php
error_reporting(0);
session_start();
//ini_set('session.gc_maxlifetime', 7200); //设置时间,正式项目还是需要用数据库操作
/*
 * 公众号绑定js安全域名，下载微信txt到目录下面
 * session 一般24分钟过期
 * token和ticket两小时过期，需要全局缓存和刷新，最好存储数据库
 * 分享时，url，link链接域名要和绑定的域名一致
 * url注意动态获取，如果和分享的页面的url不对等就会分享不成功
*/
class weixin{
    function __construct(){
        mysql_connect(localhost,'root','root');
        mysql_select_db('weixin');
        mysql_query("set names utf8");
    }
    function index(){
        $sql = "SELECT * FROM weixin";
        $res = mysql_query($sql);
        if($res){
            while($row = mysql_fetch_assoc($res)){
                $result[] = $row;
            }
        }
        $appid      =  $result['appid'];
        $appsecret  =  $result['appsecret'];
        //d,b
        $appid     = '';
        $appsecret = '';
        $token     = $this->getToken($appid,$appsecret);
        //print_r($token);
        if($token){
            $jsapiTicket = $this->getJsApiTicket($token);
        }
        if($jsapiTicket){
            $signPackage = $this->getSignPackage($appid,$jsapiTicket);
        }
        return $signPackage;
    }
    //获取token
    function getToken($appid,$appsecret){
        $sql = "SELECT * FROM weixin";
        $res = mysql_query($sql);
        if($res){
            while($row = mysql_fetch_assoc($res)){
                $result[] = $row;
            }
        }
        $token_time =  $result['token_time'];
        $token      =  $result['token'];
        //判断token是否过期，如过期重启获取，并存入数据库
        if(!$token || time() > $token_time){
            $url    = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $data   = json_decode($this->httpGet($url),true);
            $token2 = $data[access_token];
            $token_time2   = time()+7200;
            $sql2  = "UPDATE `weixin` SET `token`={$token2},`token_time`={$token_time2} WHERE `id`=1";
            if(mysql_query($sql2)){
                $token = $data[access_token];
            }
        }else{
            $token = $token;
        }
        return $token;
    }
    //获取ticket
    function getJsApiTicket($token){
        $sql = "SELECT * FROM weixin";
        $res = mysql_query($sql);
        if($res){
            while($row = mysql_fetch_assoc($res)){
                $result[] = $row;
            }
        }
        $ticket_time  =  $result['ticket_time'];
        $JsApiTicket  =  $result['JsApiTicket'];
        //判断ticket是否过期，如过期重启获取，并存入数据库
        if(!$JsApiTicket || time() > $ticket_time){
            $url   = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$token";
            $map   = json_decode($this->httpGet($url),true);
            $JsApiTicket2  =  $map[ticket];
            $ticket_time2  =  time()+7200;
            $sql2  = "UPDATE `weixin` SET `JsApiTicket`={$JsApiTicket2},`ticket_time`={$ticket_time2} WHERE `id`=1";
            if(mysql_query($sql2)){
                $JsApiTicket = $map[ticket];
            }
        }else{
            $JsApiTicket     = $JsApiTicket;
        }
        return $JsApiTicket;
    }
    //获取签名信息
    function getSignPackage($appid,$jsapiTicket){
        $jsapiTicket = $jsapiTicket;
        // 注意 URL 一定要动态获取，不能 hardcode.
        //$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        //$url = "$protocol$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        /**
        url注意动态获取，如果和分享的页面的url不同就会分享不成功
         **/
        $url = "此处分享的页面地址，从前台接收";//$_GET['url'];

        $timestamp = time();
        $nonceStr  = $this->createNonceStr();
        // 这里参数的顺序要按照 key 值 ASCII 码升序排序
        $rawString = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";

        $signature = sha1($rawString);
        $signPackage['appid']     = $appid;
        $signPackage['nonceStr']  = $nonceStr;
        $signPackage['timestamp'] = $timestamp;
        $signPackage['signature'] = $signature;
        $signPackage['rawString'] = $rawString;
        //这种数组写法容易出问题，js分享格式比较敏感
//        $signPackage = array(
//            "appId" => $appid,
//            "nonceStr" => $nonceStr,
//            "timestamp" => $timestamp,
//            "url" => $url,
//            "signature" => $signature,
//            "rawString" => $string
//        );
        return $signPackage;

    }
    //生成随机数
    function createNonceStr($length = 16){
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for($i = 0; $i < $length; $i++){
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    function httpGet($url){
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 500);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_URL, $url);

        $res = curl_exec($curl);
        curl_close($curl);

        return $res;
    }

}

$obj  = new weixin();
$sign = $obj->index();
$sign =
        "wx.config({
                    debug: false,
                    appId: '$sign[appId]',
                    timestamp: $sign[timestamp]
                ,
                nonceStr
        :
        '$sign[nonceStr]',
                signature
        :
        '$sign[signature]',
                jsApiList
        :
        [
            'checkJsApi',
            'onMenuShareTimeline',
            'onMenuShareAppMessage',
            'onMenuShareQQ',
            'onMenuShareWeibo',
            'hideMenuItems',
            'showMenuItems',
            'hideAllNonBaseMenuItem',
            'showAllNonBaseMenuItem',
            'translateVoice',
            'startRecord',
            'stopRecord',
            'onRecordEnd',
            'playVoice',
            'pauseVoice',
            'stopVoice',
            'uploadVoice',
            'downloadVoice',
            'chooseImage',
            'previewImage',
            'uploadImage',
            'downloadImage',
            'getNetworkType',
            'openLocation',
            'getLocation',
            'hideOptionMenu',
            'showOptionMenu',
            'closeWindow',
            'scanQRCode',
            'chooseWXPay',
            'openProductSpecificView',
            'addCard',
            'chooseCard',
            'openCard'
        ]
        })
        ;
        wx.ready(function () {
            wx.showOptionMenu();
            wx.onMenuShareAppMessage({
                title: '标题', // 分享标题
                desc: '内容描述', // 分享描述
                link: '$sign[url]', // 分享链接
                imgUrl: '', // 分享图标
                type: '', // 分享类型,music、video或link，不填默认为link
                dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function () {
                    window.location.replace(\"$sign[url]\");
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });


            wx.onMenuShareTimeline({
                title: '标题', // 分享标题
                link: '$sign[url]', // 分享链接
                imgUrl: '', // 分享图标
                success: function () {
                    window.location.replace(\"$sign[url]\");
                },
                cancel: function () {
                    // 用户取消分享后执行的回调函数
                }
            });

        });";
echo $sign;
?>