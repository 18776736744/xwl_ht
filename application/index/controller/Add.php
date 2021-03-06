<?php
namespace app\index\controller;
class Add extends \think\Controller{
		public function picture(){
 		$qd_img_list=db('imgage_qd')->select();
		$this->assign('qd_img_list',$qd_img_list);
		return $this->fetch();
 	}
		
		public function slide(){
 		$lb_list=db('image')->select();   
		 $this->assign('lb_list',$lb_list);
		
		return $this->fetch();
 	}
		
		public function index(){   //在index显示
		$qd_text=db('text_qd')->select();
		$this->assign('qd_text',$qd_text);
		return $this->fetch();
 	}
	  public function save(){   //保存轮播图片
	  	   $file = request()->file('img');
		   
		   if($file !=''){
		   	 $info = $file->move(ROOT_PATH.'public'.DS.'uploads');  //一张图片储存的地址
			   	 if($info){
			   	 	$list=db('image')->insert([
							"image"=>$info->getSaveName(),
							"address"=>input("text")
							]); //上传数据库地址
			   	 	 $this->success('添加成功','https://www.xiaowailian.com/xwl_ht/public/index.php?s=/index/add/slide');
			   	 }
		   }
		   else{
		   	$this->error("图片不能为空");
		   }
		   
	   	
    }
	  public function save_two(){   //保存签到页面图片
	  	 $file = request()->file('img');
		   
		   if($file !=''){
		   	 $info = $file->move(ROOT_PATH.'public'.DS.'uploads');  //一张图片储存的地址
			   	 if($info){
			   	 	$list=db('imgage_qd')->insert(["image_qd"=>$info->getSaveName()]); //上传数据库地址
			   	 	 $this->success('添加成功','https://www.xiaowailian.com/xwl_ht/public/index.php?s=/index/add/picture');
			   	 }
		   }
		   else{
		   	$this->error("图片不能为空");
		   }
	  }
	  public function save_three(){  //保存签到页面的格言
	  	
		$text=input('text');
		if($text){
			$list=db('text_qd')->insert(['text'=>$text]);
			$this->success('添加成功','https://www.xiaowailian.com/xwl_ht/public/index.php?s=/index/add/index');
		}
		else{
			
		}
			$this->error("格言不能为空");
	  }
	  
	  
	  public function del_position(){  //删除轮播
	  	  if(!empty($_POST)){
	  	  	  
	  	     foreach($_POST['del_index'] as $value){
	  	     	
	  	     	$picture=db('image')->where("id=$value")->value('image');
				$img_url=ROOT_PATH."public/uploads/".$picture;
				$imgPath =str_replace("\\", "/", $img_url);
				unlink($imgPath);
				
				db('image')->where("id=$value")->delete();
	  	     }	
			 $this->success('删除成功','https://www.xiaowailian.com/xwl_ht/public/index.php?s=/index/add/slide');    // 成功返回
			
			
			
	  	 }
		else{
			$this->error("请选择");
		}
    }
	   public function del_position_two(){  //删除签到的图片
	  	  if(!empty($_POST)){
	  	  	  
	  	     foreach($_POST['del_index'] as $value){
	  	     	
	  	     	$picture=db('imgage_qd')->where("id=$value")->value('image_qd');
				$img_url=ROOT_PATH."public/uploads/".$picture;
				$imgPath =str_replace("\\", "/", $img_url);
				unlink($imgPath);
				
				db('imgage_qd')->where("id=$value")->delete();
	  	     }	
			 $this->success('删除成功','https://www.xiaowailian.com/xwl_ht/public/index.php?s=/index/add/picture.html');    // 成功返回
			
			
			
	  	 }
		else{
			$this->error("请选择删除商品");
		}
    }
	  public function del_index(){
	  	 if(!empty($_POST)){
	  	     foreach($_POST['del_index'] as $value){
				db('text_qd')->where("id=$value")->delete();
	  	     }	
			 $this->success('删除成功','https://www.xiaowailian.com/xwl_ht/public/index.php?s=/index/add/index');    // 成功返回
			
	  	 }
		else{
			$this->error("请选择删除商品");
		}
	  }
}
?>