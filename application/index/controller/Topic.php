<?php
namespace app\index\controller;
use think\Db;


class Topic extends \think\Controller
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
    // 获取文章详情
    public function getTopic(){
        $id = input('id');
        $xxtops = db('topic')->field("id,title,describtion,image,author,authorid,views,articleclassid,viewtime,likes,articles,price,ispc")->where('id'=>$id)->select();
        foreach ( $xxtops as $question ) {
                $question['viewtime'] = date('Y-m-d',$question['viewtime']);
                $xxtop[] = $question;
            }
        if ($xxtop) {
            return json($xxtop);
        }else{
            return json("2");
        }
    }
    // 获取机构或教师总数量
    public function count_topic(){
        $type = input('type_');
        if(!empty($type)){
            $topCount = db('vertify')
            ->where('type='.$type)
            ->count();
            return json($topCount);
        }
    }
}
