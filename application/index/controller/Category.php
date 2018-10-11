<?php
namespace app\index\controller;
use think\Db;


class Category extends \think\Controller
{
    public function index()
    {	

        return $this->categoryName(0,100);
    }
    public function categoryName($pid,$cid){
    	$catelist = db('category')
    	->where("pid= $pid")
    	->select();
    	return json_encode($catelist);
    }
}
