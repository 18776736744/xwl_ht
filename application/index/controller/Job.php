<?php
namespace app\index\controller;
use think\Db;


class Index extends \think\Controller
{
    public function index()
    {	

    }
    public function Ajob(){
        $type = input('type');
        $id=input('id');
        $uid=input('uid');

        if ($type=='see') {
            if(!empty($id)){
            $ajob = db('job')->where('id='.$id)->select();
            return json($ajob);
            }
        }else if($type=='update'){
            if(!empty($uid)){
                $update=db('job')->where(['id'=>$id,'uid'=>$uid])
                                ->update([
                                    'category'=>$category,
                                    'classid'=>$classid,
                                    'address'=>$address,
                                    'people'=>$people,
                                    'money'=>$money
                                ]);
                                if($update){
                                    return "更新成功";
                                }else{
                                    return "更新失败";
                                }
            }elseif ($type=='add') {
                $insert=db('job')->update([
                                    'uid'=>$uid,
                                    'category'=>$category,
                                    'classid'=>$classid,
                                    'address'=>$address,
                                    'people'=>$people,
                                    'money'=>$money
                                ]);
                if($insert){
                    return "添加成功";
                }else{
                    return "添加失败";
                }
            }
        }
        
    }
    
}
