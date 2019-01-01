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
                $ajobs = db('job')->where('id='.$id)->alias('j')->join('user u','j.uid=u.uid')
    		->field('j.*,u.username,u.tximg')->select();
                foreach ( $ajobs as $question ) {
                     $squ = explode('市',  str_replace(',','', $question['address']));
                     $question['address'] = str_replace('省', '-', $squ[0]);
                     $question['pub_time'] = date('Y-m-d',$question['pub_time']);
                     $ajob [] = $question;
                }
                return json($ajob);
            }
        }
        
        else if($type=='update'){
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
                $mytop = db('job')->alias('j')->join('category c','j.classid=c.id')
                ->field('j.*,c.name')->where('uid='.$uid)->order('j.id desc')->select();
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


    public function toudi()
    {
        $id = input("job_id");

        $msgfrom = '校外链管理员';
        $username =  input("username");
        $uid = input("uid");
        $phone = input("phone");

       
        $info = db("job")->field("category,uid")->where("id=$id " )->find();
         
             
        sendMsg( $msgfrom,  $uid ,  $info['uid'], $username . "对您说：我对您的岗位有意向", '我对贵学校发布的'.$info['category'].'岗位有意向，我的电话是'.$phone.'，希望与您合作。 ', "job" );

        return json( "投递成功");
         

        

    }
    
}
