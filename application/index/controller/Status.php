<?php 
namespace app\index\controller;
use think\Db;
/**
 * 
 */
class Status extends \think\Controller
{
	
	
	public function collect(){

	}
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
			return $this->follower_question($uid,$cid,$id,$status);
		}else if($type=='话题'){
			return $this->follower_topic($uid,$cid,$id,$status);
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
	public function follower_article(){

	}
	public function follower_question(){
		
	}
	public function follower_topic(){
		
	}
}
