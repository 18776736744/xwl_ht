<?php
namespace app\index\controller;
class Weixin extends \think\Controller{

      //充值
      // 通知是去的pc的notify
	public function chongzhi(){
 		
 		$price = input("price");     //钱
 		$openid = input("openid");   //openid
 		$type = input("m_type");     //
    $uid = input("uid");  
 		$buy_shop_id = input("buy_shop_id");	
    $theme_name='充值';
    $trade_time=mt_rand().time();  

    $t1 = $type;

		$t2 = $buy_shop_id;

		$t3 = $uid;

		$t4 = $price;

		$t5 = rand ( 111111111, 999999999 );

		$proid = $t1 . "_" . $t2 . "_" . $t3 . "_" . $t4 . "_" . $t5;

       //支付需要的数据，详情在微信支付的文档
         $params = [
            'body' => $theme_name,                              //商品描述
            'out_trade_no' => $trade_time,                      //订单号
            'total_fee' => $price*100,                         
            'product_id'=>$proid,
            'attach'=>$proid,
            'time_start'=>date ( "YmdHis" ) ,                  //交易的起始时间
            'time_expire'=>date ( "YmdHis", time () + 3600 ),  //交易结束时间
         ];
 


        $jsApiParameters = \wxpay\JsapiPay::getPayParams($params,$openid);
       //把支付需要的数据返回到前台，前台获取这些数据调用微信支付的接口
        return json(['params'=>$jsApiParameters]);
	}

	public function pay_code()
	{
		# code...
  }
  
  public function dianbo()
  {
        // 确定 App 的云 API 密钥
    $secret_id = "AKIDDJEvX5pexHRWTUPRAzjI6Wlsulz6vYaH";
    $secret_key = "xxhKHAbtTGb1HQXVSHIlBWl08AA2Oha5";

    // 确定签名的当前时间和失效时间
    $current = time();
    $expired = $current + 86400;  // 签名有效期：1天

    // 向参数列表填入参数
    $arg_list = array(
        "secretId" => $secret_id,
        "currentTimeStamp" => $current,
        "expireTime" => $expired,
        "random" => rand());

    // 计算签名
    $orignal = http_build_query($arg_list);
    $signature = base64_encode(hash_hmac('SHA1', $orignal, $secret_key, true).$orignal);

    echo $signature;
    echo "\n";
  }


  


}

   
?>