<?php 
namespace app\index\controller;
use think\Db;
/**
 * 
 */
class Address extends \think\Controller
{

	
	public function getmap(){
		$upid=input('up_id');
		$level = input('level');

		$getmap = db('area')->where(['level'=>$level,'up_id'=>$upid])->select();
		if($getmap){
			return json($getmap);
		}else{
			return json("2");
		}
	}
}


 ?>