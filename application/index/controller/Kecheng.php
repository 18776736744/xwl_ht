<?php
namespace app\index\controller;
use think\Db;


class Kecheng extends \think\Controller
{
    public function save()
    {	
        $uid = input('uid');
        $id = input('id');
        $t = input('t');

        $data = [
                    'kecheng_name'=>input('kecheng_name'),
                    'age_range'=>input('age_range'),
                    'class_type'=>input('class_type'),
                    'class_zhidu'=>input('class_zhidu'),
                    'money'=>input('money'),
                    'start_time'=>input('start_time'),
                    'school'=>input('school'),
                    'teacher'=>input('teacher'),
                    'description'=>input('description'),
                    'chengnuo'=>input('chengnuo'),
                ];
        if ($t == 'edit') {
            $id = db('kecheng')->where("id=$id")->update($data);
        }else{
            $data['uid'] = input('uid');
            $data['add_time'] = time();

            $id = db('kecheng')->insertGetId($data);
        }
        return $id;
    }

    // 获取列表
    public function lists()
    {
        $uid = input('uid');

        $lists = db("kecheng")
                ->where("uid=$uid")
                ->field("kecheng_name title,id,add_time viewtime,start_time,money")
                ->order('id desc')
                ->select();
        return json($lists);
    }
    // 删除
    public function delete()
    {
        $id = input('id');
        $res = db("kecheng")
                ->where("id=$id")
                ->delete();
        return json($res);
    }

    public function info()
    {
        $id = input('id');
        $info = db("kecheng")
                ->where("id=$id")
                ->find();
        return json($info);
    }
    
}
