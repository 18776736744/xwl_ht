<?php
namespace app\index\controller;

use think\Db;


class Topic extends \think\Controller
{
    public function index()
    {

    }
    // 获取发布文章
    public function topic_get()
    {
        $topData = db('topic')->alias('t')
            ->field('t.*')->join('vertify v', 'v.type=' . input('type_'))
            ->where("t.authorid=v.uid")
            ->select();
        return json($topData);
    }
    //我的文章
    public function myTopic()
    {
        $mytop = db('topic')->where("authorid=" . input('authorid'))->select();
        return json($mytop);
    }
    // 获取文章详情
    public function getTopic()
    {
        $id = input('id');
        $uid = input('uid');
        $xxtops = db('topic')->where("id=$id")->select();
        foreach ($xxtops as $question) {
            $question['viewtime'] = date('Y-m-d', $question['viewtime']);
            $question['tximg'] = db("user")->where("uid=" . $question['authorid'])->value("tximg");
            if($question['readmode']>1 && $uid){
                $question['is_pay'] = db ( "topic_viewhistory")->where(" uid=$uid and tid=$id " )->value ("id");

            }
            $xxtop[] = $question;

        }
        
        if ($xxtop) {
            return json($xxtop);
        } else {
            return json("2");
        }
    }
    // 获取机构或教师总数量
    public function count_topic()
    {
        $type = input('type_');
        if (!empty($type)) {
            $topCount = db('vertify')
                ->where('type=' . $type)
                ->count();
            return json($topCount);
        }
    }

    function getlist_bytype()
    { 
        $recargelist = array();
        $typeid = input("id");
        $list_pay = db("paylog")->where(" type='tid' and typeid=$typeid and type not in('paysite_zhuanjia','paysite_xuanshang','paysite_toukan')  ")->order("time desc")->select();

     
        $recargelist = [];
        foreach ($list_pay as $money) {

            $money['time'] = date('Y-m-d H:i',$money['time']);


            if ($money['fromuid'] == 0) {

                $money['operation'] = '网友打赏' . $money['money'] . "元" . '-' . $money['time'];
 

            } else {

                $_uid = $money['fromuid'];

                $user = db("user")->where("uid='$_uid'")->find();

                $money['operation'] = $user['username'] . '打赏' . $money['money'] . "元" . '-' . $money['time'];
 

            }






            $recargelist[] = $money;

        }

        return json($recargelist);

    }

    //有偿积分或金额阅读
	public function pay_topic()
	{
        $tid = input ( 'id' ) ;
        $username = input ( 'username' ) ;
		$topic = db("topic")->where ("id= $tid" )->find();
		$readmode=$topic['readmode'];
		 
        
		$cash_fee = $topic ['price'];
        $readuid = input("uid"); 
        $authorid = $topic ['authorid'];
        
        $one = db ( "topic_viewhistory")->where(" uid=$readuid and tid=$tid " )->find ();
		if ($one != null) {
			return json('已经付费过了');
		}
		//addtopicviewhistory
        $id = db ( "topic_viewhistory")->insertGetId( [
            'uid'=>$readuid,
            'username'=>$username,
            'tid'=>$tid,
            'time'=>time(),
        ]);
        
		if ($id > 0) {
			if($readmode=='2'){
                //阅读的人积分扣减
                db ( "topic_viewhistory")->query ( "UPDATE whatsns_user SET  `credit2`=credit2-'$cash_fee' WHERE `uid`=$readuid" );
                //作者获得积分
                db ( "topic_viewhistory")->query ( "UPDATE whatsns_user SET  `credit2`=credit2+'$cash_fee' WHERE `uid`=$authorid" );
                
			}
			if($readmode=='3'){
				$paycash_fee=$cash_fee*100;
				//阅读的人金额扣减
				db ( "topic_viewhistory")->query ( "UPDATE whatsns_user SET  `jine`=jine-'$paycash_fee' WHERE `uid`=$readuid" );
			 
				$time=time();
                $authorid=$topic['authorid'];
                
                
					$ddid = "";
					for ($i=0; $i <5-strlen($id); $i++) { 
						 $ddid.='0';
					}
				$order_sn =  date('mdHi',time()).$ddid.$id ;

				//作者获得金额								
				db ( "topic_viewhistory")->query ( "UPDATE whatsns_user SET  `jine`=jine+'$paycash_fee' WHERE `uid`=$authorid" );
				db ( "topic_viewhistory")->query ( "INSERT INTO whatsns_paylog SET type='topayarticle',typeid=$tid,money=$cash_fee,openid='',fromuid=$authorid,touid=$readuid,`time`=$time,order_sn='$order_sn'" );
				db ( "topic_viewhistory")->query ( "INSERT INTO whatsns_paylog SET type='payarticle',typeid=$tid,money=$cash_fee,openid='',fromuid=$readuid,touid=$authorid,`time`=$time,order_sn='$order_sn'" );
			}
			exit ( '1' );
		} else {
			exit ( '-4' );
		}

	}
}
