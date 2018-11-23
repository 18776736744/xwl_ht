<?php
namespace app\index\controller;
class Weixin extends \think\Controller{

      //充值
      // 通知是去的pc的notify
	public function chongzhi(){
 		
 		$price = input("price");
 		$openid = input("openid");
 		$type = input("m_type");
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
            'body' => $theme_name,
            'out_trade_no' => $trade_time,
            'total_fee' => $price*100,
            'product_id'=>$proid,
            'attach'=>$proid,
            'time_start'=>date ( "YmdHis" ) ,
            'time_expire'=>date ( "YmdHis", time () + 3600 ),
         ];
 


        $jsApiParameters = \wxpay\JsapiPay::getPayParams($params,$openid);
       //把支付需要的数据返回到前台，前台获取这些数据调用微信支付的接口
        return json(['params'=>$jsApiParameters]);
	}

	public function pay_code()
	{
		# code...
	}


}

   
?>