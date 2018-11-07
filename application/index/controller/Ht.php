<?php
namespace app\index\controller;
class Ht extends \think\Controller{
	public function kc_list()   //课程推荐
	{
    	$list=db('kecheng')->order('id desc')->limit(6)->select();
		  return json($list);
	}
    public function kc_xq()   //课程详情
	{
		$id=input('id');
    	$list=db('kecheng')->where("id=$id")->find();
		$list['chengnuo'] = json_decode($list['chengnuo'],true);
		  return json($list);
	}

     public function recruit_list(){  //查询分类列表
     $name=input('name');
     $list=db('category')->where("name='$name'")->value('id');
	 return json($list);

   }

   public function recruit(){    //添加招聘
       $uid=input('uid');
       $title=input('title');
	   $classa=input('classa');
	   $address=input('address');
	   $people=input('people');
	   $money=input('money');
	   $xueli=input('xueli');
	   $sex=input('sex');
	   $xingzhi=input('xingzhi');
	   $describe=input('describe');
	   $shebao=input('shebao');
	   $gjj=input('gjj');
	   $description=input('description');
	   
	 $list=db('job')->insert([
	      'uid'=>$uid,'category'=>$title,
	      'classid'=>$classa,'address'=>$address,
	      'people'=>$people,'money'=>$money,
	      'xueli'=>$xueli,'sex'=>$sex,
	      'xingzhi'=>$xingzhi,'description'=>$describe,
	      'has_shebao'=>$shebao,'has_gjj'=>$gjj,
	      'pub_time'=>time()
	       ]);
       return json('1');
	   
   	
   }
   
   
   public function up_img(){
   	return saveImg('img'); 
   }
   
   
   public function article(){
        $title=input('title');
		$describtion=input('describtion');
		$image=input('image');
		$author=input('author');
		$uid=input('uid');
		$articleclassid=input('articleclassid');
		$readmode=input('readmode');
		$price=input('price');
		$freeconent=input('freeconent');
		$name=input('name');
		
		
		if($readmode != 1){
			$list_id=db('topic')->insertGetId([
			    'title'=>$title, 'describtion'=>$describtion,
			    'image'=>$image, 'author'=>$author,
			    'authorid'=>$uid, 'articleclassid'=>$articleclassid,
			    'viewtime'=>time(), 'readmode'=>$readmode,
			    'price'=>$price, 'freeconent'=>$freeconent,
			]);
			if($name){
		   $list=db('topic_tag')->insert([
		     'aid'=>$list_id,
		     'name'=>$name,
			 'time'=>time(),
		   ]);
			};
			return json('1');
		}
		else{
			$list_id=db('topic')->insertGetId([
			    'title'=>$title, 'describtion'=>$describtion,
			    'image'=>$image, 'author'=>$author,
			    'authorid'=>$uid, 'articleclassid'=>$articleclassid,
			    'viewtime'=>time(), 'readmode'=>$readmode
			]);
			if($name){
			$list=db('topic_tag')->insert([
		     'aid'=>$list_id,
		     'name'=>$name,
			 'time'=>time(),
		   ]);
			};
			return json('1');
			
		}
   }
}

  
?>