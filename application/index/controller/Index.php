<?php
namespace app\index\controller;
use think\Db;


class Index extends \think\Controller
{
    public function index()
    {	
    	
    }
// 全部文章
    public function topic(){
        $type = input('type_');
        if(!empty($type)){
            $topData = db('topic')->order('id desc')->limit('0,5')->select();
        }else{
            $topData = db('topic')->order('id desc')->paginate(6);
        }
    	return json($topData);
    }
    // 招聘
    public function job(){
    	if(empty(input('type_'))){
    		$jobs = db('job')->alias('j')->join('user u','j.uid=u.uid')
    		->field('j.*,u.username,u.tximg')->order('id desc')->limit(0,5)->select();
    		$job=[];

    		foreach ( $jobs as $question ) {
				 $squ = explode('市',  str_replace(',','', $question['address']));
				 $question['address'] = str_replace('省', '-', $squ[0]);
//				 $question['pub_time'] = date('Y-m-d',$question['pub_time']);
				$job [] = $question;
			}
    	}else{
    		$job = db('job')->alias('j')->join('user u','j.uid=u.uid')
    		->field('j.*,u.username,u.tximg')->order('id desc')->paginate(6)
    		->each(function($item, $key){
            $squ = explode('市',  str_replace(',','', $item['address']));
                     $item['address'] = str_replace('省', '-', $squ[0]);
//                   $item['pub_time'] = date('Y-m-d',$item['pub_time']);
            return $item;

    	           });
    	
             }
			return json($job);
    }
    public function upload(){
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file('image');
        
        // 移动到框架应用根目录/public/uploads/ 目录下
        // 
        if($file){
            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){

              echo  $img=  '/uploads/'.str_replace("\\", "/",$info->getSaveName());exit();
                
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }

//          return json($img);
    }else{
    	return json('222');

            return json($img);
        }

    }


}
