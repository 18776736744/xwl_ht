<?php
namespace app\index\controller;

class Ht extends \think\Controller
{
	public function kc_list()   //课程推荐
	{
		$list = db('kecheng')->where("is_delete =2  and status=1 and is_home=1")->order('id desc')->limit(6)->select();
		return json($list);
	}
	public function kc_xq()   //课程详情
	{
		$id = input('id');
		$list = db('kecheng')->where("id=$id")->find();
		$list['chengnuo'] = json_decode($list['chengnuo'], true);
		$list['uname'] = db("vertify")->where("uid=" . $list['uid'])->value("name");

		return json($list);
	}

	public function recruit_list()
	{  //查询分类列表
		$name = input('name');
		$list = db('category')->where("name='$name'")->value('id');
		return json($list);

	}

	public function recruit()
	{    //添加招聘
		$uid = input('uid');
		$title = input('title');
		$classa = input('classa');
		$address = input('address');
		$people = input('people');
		$money = input('money');
		$xueli = input('xueli');
		$sex = input('sex');
		$xingzhi = input('xingzhi');
		$describe = input('describe');
		$shebao = input('shebao');
		$gjj = input('gjj');
		$description = input('description');

		$list = db('job')->insert([
			'uid' => $uid, 'category' => $title,
			'classid' => $classa, 'address' => $address,
			'people' => $people, 'money' => $money,
			'xueli' => $xueli, 'sex' => $sex,
			'xingzhi' => $xingzhi, 'description' => $describe,
			'has_shebao' => $shebao, 'has_gjj' => $gjj,
			'pub_time' => time()
		]);
		return json('1');


	}


	public function up_img()
	{
		return saveImg('img');
	}


	public function article()
	{
		$title = input('title');
		$describtion = input('describtion');
		$image = input('image');
		$author = input('author');
		$uid = input('uid');
		$articleclassid = input('articleclassid');
		$readmode = input('readmode');
		$price = input('price');
		$freeconent = input('freeconent');
		$name = input('name');


		if ($readmode != 1) {
			$list_id = db('topic')->insertGetId([
				'title' => $title, 'describtion' => $describtion,
				'image' => $image, 'author' => $author,
				'authorid' => $uid, 'articleclassid' => $articleclassid,
				'viewtime' => time(), 'readmode' => $readmode,
				'price' => $price, 'freeconent' => $freeconent,
			]);
			if ($name) {
				$list = db('topic_tag')->insert([
					'aid' => $list_id,
					'name' => $name,
					'time' => time(),
				]);
			};
			return json('1');
		} else {
			$list_id = db('topic')->insertGetId([
				'title' => $title, 'describtion' => $describtion,
				'image' => $image, 'author' => $author,
				'authorid' => $uid, 'articleclassid' => $articleclassid,
				'viewtime' => time(), 'readmode' => $readmode
			]);
			if ($name) {
				$list = db('topic_tag')->insert([
					'aid' => $list_id,
					'name' => $name,
					'time' => time(),
				]);
			};
			return json('1');

		}
	}
	public function pur_class()
	{   //购课须知
		$xz = input('xz');
		$list = db('setting')->where("k='$xz'")->find();
		$list['v'] = "<div>" . str_replace("\r\n", "</div><div>", $list['v']) . "</div>";

		return json($list);
	}

	public function add_num()
	{  //点进文章加一
		$id = input('id');
		$list = db('topic')->where("id=$id")->setInc('views');
		return json($list);

	}
	public function kecheng()
	{  //上传课程
		$uid = input('uid');
		$kecheng_name = input('kecheng_name');
		$age_range = input('age_range');
		$class_type = input('class_type');
		$class_zhidu = input('class_zhidu');
		$money = input('money');
		$times = input('times');
		$school = input('school');
		$teacher = input('teacher');
		$nandu = input('nandu');
		$yuan_price = input('yuan_price');
		$zhekou = input('zhekou');
		$description = input('description');
		$image = input('image');
		if (empty($image)) {
			$image = '/static/study/default.jpg';
		}
		$chengnuo = input('chengnuo');

		$list = db('kecheng')->insert([
			'uid' => $uid, 'kecheng_name' => $kecheng_name,
			'age_range' => $age_range, 'kecheng_name' => $kecheng_name,
			'class_type' => $class_type, 'class_zhidu' => $class_zhidu,
			'money' => $money, 'start_time' => $times,
			'school' => $school, 'teacher' => $teacher,
			'nandu' => $nandu, 'yuan_price' => $yuan_price,'zhekou' => $zhekou,
			'description' => $description, 'image' => $image,
			'add_time'=>time(),
			'chengnuo' => $chengnuo
		]);

		return json($list);
	}

	public function fabulous_recruit()
	{    //招聘收藏
		$uid = input('uid');
		$tid = input('tid');
		$time = time();

		$list_see = db('job_likes')->where(["tid" => $tid, "uid" => $uid])->find();

		if ($list_see) {
			db('job_likes')->where(["tid" => $tid, "uid" => $uid])->delete();
			db('job')->where("id=$tid")->setDec('likes');
			return json("1");
		} else {
			db('job_likes')->insert(['uid' => $uid, 'tid' => $tid, 'time' => $time]);
			db('job')->where("id=$tid")->setInc('likes');
			return json("2");
		}
	}


	public function see_recruit()
	{   //查询招聘收藏
		$uid = input('uid');
		$tid = input('tid');
		$list = db('job_likes')->where(["tid" => $tid, "uid" => $uid])->find();
		if ($list) {
			return json('2');
		} else {
			return json('1');
		}

	}

  public function my_collection(){   //收藏页面查看文章
  	  $uid=input('uid');
	  $list=db('topic_likes')->where("l.uid=$uid")->alias('l')->join('topic t','l.tid=t.id')
	  ->order('l.id desc')->field('t.title,t.image,t.author,t.id,t.viewtime')->select();
	  
	  foreach($list as $key=>$value){
	  	 $list[$key]['viewtime'] = date('Y-m-d',$value['viewtime']);
		  //思路：等号左边 就像小程序的 后台拿到数据点进去一样，$key就是数组的第几个，右边就是替换
	  }
	  
	  
	  return json($list);
  }
  public function my_recruit(){   //收藏页面查看课程
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

//	public function my_curriculum()
//	{
//		$uid = input('uid');
//		$list = db('kecheng_likes')->where("l.uid=$uid")->alias('l')->join('kecheng k', 'l.tid=k.id')
//			->order('l.id desc')->field('k.kecheng_name,k.image,k.money,k.school,k.id')->select();
//
//		return json($list);
//	}
  
    public function my_curriculum(){  //在收藏查看课程
  	  $uid=input('uid');
	  $list=db('kecheng_likes')->where("l.uid=$uid")->alias('l')->join('kecheng k','l.tid=k.id')
	  ->join('user u','l.uid=u.uid')->order('l.id desc')
	  ->field('k.kecheng_name,k.image,k.money,k.school,k.id,k.start_time,u.username')->select();
	  
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
                       
					   
					   
                       
    $list_kc=db('kecheng')->where('kecheng_name','like',"%".$content."%")
    ->alias('k')->join('user u','k.uid=u.uid')->field('k.*,u.username')->select(); //课程                   
    
    return json(['user'=>$list_user,'zp'=>$list_zp,'wz'=>$lit_wz,'kc'=>$list_kc]);
   }
 
   
   public function qd_text(){	
   	$list=db('text_qd')->select();
	$list_list=db('text_qd')->count();
	 return json([$list,$list_list]);
   }
   
   
   
   public function qd_img(){
   	  $list=db('imgage_qd')->select();
	  $list_list=db('imgage_qd')->count();
	    foreach($list as $key=>$value){
               	    $list[$key]['image_qd'] = str_replace('\\', '/',$value['image_qd']);
               }
	 return json([$list,$list_list]);
   }




   public function lb_img(){
   	  $list=db('image')->select();
	  foreach($list as $key=>$value){
               	    $list[$key]['image'] = str_replace('\\', '/',$value['image']);
               } 
	 return json($list);
   }
   
     public function xieyi(){//校外链平台使用协议
      $id=7;
	  $list=db('note')->where("id=$id")->find();
	  return json($list);
   	
   } 
   public function evaluate(){  //机构和老师详情页面的评价
   	 $pldx=input('pldx');
	 $plr=input('plr');
	 $star=input('star');
	 $info=input('info');
	 $picture=input('picture');
	 $is_true_name=input('is_true_name');
	 $add_time=time();
	 $xingzhi=input('xingzhi');
	 $list=db('commont_list')->insert([
	   'pldx'=>$pldx,'plr'=>$plr,'star'=>$star,'info'=>$info,
	   'picture'=>$picture,'is_true_name'=>$is_true_name,'add_time'=>$add_time,
	   'xingzhi'=>$xingzhi
	 ]);
	   return json($list);
   }
   public function reply(){
   	   $id=input('id');
	   $content=input('content');
	   $list=db('commont_list')->where("id=$id")->update(['reply'=>$content]);
	   return json($list);
   } 
}
   
   
?>