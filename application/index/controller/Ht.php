<?php
namespace app\index\controller;
class Ht extends \think\Controller{
	public function kc_list()   //课程推荐
	{
    	$list=db('kecheng')->where("is_delete =2  and status=1 and is_home=1")->order('id desc')->limit(6)->select();
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

  public function my_collection(){
  	  $uid=input('uid');
	  $list=db('topic_likes')->where("l.uid=$uid")->alias('l')->join('topic t','l.tid=t.id')
	  ->order('l.id desc')->field('t.title,t.image,t.author,t.id')->select();
	  
	  return json($list);
  }
  public function my_recruit(){
  	  $uid=input('uid');
	 
	  $list=db('job_likes')->where("l.uid=".$uid)
	  ->alias('l')
	  ->join('job j','l.tid=j.id')
	  ->join('user u',"u.uid=l.uid")->order('l.id desc')
	  ->field('j.id,j.category,j.money,j.address,j.people,j.xueli,j.people,j.people,u.username,u.tximg')
	  ->select();
	  
	  
	  
	  
	  return json($list);
  } 
  	public function fabulous_curriculum(){    //课程收藏
		$uid=input('uid');
		$tid=input('tid');
		$time=time();
		
		$list_see=db('kecheng_likes')->where(["tid"=>$tid,"uid"=>$uid])->find();
		
		if($list_see){
		db('kecheng_likes')->where(["tid"=>$tid,"uid"=>$uid])->delete();
		db('kecheng')->where("id=$tid")->setDec('likes');
		 return json("1");
		}
		else{
		db('kecheng_likes')->insert(['uid'=>$uid,'tid'=>$tid,'time'=>$time]);
		db('kecheng')->where("id=$tid")->setInc('likes');
		 return json("2");
		}
	}
	
	
	
	public function see_curriculum(){   //查询课程收藏
		$uid=input('uid');
		$tid=input('tid');	          
		$list=db('kecheng_likes')->where(["tid"=>$tid,"uid"=>$uid])->find();
		if($list){
			return json('2');
		}
		else{
			return json('1');
		}
		
	}
  
    public function my_curriculum(){
  	  $uid=input('uid');
	  $list=db('kecheng_likes')->where("l.uid=$uid")->alias('l')->join('kecheng k','l.tid=k.id')
	  ->order('l.id desc')->field('k.kecheng_name,k.image,k.money,k.school,k.id')->select();
	  
	  return json($list);
  }   
   
   public function  search(){   //搜索
   	$content=input('search');
	$list_user=db('user')->where('username','like',"%".$content."%")->alias('u')
	                     ->join('vertify v','u.uid=v.uid' )
	                     ->field('u.*,v.type,v.id')->select();  //用户
	
	
	
	$list_zp=db('job')->where('category','like',"%".$content."%")->alias('j')
	                  ->join('user u','j.uid=u.uid' )
	                  ->field('j.*,u.username,u.tximg')->order('j.id desc')->select(); //招聘
	
	
	
    $lit_wz=db('topic')->where('title','like',"%".$content."%")->alias('t')
                       ->join('user u','t.authorid=u.uid' )
                       ->field('t.*,u.username')->order('t.id desc')->select(); //文章
                       
               foreach($lit_wz as $key=>$lit_wz_xg){
               	    $lit_wz[$key]['viewtime'] = date('Y-m-d',$lit_wz_xg['viewtime']);
               }        
               //错误的思路
               //$lit_wz_xg['viewtime'] = date('Y-m-d',$lit_wz_xg['viewtime']);
			   //想的是替换掉原来的，在把这个  $lit_wz_xg ==  $lit_wz 然后我就卡住了，
			   //以为我不知道怎么让前面等于后面 而且viewtime谁=谁不知道
                       
					   
					   
                       
    $list_kc=db('kecheng')->where('kecheng_name','like',"%".$content."%")->select(); //课程                   
    
    return json(['user'=>$list_user,'zp'=>$list_zp,'wz'=>$lit_wz,'kc'=>$list_kc]);
   }
   
   public function qd_text(){
   	$list=db('text_qd')->select();
	 return json($list);
   }
   public function qd_img(){
   	  $list=db('imgage')->select();
	 return json($list);
   }
   public function lb_img(){
   	  $list=db('image')->select();
	 return json($list);
   }
   
  
}
  
?>