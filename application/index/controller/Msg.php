<?php
namespace app\index\controller;
use think\Db;

// 消息接口
class Msg extends \think\Controller
{
     public function fankui()
     {
        // 反馈

        $msgfrom = input("username");
        $username =  input("username");
        $description =  input("description");
        $uid = input("uid");
 

        sendMsg( $msgfrom, $uid, 1,  "我对小程序有这些要反馈的意见，".$description, "xcx_fankui" );

        return json(1);
     }

     public function lists()
     {
        // 消息列表
        $uid = input("uid");

        $msg_list = db("message")->alias("m")
            // ->join("user u","m.touid = u.uid")
            ->field("m.*")
            ->where("m.touid=$uid")
            ->order("m.id desc")
            ->select();

        foreach ($msg_list as $key => $value) {
            $msg_list[$key]['time'] = date('Y-m-d,H:i:s',$value['time']);
            $msg_list[$key]['content'] = str_replace("点击查看","", strip_tags($value['content']));
        }    
        return json($msg_list);    
     }


     public function commont_list()
     {
        //评论列表
        // 27.显示为对本学校或本老师的评价
        $uid = input("uid");

       $c_list = db("commont_list")->alias('c')
            ->field("c.*,u.username,u.tximg")
            ->join("user u","c.plr = u.uid")
            ->order("c.id desc")
            ->where("pldx='$uid' ")->select();

        foreach ($c_list as $key => $value) {
            $c_list[$key]['add_time'] = date('Y-m-d H:i',$value['add_time']);
        }    
        return json($c_list);    
     }
	 public function  view_state(){
	 	$uid=input('uid');
		$state=0;
	 	$list=db('message')->where(['touid'=>$uid,'state'=>$state])
	 	->order('id desc')->value('state');
	 	return json($list);
		 
	 }
	 public function modify_state(){
	 	$uid=input('uid');
		$state=0;
		$list=db('message')->where(['touid'=>$uid,'state'=>$state])
	 	->setInc('state');
		return json($list);
	 }
}
