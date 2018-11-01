<?php
namespace app\index\controller;
class Ht extends \think\Controller{
	public function kc_list()   //课程推荐
	{
    	$list=db('kecheng')->order('id desc')->limit(6)->select();
		  return json($list);
	}
    public function kc_xq()   //课程详情
	{
		$id=input('id');
    	$list=db('kecheng')->where("id=$id")->find();
		$list['chengnuo'] = json_decode($list['chengnuo'],true);
		  return json($list);
	}


}

?>