<?php
namespace app\index\controller;
class User extends \think\Controller{

	public function getCode(){
		$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
		  //
		$verfiy_code = rand(1000,9999); 
		$mobile = input('mobile');
		$smsConf = array(
		    'key'   => '32277140f35b8236c623ebeec48c567f', //您申请的APPKEY
		    'mobile'    =>  $mobile, //接受短信的用户手机号码
		    'tpl_id'    => '107479', //您申请的短信模板ID，根据实际情况修改
		    'tpl_value' =>'#code#='.$verfiy_code //您设置的模板变量，根据实际情况修改
		);

		$regInfo=db("user")
				  ->field("id")
				  ->where("mobile=$mobile")
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
		exit();

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

	// 注册
	public function reg(){
		$uinfo=json_decode(input('uinfo'),true);
		$mobile=$uinfo['mobile'];
		$code=$uinfo['identifycode'];
		$a;
		$info = db("verify")
				->field("status")
				->where("mobile=$mobile and code=$code")
				->find();
		$infoUser=db("user")
				  ->field("id")
				  ->where("mobile=$mobile")
				  ->find();
				  //如果验证码正确且用户表没有则把数据插入用户表
		if($info&&!$infoUser){
			db("user")->insert([
			'mobile'=>$mobile
			]);
			$a=json($info);
		}else if($infoUser){
			//如果用户表已经有了，则返回用户已存在标志
			$a=json(["exist"=>true]);
		}else {
			//如果验证码错误
			$a=json(["codeWrong"=>true]);
		}
		return $a;
	}

	// 编辑操作
	public function edit(){
		echo 'aaaa';
	}

	// 头像
	public function uploadImg(){
		// 获取表单上传文件 例如上传了001.jpg
	    $file = request()->file('image');
	    $mobile=request()->file('mobile');

	    // 移动到框架应用根目录/public/uploads/ 目录下
	    if($file){
	        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	        if($info){
	            // 成功上传后 获取上传信息
	            // db('user')-
	            // >where('mobile',1)
	            // ->update(['head_photo' => $info->getSaveName()]);
	            return json(["imgPath"=>$info->getSaveName()]);
	        }else{
	            // 上传失败获取错误信息
	            echo $file->getError();
	        }
	    }
	}

}

?>