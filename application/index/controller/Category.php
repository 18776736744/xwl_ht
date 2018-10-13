<?php
namespace app\index\controller;
use think\Db;


class Category extends \think\Controller
{
    public function index()
    {	

    }
    // 分类
    public function categoryName(){
    	$pid = input('pid');
    	$cid = input('cid');
    	if(!empty($pid)){
    		
    	}
    	$catelist = db('category')
    	->where("pid= $pid")
    	->select();
    	return json($catelist);
    }

}
