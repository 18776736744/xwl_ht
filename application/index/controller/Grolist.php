<?php
namespace app\index\controller;
class Grolist extends \think\Controller{
	public function getorg()
	{
    	$grolist=db("approve")->field("img")->field("name")->field("intro")->paginate(5);
    	return json($grolist);
    	


	}



}

?>