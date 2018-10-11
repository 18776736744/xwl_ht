<?php
namespace app\index\controller;
use think\Db;


class Kecheng extends \think\Controller
{
    public function save()
    {	
        $id = db('kecheng')->insertGetId([
            'uid'=>input('uid'),
            'age_range'=>input('age_range'),
            'class_type'=>input('class_type'),
            'class_zhidu'=>input('class_zhidu'),
            'money'=>input('money'),
            'start_time'=>input('start_time'),
            'school'=>input('school'),
            'teacher'=>input('teacher'),
            'description'=>input('description'),
            'add_time'=>time()
        ]);
        return $id;
    }
    
}
