<?php
namespace app\index\controller;
use think\Db;

// 机构接口
class Jigou extends \think\Controller
{
     public function lists()
     {
        $uid = input('uid');

        $lists = db("user")->alias("u")
                ->join("vertify v","u.uid = v.uid")
                ->field("v.name,v.jieshao,u.map,u.phone")
                ->order('id desc')
                ->where("v.type = 1")
                ->paginate();
        return json($lists);
     }
}
