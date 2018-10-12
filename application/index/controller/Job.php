<?php
namespace app\index\controller;
use think\Db;


class Job extends \think\Controller
{
    public function index()
    {	

    }
    public function Ajob(){
        $data = [
                    'category'=>input('category'),
                    'classid'=>input('classid'),
                    'address'=>input('address'),
                    'people'=>input('people'),
                    'money'=>input('money'),
                    'xueli'=>input('xueli'),
                    'sex'=>input('sex'),
                    'xingzhi'=>input('xingzhi'),
                    'description'=>input('description'),
                    'has_shebao'=>input('has_shebao'),
                    'has_gjj'=>input('has_gjj'),
                ];
        $type = input('type_');
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
                                ->update($data);
                if($update){
                    return json("1");
                }else{
                    return json("2");
                }
            }
        }else if ($type=='add') {
                $data['uid']=$uid;
                $insert=db('job')->update($data);
                if($insert){
                    return json("1");
                }else{
                    return json("2");
                }
        }else if($type=='my'){
            if(!empty($uid)){
                $mytop = db('job')->where('uid='.$uid)->order('id desc')->select();
                if($mytop){
                    return json($mytop);
                }else{
                    return json("2");
                }
            }
        }else if($type =='delete'){
            $dele = db('job')->where(['id'=>$id,'uid'=>$uid])->delete();
            if($dele){
                return json('1');
            }else{
                return json("2");
            }
        }

    }
    
}
