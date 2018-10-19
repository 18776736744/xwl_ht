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
    	->where("pid= $id")
    	->select();
    	return json($catelist);
    }

}
