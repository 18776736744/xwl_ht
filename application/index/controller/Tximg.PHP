<?php
namespace app\index\controller;
use think\Db;


class Tximg extends \think\Controller{
	public function index(){
		$path = '../public/uploads/';
		$res = $this->traversal($path);
		var_dump($res);
	}
	public function traversal($dir){
		//先判断要遍历的文件是否存在是否为目录
    if(!is_dir($dir))
    {
        return pathinfo($dir)['basename'];
    }
    $files = [];
    //打开文件夹
    if($handle = opendir($dir))
    {
        //读取文件中的内容判断是文件还是文件夹
        while(($file = readdir($handle))!=false)
        {
        	// print_r($file);exit();
        	// $files[] = $file;
            if($file != '..' && $file != '.')
            {
                //继续遍历文件夹下的子文件夹 注意路径
                $files[$file] = traversal($dir. "/".$file);
            }
            else
            {
                $files[] = $file;
            }
        }
        //关闭资源
        closedir($handle);
        return $files;
    }
    return false;
	}
}