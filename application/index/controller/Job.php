<?php
namespace app\index\controller;
use think\Db;


class Job extends \think\Controller
{
    public function index()
    {	

    }
    // 招聘增删查改
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
                    'pub_time'=>time()
                ];
        $type = input('type_');
        $id=input('id');
        $uid=input('uid');
        if ($type=='see') {
            if(!empty($id)){
                $ajobs = db('job')->where('id='.$id)->select();
                foreach ( $ajobs as $question ) {
                     $squ = explode('市',  str_replace(',','', $question['address']));
                     $question['address'] = str_replace('省', '-', $squ[0]);
                     $question['pub_time'] = date('Y-m-d',$question['pub_time']);
                     $ajob [] = $question;
                }
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
                $insert=db('job')->insert($data);
                if($insert){
                    return json("1");
                }else{
                    return json("2");
                }
        }else if($type=='my'){
            if(!empty($uid)){
                $mytop = db('job')->where('uid='.$uid)->order('id desc')->select();
                $job=[];
                foreach ( $mytop as $question ) {
                     $squ = explode('市',  str_replace(',','', $question['address']));
                     $question['address'] = str_replace('省', '-', $squ[0]);
                     $question['pub_time'] = date('Y-m-d',$question['pub_time']);
                    $job [] = $question;
                }
                if($mytop){
                    return json($job);
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
