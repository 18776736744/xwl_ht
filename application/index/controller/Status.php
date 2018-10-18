<?php 
namespace app\index\controller;
use think\Db;
/**
 * 
 */
class Status extends \think\Controller
{

	
	// 获取我的收藏的文章
	public function getcollect_wz(){
		$uid = input('uid');
		$mysc = db('topic')->alias('t')->field("t.id,t.title,t.describtion,t.image,t.author,t.authorid,t.views,t.articleclassid,t.viewtime,t.likes,t.articles,t.price,t.ispc")
				->join('topic_likes l','l.uid='.$uid)
				->where("t.id=l.tid")->order('id desc')->select();
		if ($mysc) {
			return json($mysc);
		}else{
			return json("2");
		}
	}
	// 获取机构、教师类型招聘文章
	public function wenzhanglist(){
		$order = input('limit');
		$limit="";
		if ($order) {
			$limit = "0,".$order;
		}
		$type = input('type_');
		$sele=db('vertify')->alias('v')->field("t.id,t.title,t.describtion,t.image,t.author,t.authorid,t.views,t.articleclassid,t.viewtime,t.likes,t.articles,t.price,t.ispc")
		->join('topic t','t.authorid=v.uid')
		->where('type='.$type)->order('id desc')->limit($limit)->select();
		if ($sele) {
			return json($sele);
		}else{
			return json("2");
		}
	}
	// 收藏关注
	public function index(){
		// $uid主ID，cid客ID，id类ID
		$uid =input('uid');
		$cid =input('cid');
		$id  =input('id');
		$type= input('type_');
		$status = input('status');
		switch ($status) {
			case '1':	
				$sum="+1";

				break;
			case '2':
				$sum="-1";
				break;
			default:
				# code...
				break;
		}
		if($type=='文章'){
			return $this->follower_article($uid,$cid,$id,$status);
		}else if($type=='用户'){
			return $this->follower_user($uid,$cid,$status,$sum);
		}else if($type=='问题'){
			return $this->follower_question($uid,$cid,$status,$sum);
		}else if($type=='话题'){
			return $this->follower_topic($uid,$cid,$id,$status);
		}else if($type=='点赞'){
			return $this->follower_zan($uid,$cid,$id,$status,$sum);
		}else{
			return;
		}
	}
	public function follower_user($uid,$cid,$status,$sum){
		switch ($status) {
			case '1':	
				$username = db('user')->where('uid='.$uid)->value('username');
				$user_status =db('user_attention')->insert
						([
							'followerid'=>$uid,
							'uid'=>$cid,
							'follower'=>$username,
							'time'=>time()
						]);
				break;
			case '2':
				$user_status =db('user_attention')
				         ->where(['uid='=>$cid,'followerid'=>$uid])->delete();
				break;
			default:
				# code...
				break;
		}
		
		if($user_status){
			$up = db('user')->where('uid='.$uid)->update
			([
				'attentions'=>Db::raw('attentions'.$sum),
				'followers' =>Db::raw('followers'.$sum)
			]);
			if ($up) {
				return json('1');
			}
		}else{
			return json('2');
		}
	}
	public function follower_article($uid,$id,$status,$sum){
		if($status=="1"){
			$art= db('topic_likes')->insert(['uid'=>$uid,'tid'=>$id,'time'=>time()]);
		}elseif ($status=="2") {
			$art= db('topic_likes')->where(['uid'=>$uid,'tid'=>$id])->delete();
		}
		$addart =db('topic')->where('id='.$id)->update('likes'.$sum);

		if($addart){
			return json("1");
		}else{
			return json("2");
		}
	}
	// 问题
	public function follower_question($uid,$cid,$status,$sum){
		if($status=='1'){
			$que=db('favorite')->insert(['uid'=>$uid,'qid'=>$id,'time'=>time()]);
			
		}else{
			$que=db('favorite')->where(['uid'=>$uid,'qid'=>$id])->delete();
		}
		if($que){
			$queadd=db('question')->where('id='.$id)->update('attentions'.$sum);
			if($queadd){
				return json("1");
			}else{
				return json("2");
			}
		}
	}
	public function follower_topic(){
		
	}
	// 点赞
	public function follower_zan($uid,$cid,$id,$status,$sum){
		$data=[
				'authorid'=>$cid,
				'tid'=>$id,
				'cid'=>$uid
				];
		$pd =db('article_comzan')->where($data)->select();
		if ($pd) {
			return json("已点赞");
		}else{
			$zan=db('article_comzan')->where($data)->insert();
			if ($zan) {
				$editzan = db('articlecomment')->where(['authorid'=>$cid,'tid'=>$id])->update('supports'.$sum);
				if ($editzan) {
				return json("1");	
				}else{
					return json("2");
				}
			}
		}
	}
}
