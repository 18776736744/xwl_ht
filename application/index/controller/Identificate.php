<?php 
namespace app\index\controller;
/**
 * 
 */
class Identificate extends \think\Controller
{
	


	// 上传图片
	public function uploadImg()
	{	
		$file = request()->file('image');
		$mobile=input('mobile');
		$picNum=input('picNum');
	    // 移动到框架应用根目录/public/uploads/ 目录下
		if($file){
			$info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
			if($info){
				$picture=$info->getSaveName();
				// 头像
				if($picNum==0){
					db('user')->where('mobile',$mobile)->update(['head_photo' => $picture]);
				}
				// A面
				else if($picNum==1){
					db('user')->where('mobile',$mobile)->update(['id_cart_a' => $picture]);
				}
				// B面
				else{
					db('user')->where('mobile',$mobile)->update(['id_cart_b' => $picture]);
				}

			}else{
	            // 上传失败获取错误信息
				echo $file->getError();
			}
		}
	}


	// 上传认证的字符数据
	public function uploadChars()
	{
		$mobile=input('mobile');
		$role=input('role');
		// $province=input('province');
		// $city=input('city');
		// $area=input('area');
		$good_at=input('good_at');
		$name=input('name');
		$introuduce=input('introduce');

		$address=input('address');
		$latitude=input('latitude');
		$longitude=input('longitude');

		db('user')->where('mobile',$mobile)->update([
			'role'=>$role,
			// 'province'=>$province,
			// 'city'=>$city,
			// 'area'=>$area,
			'name'=>$name,
			'introduce'=>$introuduce,
				// 'address'=>$address,
			'lat'=>$latitude,
			'lng'=>$longitude,
			'good_at'=>$good_at
		]);
	}

	// 上传地区
	public function uploadRegion()
	{
		$mobile=input('mobile');
		$province=input('province');
		$city=input('city');
		$area=input('area');

		db('user')->where('mobile',$mobile)->update([
			'province'=>$province,
			'city'=>$city,
			'area'=>$area,
		]);
	}
}

?>