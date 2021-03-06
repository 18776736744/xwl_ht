<?php
namespace app\index\controller;
use think\Db;


class Category extends \think\Controller
{
    public function index()
    {	

    }
    // 分类下一级
    public function category_list(){
    	$id = input('id');
    	$grade = input('grade');

    	$catelist = db('category')->alias('c')
    	->field("t.title,t.describtion,t.image,t.views,t.articleclassid,t.viewtime,t.likes,t.articles,t.price,t.ispc,t.tximg")
    	->join('topic t','articleclassid='.$id)
    	->where("id= $id")
    	->select();
    	return json($catelist);
    }
    // 获取分类
    public function getlist(){
    	$type = input('type_');
    	$grade = input('grade');
    	$pid=input('pid');
        // 第一次加载获取的分类
    	if ($type=='load') {
    		$list=db('category')->field("id,pid,name")->where(['pid'=>'0','grade'=>$grade])->select();
        // 选择对应分类
    	}else if($type=='level'){
    		$list=db('category')->field("id,pid,name")->where(['pid'=>$pid,'grade'=>$grade])->select();
    	}
    	return json($list);
    }

}
