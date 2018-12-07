<?php
namespace app\index\controller;
use think\Controller;

class Commentmy extends Controller{

	public function comment()
	{
		$uid=input('uid');
		$comment_list =db('commont_list')->where("pldx=$uid")->alias('a')->join('user b','b.uid=a.plr')
		->field('a.id,a.reply,a.add_time,a.picture,a.info,b.username,b.tximg,a.star')->order('add_time desc')->select();

		foreach ($comment_list as $key => $value) {
		  	$comment_list[$key]['add_time'] = date('Y-m-d H:i',$value['add_time']);
		  }  
		return json($comment_list);
		
	}

}
?>
