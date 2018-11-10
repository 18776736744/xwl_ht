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
    public function pur_class(){   //购课须知
    	 $xz=input('xz');
    	$list=db('setting')->where("k='$xz'")->find();
		return json($list);
    }
    
	public function add_num(){  //点进文章加一
	    $id=input('id');
		$list=db('topic')->where("id=$id")->setInc('views');
		return json($list);
		
	}
    public function kecheng(){  //上传课程
   	    $uid=input('uid');
		$kecheng_name =input('kecheng_name');
		$age_range =input('age_range');
		$class_type =input('class_type');
		$class_zhidu =input('class_zhidu');
		$money=input('money');
		$times=input('times');
		$school =input('school');
		$teacher=input('teacher');
		$description =input('description');
		$image =input('image');
		$chengnuo =input('chengnuo');
		
		$list=db('kecheng')->insert(['uid'=>$uid,'kecheng_name'=>$kecheng_name,
		    'age_range'=>$age_range,'kecheng_name'=>$kecheng_name,
		    'class_type'=>$class_type,'class_zhidu'=>$class_zhidu,
		    'money'=>$money,'start_time'=>$times,
		    'school'=>$school,'teacher'=>$teacher,
		    'description'=>$description,'image'=>$image,
		    'chengnuo'=>$chengnuo
		    ]);
			
		return json($list);
   }
	
	public function fabulous_recruit(){    //招聘收藏
		$uid=input('uid');
		$tid=input('tid');
		$time=time();
		
		$list_see=db('job_likes')->where(["tid"=>$tid,"uid"=>$uid])->find();
		
		if($list_see){
		db('job_likes')->where(["tid"=>$tid,"uid"=>$uid])->delete();
		db('job')->where("id=$tid")->setDec('likes');
		 return json("1");
		}
		else{
		db('job_likes')->insert(['uid'=>$uid,'tid'=>$tid,'time'=>$time]);
		db('job')->where("id=$tid")->setInc('likes');
		 return json("2");
		}
	}
	
	
	public function see_recruit(){   //查询招聘收藏
		$uid=input('uid');
		$tid=input('tid');	          
		$list=db('job_likes')->where(["tid"=>$tid,"uid"=>$uid])->find();
		if($list){
			return json('2');
		}
		else{
			return json('1');
		}
		
	}
}
   
   
  
  
?>