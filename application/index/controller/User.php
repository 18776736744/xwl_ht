<?php
namespace app\index\controller;


class User extends \think\Controller{
	

	// 注册操作
	public function res(){
		$uinfo = json_decode(input('uinfo'),true);

		$mobile = $uinfo['mobile'];
		$yzm = $uinfo['yzm'];

		// 链式操作
		$info = db("verfiy")
				->field("id")
				->where("mobile=$mobile and code=$yzm")
				->find();

		return json($info);
	}

	// 编辑操作
	public function edit(){
		
	}

	// 创建用户
	public function newuser(){
		$mobile = input('mobile');
		// 在user表创建新用户
		db('user')
		->insert(['mobile'=>"$mobile"]);//PHP变量要用双引号包起来

		// 在approve表创建新用户
		db('approve')
		->insert(['uid'=>"$mobile"]);

		return $mobile;
	}

 public function getCode(){
		$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
		  //
		$verfiy_code = rand(1000,9999); 
		$mobile = input('mobile');
		$openid = input('openid');
		$smsConf = array(
		    'key'   => 'd6481fdd36387e34b9b43c2bbd573aff', //您申请的APPKEY
		    'mobile'    =>  $mobile, //接受短信的用户手机号码
		    'tpl_id'    => '103196', //您申请的短信模板ID，根据实际情况修改
		    'tpl_value' =>'#code#='.$verfiy_code //您设置的模板变量，根据实际情况修改
		);

		$regInfo=db("user")
				  ->field("uid")
				  ->where("phone=$mobile and openid='$openid'")
				  ->find();
		if($regInfo){
			return json(["exist"=>true]);
		}
		$info = db("verify")
				->field("status")
				->where("mobile=$mobile")
				->find();
		if($info){
			db('verify')->where('mobile',$mobile)->setField('code',$verfiy_code);
		}else {
			db('verify')->insert([
			'mobile'=>$mobile,
			'code'=>$verfiy_code,
			'status'=>1,
			]);
		}
	 

		$content = juhecurl($sendUrl,$smsConf,1); //请求发送短信
		if($content){
		    $result = json_decode($content,true);
		    $error_code = $result['error_code'];
		    if($error_code == 0){
		        //状态为0，说明短信发送成功
		        echo "短信发送成功,短信ID：".$result['result']['sid'];
		    }else{
		        //状态非0，说明失败
		        $msg = $result['reason'];
		        echo "短信发送失败(".$error_code.")：".$msg;
		    }
		}else{
		    //返回内容异常，以下可根据业务逻辑自行修改
		    echo "请求发送短信失败";
		}
	}
	// 上传头像
	public function uploadImg()
	{
		// 获取表单上传文件 例如上传了001.jpg
	    $file = request()->file('image');
	    
	    // 移动到框架应用根目录/public/uploads/ 目录下
	    if($file){
	        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	        if($info){
	            // 成功上传后 获取上传信息 
	            echo $info->getSaveName();
	             
	        }else{
	            // 上传失败获取错误信息
	            echo $file->getError();
	        }
	    }
	}


	// 注册
	public function reg(){
		$uinfo=json_decode(input('uinfo'),true);
		$mobile=$uinfo['mobile'];
		if (empty($mobile)) {
			

			return json(88);
		}
		$openid=input('openid');
		$code=$uinfo['identifycode'];

		$a;
		$info = db("verify")
				->field("status")
				->where("mobile=$mobile and code='$code'")
				->find();
		$infoUser=db("user")
				  ->field("uid,username,map,tximg")
				  ->where("phone=$mobile")
				  ->find();
				  //如果验证码正确且用户表没有则把数据插入用户表
		if($info&&!$infoUser){
			db("user")->insert([
				'phone'=>$mobile,
				'openid'=>$openid,
			]);
			$a=json(getUinfo($openid));
		}else if($infoUser){
			db("user")
				->where("uid=".$infoUser['uid'])
				->update([
					'openid'=>$openid,
					'phoneactive'=>1
				]);

			//如果用户表已经有了，则返回用户已存在标志
			$a=json(getUinfo($openid));
		}else {
			//如果验证码错误
			$a=json(["codeWrong"=>true]);
		}
		return $a;
	}

	public function login() {
		$appid = 'wx34109c6158171baf';
		$appSecret = '557525f1e124be4d0b6b282a88b5df37';
		$code = input('code');
		$url = "https://api.weixin.qq.com/sns/jscode2session?appid=".$appid."&secret=".$appSecret."&js_code=".$code."&grant_type=authorization_code";
		$arr = vget($url);
		$arr = json_decode($arr, true);
		$openid = $arr['openid'];
      	// openid 获取 一个用户和小程序的一条线 

      	//  张三 授权 校外链小程序  18898498798
      	//  李四 授权 校外链小程序  1654654654
		// $session_key = $arr['session_key'];
		// $rawDataArr = json_decode($_GET['rawData'], true);
		// $userImg = $rawDataArr['avatarUrl'];
		// $userName=$rawDataArr['nickName'];

		// 判断用户表中是否存在该openid
		// 登录需要，手机号码
		// openid 查用户表，有没有openid
		$id = null;
		$res = db('user')->where('openid', $openid)->value('openid');
		if ($res) {
			
			$info  = getUinfo($openid);				
			return json($info);
		} else {
			//插入用户表数据
			// db('user')->insert(['openid' => $openid, 'tximg' => $userImg,'username'=>$userName]);
			// db('user')->insert(['openid' => $openid]);
			// $id = db('user')->getLastInsID();
			//插入参与表数据
		}
       
          
		// 返回用户信息
          	return json(['uid' => $id, 'openid' => $arr['openid']]);
          }

          public function getInfoTime()
          {
		$openid=input('openid');
          	
          	$info  = getUinfo($openid);				
			return json($info);
          }
 

}


/**
 * @author slzhang
 * @DateTime 2018-06-22
 * @param    [type]
 * @return   [type]
 */
function vget($url){
	$info = curl_init();
	curl_setopt($info, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($info, CURLOPT_HEADER, 0);
	curl_setopt($info, CURLOPT_NOBODY, 0);
	curl_setopt($info, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($info, CURLOPT_SSL_VERIFYHOST, false);
	curl_setopt($info, CURLOPT_URL, $url);
	$output = curl_exec($info);
	curl_close($info);
	return $output;
}
?>