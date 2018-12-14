<?php
namespace app\index\controller;
use think\Db;

// 家庭作业接口
class Homework extends \think\Controller
{
     public function getlslist()
     {
        $uid = input('uid');
        $vertify_type = input('vertify_type');
        $username = input('username');

        if ($vertify_type == 1) {
                // 机构查询所有
                $where = "  h.uid=" . $uid;
        } else {
                // 老师查询自己的
                $where = "  h.uname='" . $username . "'  ";
        }

        $first_cate = db("huifang")->alias("h")
                        ->field("u.username,h.kemu,h.id")
                        ->join("user u","h.uid=u.uid")
                        ->where("$where")
                        ->order("h.id desc")
                        ->select(); 

        $return_first_cate = [];
        $return_second_cate = $return_third_cate = [];

        foreach ($first_cate as $key => $value) {
                $return_first_cate[$value['username']] = $value['username'];
        }


        foreach ($first_cate as $key => $value) {
                $return_second_cate[$value['username']][] = $value['kemu'];

                $stu_a =db("huifang_student")
                        ->field("student_name")
                        ->where("hf_id=" . $value['id'])
                        ->order("id asc")
                        ->select();
                foreach ($stu_a as $k => $v) {
                         $return_third_cate[$value['username']][$key][] =  $v['student_name'];
                }
             
        }

        return json([
                'first_cate' => array_values($return_first_cate),
                'second_cate' => array_values($return_second_cate),
                'third_cate' => array_values($return_third_cate),
        ]);

        return json($lists);
     }
}
