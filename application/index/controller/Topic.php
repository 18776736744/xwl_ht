<?php
namespace app\index\controller;
use think\Db;


class Category extends \think\Controller
{
    public function index()
    {	

    }
    // 获取发布文章
    public function topic_get(){
    	$topData = db('topic')->alias('t')
                    ->field('t.*')->join('vertify v','v.type='.input('type_'))
                    ->where("t.authorid=v.uid")
                    ->select();
                    return json($topData);
    }
    //我的文章
    public function myTopic(){
        $mytop = db('topic')->where("authorid=".input('authorid'))->select();
        return json($mytop);
    }
}
