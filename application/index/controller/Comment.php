<?php 
namespace app\index\controller;
use think\Db;


class Comment extends \think\Controller{
	// 获取对应文章评论数据
	public function article_comment(){
		$type=input('type_');
		$uid = input('uid'); //我评论
		$id = input('id');
		$cid = input('cid');//被评论
		$tid = input('tid');//一级评论对应二级id
		$data=['a.authorid'=>$uid,'a.tid'=>$id,'a.aid'=>$cid];
		if($type=='my'){
			// t.authorid与a.authorid冲突，设置输出一个
			$sele_com = db('article_comment')->alias('a')
			->field('t.id,t.title,t.describtion,t.image,t.views,t.articleclassid,t.viewtime,t.likes,t.articles,t.price,t.ispc,a.*,r.*')
			->join('topic t','t.id='.$id)
			->join('articlecomment r','r.tid='.$tid)
			->where($data)->select();
		}else if($type=='he'){
			$sele_com = db('article_comment')->alias('a')
			->field('t.*,a.tid,a.content,a.time,a.aid,r.*')
			->join('topic t','t.id='.$id)
			->join('articlecomment r','r.tid='.$tid)
			->where($data)->select();
		}

		
		if ($sele_com) {
			return json($sele_com);
		}else{
			return json("2");
		}
	}
	// 删除评论
	public function comment_delete(){
		$type=input('type_');
		$uid=input('uid');
		$cid=input('cid');
		$tid=input('tid');//文章id
		$id = input('id');//一级对应二级id
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
}

 ?>