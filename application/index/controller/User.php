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

	// 获取验证码
	public function getYzm(){
		$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
  
		// 随机验证码
		$verfiy_code = rand(1000,9999);
		$mobile = input('mobile');
		$smsConf = array(
		    'key'   => '32277140f35b8236c623ebeec48c567f', //您申请的APPKEY
		    'mobile'    => $mobile, //接受短信的用户手机号码
		    'tpl_id'    => '107479', //您申请的短信模板ID，根据实际情况修改
		    'tpl_value' =>'#code#='.$verfiy_code //您设置的模板变量，根据实际情况修改
		);

		// 连接数据库 
		db('verfiy')->insert([
			 'mobile'=>$mobile,
			 'code'=>$verfiy_code,
			 'status'=>1,
			]);

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
}

?>