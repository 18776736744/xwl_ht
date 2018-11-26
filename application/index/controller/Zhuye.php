<?php
namespace app\index\controller;
class Zhuye extends \think\Controller{
    // 获取认证类型
    public function getFlag($value='')
    {
        $uid = input('uid');
        $type = db('vertify')->where("uid='$uid'")->value('type');
        return json($type);
    }


    // 机构头像
    public function head(){
        $uid = input('uid');
        $head = db('user')->where("uid='$uid'")->value('tximg');
        return json($head);
    }
    // 机构名字
    public function name(){
        $uid = input('uid');
        $name = db('vertify')->where("uid='$uid'")->value('name');
        return json($name);
    }
    // 机构营业执照
    public function pic(){
        $uid = input('uid');
        $all_img = '';
        $all_img[0] = db('vertify')->where("uid='$uid'")->value('zhaopian1');
        $all_img[1] = db('vertify')->where("uid='$uid'")->value('zhaopian2');

        foreach ($all_img as $key => $value) {
            if (strstr($value,'upload')) {
                $all_img[$key] = 'https://www.xiaowailian.com/xwl_ht/public'.$value;
            }else{
                $all_img[$key] = 'https://www.xiaowailian.com/'.$value;
            }
        }
        return json($all_img);        
    }
    // 机构电话
    public function mobile(){
        $uid = input('uid');

        $mobile = db('user')->where("uid='$uid'")->value('phone');
        return json($mobile);
    }
    // 机构地址
    public function add(){
        $uid = input('uid');
        $add = '';
        $info = db('vertify')->where("uid='$uid'")->field('address,lng,lat')->find(); 
        if (empty($info['address'])) {
            $info = db('user')->where("uid='$uid'")->field('map address')->find(); 
        }
        return json($info);
    }
    // 机构简介
    public function jianjie(){
        $uid = input('uid');
        $jian_jie = db('vertify')->where("uid='$uid'")->value('jieshao');
        return json($jian_jie);
    }
    // 机构评论列表
    public function pinglun()
    {
        $uid = input('uid');

        $u_list = db("commont_list")->alias('c')
            ->field("c.*,u.username,u.tximg")
            ->join("user u","c.plr = u.uid")
            ->order("c.id desc")
            ->where("pldx='$uid' and status =2")->paginate(5);
			
	
            
        return json($u_list);
    }
    // 机构课程列表
    public function kecheng()
    {
         $uid = input('uid');
        $u_list = db("kecheng")->where("uid='$uid' and is_delete=2 ")->order("id desc")->paginate(6);
        return json($u_list);
    }
    // 机构招聘列表
    public function zhaopin()
    {
         $uid = input('uid');
        $u_list = db("job")->where("uid='$uid'")->order("id desc")->paginate(4);
        return json($u_list);
    }


    public function newinfo()
    {
        $uid = input('uid');
        $headT = db('vertify')->where("uid='$uid'")->find();

        
        if ($headT['type'] == 1) {
            $headT['kecheng_num'] = db("kecheng")->where("uid=$uid and is_delete=2")->count();
            $headT['job_num'] = db("job")->where("uid=$uid")->count();
        }else{
            $headT['uinfo'] = db("user")->where("uid=$uid")->find();
        }
        $headT['name'] = db("user")->where("uid=$uid")->value("username");
        $headT['describtion'] = db("topic")->where("articleclassid=148 and authorid=$uid")->value("describtion"); 

		if(!$headT['describtion']){
			$headT['describtion'] = db("topic")->where("articleclassid=150 and authorid=$uid")->value("describtion"); 
		}
        
        return json($headT);
    }

    
    // 教师头像
    public function headT(){
        $uid = input('uid');
        $headT = db('user')->where("uid='$uid'")->value('tximg');
        return json($headT);
    }   
    // 教师名字
    public function nameT(){
        $uid = input('uid');
        $nameT= db('vertify')->where("uid='$uid'")->value('name');
        return json($nameT);
    }
    // 教师电话
    public function mobileT(){
        $uid = input('uid');
        $mobile = db('user')->where("uid='$uid'")->value('phone');
        return json($mobile);
    }
    // 教师证件照片
    public function picT(){
        $uid = input('uid');
        $all_img = '';
        $all_img[0] = db('vertify')->where("uid='$uid'")->value('zhaopian1');
        $all_img[1] = db('vertify')->where("uid='$uid'")->value('zhaopian2');
        return json($all_img);        
    }
    // 教师简介
    public function jianjieT(){
        $uid = input('uid');
        $jian_jieT = db('vertify')->where("uid='$uid'")->value('jieshao');
        return json($jian_jieT);
    }
    // 教师教学经历
    public function jingliT(){
        $uid = input('uid');
        $jing_liT = '';
        $jing_liT = db('teacher_road')->where("uid='$uid'")->order('id desc')->select();

        foreach ($jing_liT as $key => $value) {
             if (intval($value['category'])>0) {
                $jing_liT[$key]['fenlei'] = db("category")->where("id=".$value['category'])->value("name");
            }
        }
        return json($jing_liT);
    }
   
    // 教师岗位
    public function gangwei(){
        $uid = input('uid');
        $gang_wei = db('user')->field("is_busy,has_teacher_zm,money_grade")->where("uid=$uid")->find();
        return json($gang_wei);
    }
}