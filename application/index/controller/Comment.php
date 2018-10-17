<?php 
namespace app\index\controller;
use think\Db;


class Comment extends \think\Controller{
	public function index(){
		$type=input('type_');
		$uid = input('uid'); //我评论
		$id = input('id');
		$cid = input('cid');//被评论
		$tid = input('tid');//一级评论对应二级id
		if($type=='my'||$type=='he'){
			return $this->article_comment($type,$uid,$id,$cid,$tid);
		}if($type=='1'||$type=='2'){
			return $this->article_delete($type,$uid,$id,$cid,$tid);
		}if($type=='addmy'||$type=='addhe'){
			return $this->article_insert($type,$uid,$id,$cid,$tid);
		}
	}

	// 获取对应文章评论数据 Tid==文章id
	public function article_comment($type,$uid,$id){//type_ my,uid自己id,id=文章ID，cid被评论人id
			$se = db('articlecomment')->field('id')->where('tid='.$id)->select();//查找一级评论id
			// 返回的id是articlecomment里的
			// t.authorid与a.authorid冲突，设置输出一个
			$sele_one=db('topic')->alias('a')
						->field("a.title,a.describtion,a.image,a.views,a.articleclassid,a.viewtime,a.likes,a.articles,a.price,a.ispc,r.id,r.tid,r.title,r.authorid,r.author,r.time,r.content,r.comments")
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
		// $type=input('type_');
		// $uid=input('uid');
		// $cid=input('cid');
		// $tid=input('tid');//文章id
		// $id = input('id');//一级对应二级id
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
	public function article_insert($type,$uid,$id,$cid,$tid){
		if($type=='addmy'){

		}
	}
	// 获取我点评过的,
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
				->field('username,uid')
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
	public function dp_add_comment(){
		$plr = input('uid');
		$pldx= input('cid');
		$star= input('star');
		$picture=input('picture');
		$has_name=input('is_true_name');
		$info= input('info');
		if (!empty($picture)) {
			# code...
		}
	}
}

 ?>