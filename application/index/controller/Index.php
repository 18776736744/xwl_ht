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
    	return json($topData);
    }
    // 招聘
    public function job(){
    	if(empty(input('type'))){
    		$jobs = db('job')->order('id desc')->limit(5)->select();
    		$job=[];

    		foreach ( $jobs as $question ) {
				 $squ = explode('市',  str_replace(',','', $question['address']));
				 $question['address'] = str_replace('省', '-', $squ[0]);
				 $question['pub_time'] = date('Y-m-d',$question['pub_time']);
				$job [] = $question;
			}
    	}else{
    		$jobs = db('job')->order('id desc')->select();
    		$job=[];
    		foreach ( $jobs as $question ) {
				 $squ = explode('市',  str_replace(',','', $question['address']));
				 $question['address'] = str_replace('省', '-', $squ[0]);
				 $question['pub_time'] = date('Y-m-d',$question['pub_time']);
				$job [] = $question;
			}
    	}
    	return json($job);
    }

}
