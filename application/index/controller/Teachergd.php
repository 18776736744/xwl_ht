<?php
namespace app\index\controller;

class Teachergd extends \think\Controller
{
	public function load()
	{
		//简介
		// $intro=db("")
		//岗位
		$mobile=input('mobile');
		 // $teacher=["小飞侠，毕业于官洲大学吃人学院，25岁，爱好是吃人，目前已有10次扶老奶奶过马路被骗经历","没有经验","这个老师人很好的，上次还把我吃了","想当个流浪汉啊","https://m.jiaoyubao.cn/images/jigou_zx.png"];
		 		 // $teacher=["小飞侠，毕业于官洲大学吃人学院，25岁，爱好是吃人，目前已有10次扶老奶奶过马路被骗经历",$experience,$commont,"想当个流浪汉啊","https://m.jiaoyubao.cn/images/jigou_zx.png"];
		$info=db("user")->where("mobile",$mobile)->select();
		return json($info);
	}
}