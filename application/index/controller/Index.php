<?php
namespace app\index\controller;
use think\Db;


class Index extends \think\Controller
{
    public function index()
    {	
        $data = db('attach')->select();
        return json_encode($data);
    }
}
