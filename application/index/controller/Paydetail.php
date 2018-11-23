<?php
namespace app\index\controller;
use think\Controller;

class Paydetail extends Controller{
	public function paydetail()
	{
		$uid = input("uid");
		$paylog = db('paylog')->alias('c')->join('user d','d.uid=c.touid')->field('c.id,c.type,c.money,c.time,d.tximg,c.fromuid,c.typeid,c.order_sn')
				->where("uid=$uid")
				->order('time desc')
				->select();
		foreach ($paylog  as $key => $money) {
		  	$money['time'] = date('Y-m-d H:i',$money['time']);

			$money = getType($money);
			$paylog[$key] = $money;
		  }  
		return json($paylog);
	}
	public function bill(){
		$id=input('id');
		$list=db('paylog')->alias('c')->join('user d','d.uid=c.touid')->where("id=$id")->field('c.time,c.money,c.order_sn,d.tximg,c.fromuid,c.typeid,c.type')->find();

		$list  = getType($list);
		$list['time'] = date('Y-m-d H:i',$list['time']);
		return json($list);
	}
}
function getType($money)
{
	switch ($money ['type']) {

		case 'tohuifangmsg' :

			$money ['operation'] = '回访发短信';

			$money ['money'] = "消费" . $money ['money'] . "财富值";




			$money ['content'] = "家校反馈发短信消费";



			break;
				case 'topaykecheng' :

					$money ['operation'] = '用户付费报名';

					

					$money ['money'] = "支出" . $money ['money'] . "元";

					$mod = db("kecheng")->where("id =". $money ['typeid'] )->find();
					$money ['keinfo'] = $mod;

			$money ['fromusername'] = db("user")->where ("uid=". $money ['fromuid'] )->find();




					$money ['content'] = "您付费报名了课程：" . $mod ['kecheng_name'];
					$money['chengnuo'] = json_decode($mod['chengnuo'],true);
					
				

					break;

				case 'paykecheng' :

					$money ['operation'] = '用户付费报名';

					
			$money ['fromusername'] = db("user")->where ("uid=". $money ['fromuid'] )->find();




					$money ['money'] = "收入" . $money ['money'] . "元";



					$mod = db("kecheng")->where("id =". $money ['typeid'] )->find();

					$money ['keinfo'] = $mod;

					$money['chengnuo'] = json_decode($mod['chengnuo'],true);

					$money ['content'] = "用户付费报名了您的课程：" . $mod ['kecheng_name'];

					

					break;

				
				case 'topayarticle' :

					$money ['operation'] = '用户付费阅读';

					

					$money ['money'] = "支出" . $money ['money'] . "元";

					$mod = db("topic")->where ("id=". $money ['typeid'] );


					$money ['content'] = "您付费阅读了文章：" . $mod ['title'] ;

					

					break;

				case 'tid':
						
					$_uid = $money['fromuid'];

					$money ['money'] = "被打赏" . $money ['money'] . "元";

					$money['operation'] = '文章打赏';
					break;
				case 'payarticle' :

					$money ['operation'] = '用户付费阅读';

					

					$money ['money'] = "收入" . $money ['money'] . "元";

					

					$mod = db("topic")->where ("id=". $money ['typeid'] );


					$money ['content'] = "用户付费阅读了您的文章：" . $mod ['title'] ;

					

					break;
 
				case 'chongzhi' :

					$money ['operation'] = '用户充值';



					$money ['money'] = "收入" . $money ['money'] . "元";



					$money ['content'] = "来自用户充值付款";

					break;

				case 'creditchongzhi' :

					$money ['operation'] = '用户财富积分充值';

					$credit2 = $money ['money'] * 100;



					$money ['money'] = "获得" . $credit2 . "积分";



					$money ['content'] = "来自用户财富积分充值付款";

					break;

 			}
return $money;
}
?>