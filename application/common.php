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
		require(ROOT_PATH.'/vendor/topthink/think-image/src/image/Exception.php');
		require(ROOT_PATH.'/vendor/topthink/think-image/src/Image.php');
		$imgPaths = \think\Image::open(ROOT_PATH.'public'.DS.$imgPath);  //要绝对路径
		$imgPaths->water(ROOT_PATH.'public/static/sy.png',\think\Image::WATER_SOUTHEAST,50)->save(ROOT_PATH.'public/'.$imgPath); 
		                             // 水印的图片                         位置      透明度           然后保持回去
		return json(['imgPath' => $imgPath]);                          
	} else {
	    // 上传失败，获取错误信息
		return json(['error' => $image->getError()]);
	}    
}


/**
 * 请求接口返回内容
 * @param  string $url [请求的URL地址]
 * @param  string $params [请求的参数]
 * @param  int $ipost [是否采用POST形式]
 * @return  string
 */
function juhecurl($url,$params=false,$ispost=0){
    $httpInfo = array();
    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_HTTP_VERSION , CURL_HTTP_VERSION_1_1 );
    curl_setopt( $ch, CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows NT 5.1) AppleWebKit/537.22 (KHTML, like Gecko) Chrome/25.0.1364.172 Safari/537.22' );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT , 30 );
    curl_setopt( $ch, CURLOPT_TIMEOUT , 30);
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER , true );
    if( $ispost )
    {
        curl_setopt( $ch , CURLOPT_POST , true );
        curl_setopt( $ch , CURLOPT_POSTFIELDS , $params );
        curl_setopt( $ch , CURLOPT_URL , $url );
    }
    else
    {
        if($params){
            curl_setopt( $ch , CURLOPT_URL , $url.'?'.$params );
        }else{
            curl_setopt( $ch , CURLOPT_URL , $url);
        }
    }
    $response = curl_exec( $ch );
    if ($response === FALSE) {
        //echo "cURL Error: " . curl_error($ch);
        return false;
    }
    $httpCode = curl_getinfo( $ch , CURLINFO_HTTP_CODE );
    $httpInfo = array_merge( $httpInfo , curl_getinfo( $ch ) );
    curl_close( $ch );
    return $response;
}


function getUinfo($openid)
{
    $info = db('user')->field('*')
                    ->where('openid', $openid)
                    ->find();
    $info['vertify'] = db('vertify')->field('type,name,status')
            ->where('uid='.$info['uid'])
            ->find();
    return $info;
}

function sendMsg($msgfrom, $msgfromid, $msgtoid, $subject, $message,$typename='') {

    $msgtoid = intval ( $msgtoid );

    $user = db("user")->field('isnotify')->where("uid='$msgtoid'" )->find ();


    if ($user ['isnotify']) {

        $time = time ();

        $data = array ('typename' => $typename,'from' => $msgfrom, 'fromuid' => $msgfromid, 'touid' => $msgtoid, 'subject' => $subject, 'time' => $time, 'content' => $message );

        db('message')->insert($data );


    } else {

        return 0;

    }

}