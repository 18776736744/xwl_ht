<?php
namespace app\index\controller;
use think\Db;


class Video extends \think\Controller
{
    public function save()
    {	
        $uid = input('uid');
		$video_name = input('video_name');
		$age_range = input('age_range');
		$nandu = input('nandu');
        $add_time = time();

		$description = input('description');
		$image = input('image');
		$video = input('video');
		if (empty($image)) {
			$image = '/static/study/default.jpg';
		} 

		$list = db('video')->insert([
			'uid' => $uid, 'video_name' => $video_name,
			'age_range' => $age_range, 
			'nandu' => $nandu, 
            'description' => $description, 
            'image' => $image,
            'video' => $video,
			'add_time'=>time(),
		]);

		return json($list);
    }

    // 获取班级
    public function getBanji()
    {
        $uid = input('uid');
        $banji = db("huifang")->field("kemu,id")->where("uid=$uid")->order("id desc")->select();
        return json($banji);
    }
    // 获取列表
    public function lists()
    {
        $id=input('id');
        $uid = input('uid');
        if (!empty($uid)) {
            # code...
            $lists = db("video")
                ->where("uid=$uid")
                ->field("video_name title,id,add_time viewtime,views,is_delete")
                ->order('id desc')
                ->select();
        }else if(!empty($uid)&&!empty($id)){
            $lists = db("video")
                ->where(['id'=>$id,'uid'=>$uid])
                ->field("video_name title,id,add_time viewtime,views,is_delete")
                ->order('id desc')
                ->select();
        }else{
            $lists = db("video")
                ->field("video_name title,id,add_time viewtime,views,is_delete")
                ->order('id desc')
                ->select();
        }
        return json($lists);
    }
    // 删除
    public function delete()
    {
        $id = input('id');
        $res = db("video")
                ->where("id=$id")
                ->update(["is_delete"=>1]);
        return json($res);
    }

    public function info()
    {
        $id = input('id');
        $info = db("video")
                ->where("id=$id")
                ->find();
        return json($info);
    }


    // 学生视频辅导班级
	public function getStuClass()
	{


		$uid = input('uid');
		$where = "  u.uid=" . $uid;

        $first_cate = db("user")->alias("u")
                    ->field("h.uname,h.kemu")
                    ->join("huifang_student hs","u.phone=hs.mobile","left")
                    ->join("huifang h","h.id=hs.hf_id","left")
                    ->where($where)
                    ->select();
        
         

		$return_first_cate = [];
		$return_second_cate = $return_third_cate = [];

		foreach ($first_cate as $key => $value) {
			$return_first_cate[$value['uname']] = $value['uname'];
		}


		foreach ($first_cate as $key => $value) {
			$return_second_cate[$value['uname']][] = $value['kemu'];
		}

		return json([
			'first_cate' => array_values($return_first_cate),
			'second_cate' => array_values($return_second_cate),
			'third_cate' => []
		]);
	}
    
}
