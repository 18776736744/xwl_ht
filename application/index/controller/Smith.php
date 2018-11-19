<?php
namespace app\index\controller;
class Smith extends \think\Controller{

	//课程评价
	public function kecheng_commont()   
	{
		$id = input("kecheng_id");
    	$commont_list  =[];
		$comm = db("kecheng_commont")->alias("c")
					->field("c.*,u.username")
					->join("user u","c.plr = u.uid")
					->where("pldx = $id and status=2")
					->order("c.id desc")
					->select();

		$cp = 0;
		$star = [];
		foreach ( $comm as $question ) {

			if ($question['xingzhi'] == 2) {
			 	++$cp;
			 } 
			 $question['star_str']='';
			 for ($i=0; $i <$question['star'] ; $i++) { 
			 	$question['star_str'].= '★';
			 }

			// ++$star[$question['star']];
			$commont_list [] = $question;

		}

		$total_num  =count($commont_list);

		return json([
			"commont_list"=>$commont_list,
			"total_num"=>$total_num,
			"star"=>$star,
			"cp"=>$cp,
		]);
	}

	//充值金额买课 
	public function pay_kecheng()
	{
		 //addtopicviewhistory

		 $creattime = time();
		 $buy_uid = input("uid");
		$seller_uid=input("seller_uid");
		$kcid = input("kcid");
		$hid = db("kecheng_viewhistory")->insertGetId([
		 		'uid'=> $buy_uid,
		 		'username'=>input("username"),
		 		'tid'=>$kcid,
		 		'time'=>$creattime
		 ]);
	 	
	 	$cash_fee = input("money");
 		$paycash_fee=$cash_fee*100;

				//阅读的人金额扣减

				 db("kecheng_viewhistory")->query ( "UPDATE whatsns_user SET  `jine`=jine-'$paycash_fee' WHERE `uid`=$buy_uid" );


				$time=time();


				 $ddid = "";
				for ($i=0; $i <5-strlen($hid) ; $i++) { 
					 $ddid.='0';
				}
			$order_sn =  date('mdHi',time()).$ddid.$hid ;

				//作者获得金额								

				// $this->db->query ( "UPDATE whatsns_user SET  `jine`=jine+'$paycash_fee' WHERE `uid`=$seller_uid" );
				db("kecheng_viewhistory")->query ( "UPDATE whatsns_kecheng SET  `num`=num+1  WHERE `id`=$kcid" );

				db("kecheng_viewhistory")->query ( "INSERT INTO whatsns_paylog SET type='topaykecheng',typeid=$kcid,money=$cash_fee,openid='',fromuid=$seller_uid,touid=$buy_uid,`time`=$time,order_sn=$order_sn" );

				// dj_money
				db("kecheng_viewhistory")->query ( "INSERT INTO whatsns_paylog SET type='paykecheng',typeid=$kcid,money=$cash_fee,openid='',fromuid=$buy_uid,touid=$seller_uid,`time`=$time,order_sn=$order_sn" );

			

			exit ( '1' );

	}

	// 直接买课
	public function pay_kecheng_zhijie($value='')
	{
		# code...
	}

	public function getHasKecheng()
	{

		 $buy_uid = input("uid");
		$kcid = input("kcid");
		 $sl = db("kecheng_viewhistory")->where([
		 		'uid'=> $buy_uid,
		 		'tid'=>$kcid,
		 ])->count();

		 return json($sl);
	 
	}



	public function teacher_load()
	{
		$data['uid'] = input("uid");
		$data['doing_time'] = input( 'doing_time' );
		$data['doing_time_end'] = input( 'doing_time_end' );
		$data['company'] = input( 'company' );
		$data['category'] = input( 'category' );
		$data['money_grade'] = input( 'money_grade' );
			
		db( 'teacher_road')->insert($data );
	}

	public function load_lists()
	{
		$uid = input("uid");

		$load_lists = db("teacher_road")->where(" uid = ". $uid)->order("id desc" )->select();

		foreach ( $load_lists as $key=>$question ) {
			if (intval($question['category'])>0) {
			 	$load_lists[$key]['category']=db("category")->where("id=".$question['category'])->value('name');
				 
			}
			 
		}

		return json($load_lists);
	}
     


}

   
?>