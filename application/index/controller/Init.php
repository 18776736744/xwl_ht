<?php
namespace app\index\controller;
class Init extends \think\Controller{
	// 申请
	public function init(){
		$lessons= db('kecheng')->where('uid',1)->select();
		return json($lessons);
	}

	public function getUserInfo(){
		$mobile=input('mobile');
		$info=db('user')->where('mobile',$mobile)->select();
		return json($info);
	}
}

?>