<?php 
namespace app\index\controller;
use think\Db;


class Comment extends \think\Controller{
     public function one_bc(){     //保存一级评论
          $tid= input('tid');//文章的id
          $title=input('title'); //文章标题
          $author = input('author'); //文章的人
		  $authorid = input('authorid');//评论的人	
		  $content=input('content');   //评论内容
          $list=db('articlecomment')->insertGetId([
          'tid'=>$tid,
         'authorid'=>$authorid,
         'author'=>$author,
		 'title'=>$title,
		 'time'=>time(),
		 'content'=>$content]);
		 
		 return json(['content'=>$content,'authorid'=>$authorid,'supports'=>"0",'tid'=>$tid,'id'=>$list]);
     }
	 public function one_pl(){    //查询一级评论
	 	$tid=input('id');
		$list=db('articlecomment')->where("tid=$tid")->order('id desc')->select();
		return json($list);
	 }
	  
	 public function aa(){    //查询二级评论的一级评论
	 	$id=input('id');
		$list=db('articlecomment')->where("id=$id")->find(); 
		return json($list);
	 }
	 public function bb(){    //查询二级评论的二级评论
	 	$aid=input('aid');
		$list=db('article_comment')->where("aid=$aid")->order('id desc')->select(); 
		return json($list);
	 }
	 
	public function two_wz(){   //保存二级评论中的评论二级评论
		$aid=input('aid');
		$content=input('content');
		$tid=input('tid');
		$author=input('author'); 
		$time=time();
		$authorid="1";   //先默认等于1
		$list=db('article_comment')->insert([
		 "content"=>$content,
		 "aid"=>$aid,
		 "time"=>$time,
		 "tid"=>$tid,
          "author"=>str_replace('\"','',$author),
          "authorid"=>$authorid
		]);
		return json([
		 "content"=>$content,
		
		]);
	}
	
	
	public function index(){
		// uid统一是第一视角id
		$type=input('type_');
		$uid = input('uid'); //我评论
		$id = input('id');//文章id
		$cid = input('cid');//被评论
		$tid = input('tid');//一级评论对应二级id
		if($type=='my'||$type=='he'){//查看自己的文章的评论和他的文章评论
			return $this->article_comment($type,$uid,$id,$cid,$tid);
		}if($type=='1'||$type=='2'){
			return $this->article_delete($type,$uid,$id,$cid,$tid);
		}if($type=='addmy'||$type=='addhe'){
			// 评论别人评论的评论
			$author = input('author');
			$title = input('title');
			$content =input('content');
			return $this->article_insert($type,$uid,$id,$tid,$author,$title,$cid,$content);
		}
	}

	// 获取对应文章评论数据 Tid==文章id、、查看我的和他的 uid与cid对换
	public function article_comment($type,$uid,$id){//type_ my,uid自己id,id=文章ID，cid被评论人id
			// 返回的id是articlecomment里的
			$se = db('articlecomment')->field('id')->where('tid='.$id)->select();//查找一级评论id
			// t.authorid与a.authorid冲突，设置输出一个，不要自己的id
			$sele_one=db('topic')->alias('a')
						->field("a.title,a.describtion,a.image,a.views,a.articleclassid,a.viewtime,a.likes,a.articles,a.price,a.ispc,a.tximg,r.id,r.tid,r.title,r.authorid,r.author,r.time,r.content,r.comments,r.supports")
						->join('articlecomment r','tid='.$id)
						->where(['a.authorid'=>$uid,'a.id'=>$id])->select();
						$sele_two=array();
						if(count($se)>1){
							foreach ($se as $key => $value) {
								$sele_two[$key]=db('article_comment')->where('aid='.$value['id'])->select();
							}
							
						}else{
								$sele_two=db('article_comment')->where('aid='.$se)->select();
						}
			$data=array();
			$data[0]=$sele_one;
			$data[1]=$sele_two;
			return json($data);				
	}
	// 删除评论
	public function comment_delete($type,$uid,$id,$cid,$tid){
		if($type=='2'){//2==删除自己评论别人的评论 二级评论
			$del=db('article_comment')->where(['authorid'=>$uid,'id'=>$id])->delete();
			if($del){
				$dele = db('articlecomment')->where(['authorid'=>$cid,'tid'=>$tid])->update('comments-1');
				if ($dele) {
					return json("1");
				}else{
					return json("2");
				}
			}else{
				return json("2");
			}
		}else if($type=='1'){//1==删除自己评论 一级评论
			$del=db('articlecomment')->where(['tid'=>$tid,'authorid'=>$uid,'id'=>$id])->delete();
			if($del){
				$delt=db('article_comment')->where(['aid'=>$id])->delete();
				$delete = db('topic')->where(['authorid'=>$cid,'id'=>$id])->update('articles-1');
			}else{
				return json("2");
			}
		}else{
			return json("2");
		}
	}
	// 添加评论  
	public function article_insert($type,$uid,$id,$tid,$author,$title,$cid,$content){
		// 一级评论
		if($type=='addmy'){
			$data=[
					'tid'=>$id,'authorid'=>$uid,'author'=>$author,
					'title'=>$title,'time'=>time(),'content'=>$content
			];
			 return json(['content'=>$content]);
			$one_ment = db('articlecomment')->insert($data);
			  
			if ($one_ment) {
				return json("1");
			}else{
				return json("2");
			}
			// 二级评论
		}else if ($type=='addhe') {
			$two_seleID = db('articlecomment')->where(['tid'=>$tid,'authorid'=>$cid])->value('id');
			$two_data = [
							'tid'=>$id,'authorid'=>$uid,'author'=>$author,
							'time'=>time(),'content'=>$content,'aid'=>$two_seleID
						];
			
			if ($two_seleID) {
				$two_add = db('article_comment')->where($two_data)->insert();
				if ($two_add) {
					$three_add=db('articlecomment')->where(['tid'=>$tid,'authorid'=>$cid])->update('comments+1');
				}
			}
		}
	}
	// 获取我点评过的列表,
	public function dp_comment(){
		$type = input('type_');
		$px='desc';
		if ($type=='asc') {
			$px='asc';
		}
		$d=['plr'=>input('uid'),'status'=>'2'];
		$dxid = db('commont_list')->field('pldx')->distinct(true)->where($d)->select();
		$data=array();

		$com_data = db('commont_list')->field('id,add_time,star,picture,info')->where($d)->order('id '.$px)->select();

		if(count($dxid)>1){
			foreach ($dxid as $key => $value) {
				$data[$key]=db('user')
				->field('username,uid')
				->where('uid='.$value['pldx'])->select();
			}
		}else{
			$data=db('commont_list')->alias('c')
				->join('user u','u.uid='.$dxid)
				->where($d)->select();
		}
		$all[0]=$data;
		$all[1]=$com_data;
		return json($all);
	}
	// 谁点评过我,自己看与他看页面相同
	public function dp_comment_(){
		$type = input('type_');
		$px='id desc';
		if ($type=='asc') {
			$px='star asc';
		}
		$d=['pldx'=>input('uid'),'status'=>'2'];
		$dxid = db('commont_list')->field('plr')->distinct(true)->where($d)->select();
		$data=array();

		$com_data = db('commont_list')->field('id,add_time,star,picture,info,plr')->where($d)->order($px)->select();
		$avg_data =db('commont_list')->where($d)->order($px)->avg('star');
		$avg_count =db('commont_list')->where($d)->order($px)->sum('star');
		$avg_hpl = $avg_count/count($com_data)/5*100;
		if(count($dxid)>1){
			foreach ($dxid as $key => $value) {
				$data[$key]=db('user')
				->field('username,uid,tximg')
				->where('uid='.$value['plr'])->select();
			}
		}else{
			$data=db('commont_list')->alias('c')
				->join('user u','u.uid='.$dxid)
				->where($d)->select();
		}
		$all[0]=$data;
		$all[1]=$com_data;
		$all[3]=$avg_data;
		$all[4]=$avg_hpl;
		return json($all);
	}
	// 添加点评
	public function dp_add_comment(){
		$plr = input('uid');
		$pldx= input('cid');
		$star= input('star');
		$picture=input('picture');
		$has_name=input('is_true_name');
		$info= input('info');
		$imgurl ="";
		if(!empty($picture)){//为空就判断$file
			$file = request()->file('picture');
		    // 移动到框架应用根目录/public/uploads/ 目录下
		    if($file){
		        $info = $file->rule('uniqid')->move(ROOT_PATH . '../data/attach' . DS . 'commont');
		        if($info){
		            $imgurl= "data/attach/commont/".$info->getFilename(); 
		        }else{
		            // 上传失败获取错误信息
		            echo $file->getError();
		        }

		    }
		}
		$data =	[
					'pldx'=>$pldx,
					'plr' => $plr,
					'add_time' =>time(),
					'star' =>$star,
					'picture' =>$imgurl,
					'is_true_name' =>$has_name,
					'info' =>$info
				];
		$insert = db('commont_list')->insert($data);
		if ($insert) {
			return json("1");
		}else{
			return json("2");
		}
	}
}

 ?>