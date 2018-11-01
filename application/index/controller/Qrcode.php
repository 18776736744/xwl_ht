<?php
namespace app\index\controller; 
use think\Request;
use think\Db;

/**
 * 二维码接口
 */
class Qrcode extends \think\Controller {
	 //二维码生成
    public function code()
    {
          $path=input('path')?input('path'):"pages/index/index";
        $file_name =  time()."code";
        if (strstr($path,"?")) {
            $a = parse_url($path);
            $file_name = str_replace(['&','='], ['',''], $a['query']);
        }
         $img = "code/".$file_name.".jpg";
         $urls = ROOT_PATH.'public/uploads/img/'.$img;
         if (file_exists($urls)) {
               $code = [
                        'code_img'=>'/uploads/img/'.$img,
                    ];
                return json($code);
         }
            //获取token
            $appID = "wxa4499dcf136ab992";
            $appSecret = "5d6444b3d79e04cbc7217d34f3c92fe1";
            $tokenUrl= "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" .$appID ."&secret=".$appSecret;
            $getArr=array();
            $tokenArr=json_decode($this->send_post($tokenUrl,$getArr,"GET"));
            $access_token=$tokenArr->access_token;
            //生成二维码
   
            $width=500;
            $data = [
                'path'=>$path,
                "width"=>$width,
                'auto_color'=>false,
                //'line_color'=>$line_color,
            ];
            $post_data= json_encode($data,true);
            $url="https://api.weixin.qq.com/wxa/getwxacode?access_token=".$access_token;
            $result=$this->api_notice_increment($url,$post_data);
            if ($result){
              
                if(file_put_contents($urls, $result)){
                    $code = [
                        'code_img'=>'/public/xcxewm/'.$img,
                    ];
                    return json($code);
                };
            }
       
    }



    function send_post($url, $post_data,$method='POST') {
        $postdata = http_build_query($post_data);
        $options = array(
            'http' => array(
                'method' => $method, //or GET
                'header' => 'Content-type:application/x-www-form-urlencoded',
                'content' => $postdata,
                'timeout' => 15 * 60 // 超时时间（单位:s）
            )
        );
        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
        return $result;
    }

    function api_notice_increment($url,$data)
    {
        $curl = curl_init();
        $a = strlen($data);
        $header = array("Content-Type: application/json; charset=utf-8","Content-Length: $a");
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl,CURLOPT_POST,1);
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $res = curl_exec($curl);
        curl_close($curl);
        return $res;

    }
}