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
        if ($type=='see') {
            if(!empty($id)){
            $ajob = db('job')->where('id='.$id)->select();
            }
        }else if($type=='edit'){
            $uid=input('uid');
            if(!empty(['$uid','$hid'])){
                
            }
        }
        
    }
    
}
