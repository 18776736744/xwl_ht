<?php
namespace app\index\controller;
use think\Db;


class Article extends \think\Controller
{
    // 上传图片
    public function uploadImg()
    {	
        return saveImg('image');  
		 
    }

    // 保存文章
    public function save()
    {
        $uid = input('uid');
        $id = input('id');
        $t = input('t');
        if ($t == 'edit') {
            $id = db('topic')->where("id=$id")->update([
                'title'=>input('title'),
                'articleclassid'=>input('atid'),
            ]);
        }else{
            $uname = db("user")->where("uid=$uid")->value("username");
            $id = db('topic')->insertGetId([
                'authorid'=>$uid,
                'author'=>$uname,
                'title'=>input('title'),
                'articleclassid'=>input('atid'),
                'views'=>1,
                'isphone'=>1,
                'viewtime'=>time(),
                // 'tag'=>input('tag'),
                'image'=>input('image')
            ]);
        }
        return $id; 

       
    }
    
    // 获取列表
    public function lists()
    {
        $uid = input('uid');

        $lists = db("topic")
                ->where("authorid=$uid")
                ->field("title,id,viewtime,views")
                ->order('id desc')
                ->select();
        foreach ($lists as $key => $value) {

                    $lists[$key]['viewtime'] = date("Y-m-d H:i",$value['viewtime']);
                    $lists[$key]['longtap'] = false;
                }        
        return json($lists);
    }
    
    // 删除
    public function delete()
    {
        $id = input('id');
        $lists = db("topic")
                ->where("id=$id")
                ->delete();
		return($lists);	
    }

    public function info()
    {
        $id = input('id');
        $info = db("topic")
                ->where("id=$id")
                ->find();
        return json($info);
    }
}
