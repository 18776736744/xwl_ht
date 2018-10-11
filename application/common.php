<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function saveImg($file_name) {
    // $file_type=substr($file_name,strrpos($file_name, '.'));
    // if($file_type=='jpg'||$file_type=='png'||$file_type=='gif'){
    	$image = request()->file($file_name); 	
    	// 将图片移动到框架应用根目录/public/uploads/目录
    	$info = $image->move(ROOT_PATH.'public'.DS.'uploads');
    // }elseif($file_type=='mp4'||$file_type=='mid'||$file_type=='3GP'||$file_type=='flash'){

    // }else{}

	if($info) {
	    // 成功上传，获取文件名
		$imgPath = '/uploads/'.str_replace("\\", "/", $info->getSaveName());
		return json(['imgPath' => $imgPath]);
	} else {
	    // 上传失败，获取错误信息
		return json(['error' => $image->getError()]);
	}    
}