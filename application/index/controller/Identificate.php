<?php 
namespace app\index\controller;
/**
 * 个人资料和认证
 */
class Identificate extends \think\Controller
{
	


	// 上传图片
	public function uploadImg()
	{	
		$file = request()->file('image');
		$openid=input('openid');
		$uid=input('uid');
		$picNum=input('picNum');
	    // 移动到框架应用根目录/public/uploads/ 目录下
		if($file){
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
			if($info){
				$picture='/uploads/'.$info->getSaveName();

				// 头像
				if($picNum==0){
					db('user')->where('openid',$openid)->update(['tximg' => $picture]);
				}
				// A面
				else if($picNum==1){
					db('vertify')->where('uid',$uid)->update(['zhaopian1' => $picture]);
				}
				// B面
				else{
					db('vertify')->where('uid',$uid)->update(['zhaopian2' => $picture]);
				}
		return json(getUinfo($openid));
				
				exit();
			}else{
	            // 上传失败获取错误信息
				echo $file->getError();
			}
		}
	}


	// 上传认证的字符数据
	// 认证教师和机构
	public function uploadChars()
	{
		$uid=input('uid');
		// 认证角色
		$role=input('role');
		if ($role == 'company') {
			$type = 1;
		}else{
			$type = 0;
		}
		$good_at=input('good_at');
		$good_at_1=input('good_at_1');
		$good_at_2=input('good_at_2');
		$is_edit=input('is_edit');
		$address=input('address');
		$name=input('name');
		$introuduce=input('introduce');
 
		$latitude=input('latitude');
		$longitude=input('longitude');
		
		$f_data  = [
			'type'=>$type,
			'uid'=>$uid,
			'name'=>$name,
			'jieshao'=>$introuduce,
			'address'=>$address,
			'lat'=>$latitude,
			'lng'=>$longitude,
			'good_at'=>$good_at,
			'good_at_1'=>$good_at_1,
			'good_at_2'=>$good_at_2,
		];
		if ($is_edit) {
			db('vertify')->where("uid=$uid")->update($f_data);
		}else{
			db('vertify')->insert($f_data);
		}
		db("user_category")->where("uid=$uid")->delete();
		if($good_at){
			$cid = db("category")->where("name='$good_at'")->value("id");
			if($cid){
				db("user_category")->insert([
					"uid"=>$uid,
					"cid"=>$cid
				]);
			}
			
		}
		if($good_at_1){
			$cid = db("category")->where("name='$good_at_1'")->value("id");
			if($cid){
				db("user_category")->insert([
					"uid"=>$uid,
					"cid"=>$cid
				]);
			}
		}
		if($good_at_2){
			$cid = db("category")->where("name='$good_at_2'")->value("id");
			if($cid){
				db("user_category")->insert([
					"uid"=>$uid,
					"cid"=>$cid
				]);
			}
		}
		return json([1]);
	}
	public function getYzinfo()
	{
		$uid = input("uid");

		$info = db("vertify")->where("uid=$uid")->find();
		return json($info);
	}

	// 上传地区
	public function uploadRegion()
	{
		$openid=input('openid');
		$province=input('province');
		$city=input('city');
		$area=input('area');

		db('user')->where('openid',$openid)->update([
			'map'=>$province.'|'.$city.'|'.$area,
			'province'=>$province,
			'city'=>$city,
			'area'=>$area,
		]);

		return json(getUinfo($openid));
	}
}

?>