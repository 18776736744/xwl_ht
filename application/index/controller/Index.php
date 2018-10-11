<?php
namespace app\index\controller;
use think\Db;


class Index extends \think\Controller
{
    public function index()
    {	

    }
// 全部文章
    public function topic(){
    	$topData = db('topic')->order('id desc')->select();
    	return json_encode($topData);
    }
    // 招聘
    public function job(){
    	if(empty(input('type'))){
    		$job = db('job')->order('id desc')->limit(5)->select();
    	}else{
    		$job = db('job')->order('id desc')->select();
    	}
    	return json_encode($job);
    }
    
}
