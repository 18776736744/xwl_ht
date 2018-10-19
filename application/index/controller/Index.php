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
        $type = input('type_');
        if(!empty($type)){
            $topData = db('topic')->order('id desc')->limit('0,5')->select();
        }else{
            $topData = db('topic')->order('id desc')->select();
        }
    	return json($topData);
    }
    // 招聘
    public function job(){
    	if(empty(input('type_'))){
    		$jobs = db('job')->order('id desc')->limit(0,5)->select();
    		$job=[];

    		foreach ( $jobs as $question ) {
				 $squ = explode('市',  str_replace(',','', $question['address']));
				 $question['address'] = str_replace('省', '-', $squ[0]);
				 $question['pub_time'] = date('Y-m-d',$question['pub_time']);
				$job [] = $question;
			}
    	}else{
    		$job = db('job')->order('id desc')->paginate(6)->each(function($item, $key){
     
            $squ = explode('市',  str_replace(',','', $item['address']));
                     $item['address'] = str_replace('省', '-', $squ[0]);
                     $item['pub_time'] = date('Y-m-d',$item['pub_time']);
            return $item;
    	    )};
        }
    	return json($job);
    }

}
