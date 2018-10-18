<?php
namespace app\index\controller;
use think\Db;

// 获取头像
class Tximg extends \think\Controller{
	public function index(){
		$uid = input('uid');
		$struid = "0".$uid.".";
		$path = '../../data/avatar/000/00/35';
		$res = $this->traversal($path);
		$img = array();
		$img_type = array();
		$res = array_values($res);

		for ($i=0; $i <count($res); $i++)
		{
			if(substr($res[$i],-4) ==".jpg"||substr($res[$i],-4)==".png")
			{
					$img_type[$i] = $res[$i];
					if (strpos($img_type[$i],$struid) !==false)
					{
						$img[] ="/data/avatar/000/00/35/".$img_type[$i];
					}
			}
		}
		return json($img);	

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
            if($file != '..' && $file != '.')
            {
                //继续遍历文件夹下的子文件夹 注意路径
                $files[$file] = $this->traversal($dir. "/".$file);
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