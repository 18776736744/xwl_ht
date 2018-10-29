<?php
namespace app\index\controller;
class Zhuye extends \think\Controller{
    // 获取认证类型
    public function getFlag($value='')
    {
        $uid = input('uid');
        $type = db('approve')->where("uid='$uid'")->value('type');
        return json($type);
    }


    // 机构头像
    public function head(){
        $uid = input('uid');
        $head = db('teacher_user')->where("mobile='$uid'")->value('head_portrait');
        return json($head);
    }
    // 机构名字
    public function name(){
        $uid = input('uid');
        $name = db('approve')->where("uid='$uid'")->value('orgName');
        return json($name);
    }
    // 机构营业执照
    public function pic(){
        $uid = input('uid');
        $all_img = '';
        $all_img[0] = db('approve')->where("uid='$uid'")->value('pic_zhengmian');
        $all_img[1] = db('approve')->where("uid='$uid'")->value('pic_fanmian');
        return json($all_img);        
    }
    // 机构电话
    public function mobile(){
        $mobile = db('teacher_user')->where('id=2')->value('mobile');
        return json($mobile);
    }
    // 机构地址
    public function add(){
        $uid = input('uid');
        $add = '';
        $add[0] = db('teacher_user')->where("mobile='$uid'")->value('province');
        $add[1] = db('teacher_user')->where("mobile='$uid'")->value('city');
        $add[2] = db('teacher_user')->where("mobile='$uid'")->value('country');
        return json($add);
    }
    // 机构简介
    public function jianjie(){
        $uid = input('uid');
        $jian_jie = db('approve')->where("uid='$uid'")->value('jianjie');
        return json($jian_jie);
    }
    // 机构评论列表
    public function pinglun()
    {
        $uid = input('uid');
        $u_list = db("commont_list")->where("pldx='$uid'")->paginate(5);
        return json($u_list);
    }
    // 机构课程列表
    public function kecheng()
    {

        $u_list = db("kecheng")->paginate(6);
        return json($u_list);
    }
    // 机构招聘列表
    public function zhaopin()
    {
        $u_list = db("job")->paginate(4);
        return json($u_list);
    }




    
    // 教师头像
    public function headT(){
        $uid = input('uid');
        $headT = db('teacher_user')->where("mobile='$uid'")->value('head_portrait');
        return json($headT);
    }   
    // 教师名字
    public function nameT(){
        $uid = input('uid');
        $nameT= db('approve')->where("uid='$uid'")->value('teachName');
        return json($nameT);
    }
    // 教师电话
    public function mobileT(){
        $uid = input('uid');
        $mobile = db('teacher_user')->where("mobile='$uid'")->value('mobile');
        return json($mobile);
    }
    // 教师证件照片
    public function picT(){
        $uid = input('uid');
        $all_img = '';
        $all_img[0] = db('approve')->where("uid='$uid'")->value('pic_zhengmian');
        $all_img[1] = db('approve')->where("uid='$uid'")->value('pic_fanmian');
        return json($all_img);        
    }
    // 教师简介
    public function jianjieT(){
        $uid = input('uid');
        $jian_jieT = db('approve')->where("uid='$uid'")->value('jianjie');
        return json($jian_jieT);
    }
    // 教师教学经历
    public function jingliT(){
        $uid = input('uid');
        $jing_liT = '';
        $jing_liT[0] = db('teacher_road')->where("uid='$uid'")->value('doing_time');
        $jing_liT[1] = db('teacher_road')->where("uid='$uid'")->value('company');
        $jing_liT[2] = db('teacher_road')->where("uid='$uid'")->value('category');
        $jing_liT[3] = db('teacher_road')->where("uid='$uid'")->value('money_grade');
        return json($jing_liT);
    }
    // 教师评论
    public function pinglunT()
    {
        $uid = input('uid');
        $u_list = db("commont_list")->where('pldx=1')->paginate(5);
        return json($u_list);
    }
    // 教师岗位
    public function gangwei(){
        $uid = input('uid');
        $gang_wei = '';
        $gang_wei[0] = db('teacher_user')->where('id=1')->value('dangqian');
        $gang_wei[1] = db('teacher_user')->where('id=1')->value('yixiang');
        return json($gang_wei);
    }
}