<?php
namespace app\index\controller;

class Categorygd extends \think\Controller{
    // 三级联动
    public function sjld()
    {
         // 要把一级分类全部查出来
    	$first_cate =  db("category")->where('grade=1')->order('id asc')->select();
    	$a =  db("category")->where('grade=1')->field('name')->order('id asc')->select();
    	$second_cate = $third_cate = [];
    	$return_first_cate=[];
    	$return_second_cate=$return_third_cate=[];
    	for($i=0;$i<count($first_cate);$i++){
    		if ($first_cate[$i]['id']) {

         	// 把一级分类的第一个里的二级子分类全部查出来
    		 	$second_cate[$i] =  db("category")->where('pid = '.$first_cate[$i]['id'].' and grade=2')->order('id asc')->select();
    		 	if($second_cate[$i]){
    		 		for($j=0;$j<count($second_cate[$i]);$j++){
    		 			if ($second_cate[$i][$j]['id']) {
    		 					$third_cate[$i][$j] =  db("category")->where('pid = '.$second_cate[$i][$j]['id'].' and grade=3')->order('id asc')->select();
    		 			}
    		 		}
    		 	}
	         // 把二级分类的第一个里的三级子分类全部查出来

    		}
	    }
	    //把每条记录的name属性提取出来
	    for($i=0;$i<count($first_cate);$i++){
	    	$return_first_cate[$i]=$first_cate[$i]['name'];
	    }

	    for($i=0;$i<count($second_cate);$i++){
	    	if($first_cate[$i]){
	    		for($j=0;$j<count($second_cate[$i]);$j++){
	    			$return_second_cate[$i][$j]=$second_cate[$i][$j]['name'];
	    		}
	    	}
	    }
	    $second_cate=array_values($second_cate);

	    for($i=0;$i<count($third_cate);$i++){
	    	if($second_cate[$i]){
	    		for($j=0;$j<count($third_cate[$i]);$j++){
	    			if($second_cate[$i][$j]){
	    				for($k=0;$k<count($third_cate[$i][$j]);$k++){
	    					$return_third_cate[$i][$j][$k]=$third_cate[$i][$j][$k]['name'];
	    				}
	    			}
	    		}
	    	}
	    }
	    return json(['first_cate'=>$return_first_cate,
	    			'second_cate'=>$return_second_cate,
	    			'third_cate'=>$return_third_cate
					]);
    }

    public function multipleSort(){
    	$area=input('area');
    	$classify=input('classify');
    	$role=input('role');
        $key=input('key');
    	$search_cate=input('search_cate');
    	$list;
            $where['status'] =  1;
			$where['type'] =  $role;
    	$grolist;
    	//分四种情况，区域和类别都不为空、区域不为空、类别不为空、区域和类别都不为空。
 	$where['name']=array('like','%'.$key.'%');

        $where['good_at_1']=array('like','%'.$search_cate.'%');
    
    	if($area&&$classify){

    		$where['address'] = array('like','%'.$area.'%');
			$where['good_at'] = array('like','%'.$classify.'%');
    		
    	}else if($area){
            $where['address'] = array('like','%'.$area.'%');
    		 
    	}else if($classify){
    		$where['good_at']=array('like','%'.$classify.'%');
    		 
    	}

        if (empty($area)) {
            $cur_area = json_decode(input('cur_area'),true);
            $where['address'] = array('like','%'. $cur_area['province'].'%');
            // city : "广州市"district : "天河区"nation : "中国"province : "广东省"street : "天府路"street_number : "天府路1号"
        }
        $grolist=db("user")->alias("u")
                ->field("u.uid,u.tximg,v.*")
                ->join("vertify v","u.uid=v.uid")
                ->where($where)->paginate(5)->each(function($item, $key){
                    $uid = $item['uid'];
                    if ($item['type'] == 1) {
                        $item['kecheng_num'] = db("kecheng")->where("uid=$uid")->count();
                        $item['job_num'] = db("job")->where("uid=$uid")->count();
                    }else{
                        $item['uinfo'] = db("user")->where("uid=$uid")->find();
                    }


                    return $item;
                });


 
    	return json($grolist);
    }
    public function distanceSort(){
    	$area=input('area');
    	$classify=input('classify');
        $key=input('key');
    	$search_cate=input('search_cate');
    	$role=input('role');
    	$lng=(float)input('lng');
    	$lat=(float)input('lat');
    	$list;
    	$grolist;

        $where['status'] =  1;
        $where['type'] =  $role;
        $where['name']=array('like','%'.$key.'%');
    	$where['good_at_1']=array('like','%'.$search_cate.'%');
    	if($area&&$classify){

            $where['address'] = array('like','%'.$area.'%');
			$where['good_at'] = array('like','%'.$classify.'%');
    		
    	}else if($area){
            $where['address'] = array('like','%'.$area.'%');
    	}else if($classify){
    		$where['good_at']=array('like','%'.$classify.'%');
    	}

		$grolist=db("user")->alias("u")
        ->field("u.uid,u.tximg,v.*")
        ->join("vertify v","u.uid=v.uid")->where($where)->paginate(5)->each(function($item, $key){
                    $uid = $item['uid'];
                    if ($item['type'] == 1) {
                        $item['kecheng_num'] = db("kecheng")->where("uid=$uid")->count();
                        $item['job_num'] = db("job")->where("uid=$uid")->count();
                    }else{
                        $item['uinfo'] = db("user")->where("uid=$uid")->find();
                    }


                    return $item;
                });

                 
    	$gap=[];
    	$length=count($grolist);
    	//算出取出的数据和当前位置的距离
    	for($i=0;$i<$length;$i++){
    		$gap[$i]=getDistance($lng,$lat,(float)$grolist[$i]['lng'],(float)$grolist[$i]['lat']);
    	}
    	//根据距离进行排序
    	for($i=0;$i<$length;$i++){
    		for($j=$i+1;$j<$length-$i;$j++){

    			if($gap[$i]>$gap[$j]){
    				$temp=$grolist[$j];
    				$grolist[$j]=$grolist[$i];
    				$grolist[$i]=$temp;
    			}
    		}
    	}
    	return json($grolist);

    }

    //获取地区的三级联动
    public function getArea(){
    	$first_cate =  db("areagd")->where('level=1')->order('id asc')->select();
    	$a =  db("areagd")->where('level=1')->field('areaname')->order('id asc')->select();
    	$second_cate = $third_cate = [];
    	$return_first_cate=[];
    	$return_second_cate=$return_third_cate=[];
    	for($i=0;$i<count($first_cate);$i++){
    		if ($first_cate[$i]['id']) {

         	// 把一级分类的第一个里的二级子分类全部查出来
    		 	$second_cate[$i] =  db("areagd")->where('parentid = '.$first_cate[$i]['id'].' and level=2')->order('id asc')->select();
    		 	if($second_cate[$i]){
    		 		for($j=0;$j<count($second_cate[$i]);$j++){
    		 			if ($second_cate[$i][$j]['id']) {
    		 					$third_cate[$i][$j] =  db("areagd")->where('parentid = '.$second_cate[$i][$j]['id'].' and level=3')->order('id asc')->select();
    		 			}
    		 		}
    		 	}
	         // 把二级分类的第一个里的三级子分类全部查出来

    		}
	    }

	    for($i=0;$i<count($first_cate);$i++){
	    	$return_first_cate[$i]=$first_cate[$i]['areaname'];
	    }

	    for($i=0;$i<count($second_cate);$i++){
	    	if($first_cate[$i]){
	    		for($j=0;$j<count($second_cate[$i]);$j++){
	    			$return_second_cate[$i][$j]=$second_cate[$i][$j]['areaname'];
	    		}
	    	}
	    }
	    $second_cate=array_values($second_cate);

	    for($i=0;$i<count($third_cate);$i++){
	    	if($second_cate[$i]){
	    		for($j=0;$j<count($third_cate[$i]);$j++){
	    			if($second_cate[$i][$j]){
	    				for($k=0;$k<count($third_cate[$i][$j]);$k++){
	    					$return_third_cate[$i][$j][$k]=$third_cate[$i][$j][$k]['areaname'];
	    				}
	    			}
	    		}
	    	}
	    }
	    return json(['first_cate'=>$return_first_cate,
	    			'second_cate'=>$return_second_cate,
	    			'third_cate'=>$return_third_cate
					]);
    }

    
}
//根据两点的经纬度计算两点间的距离
function getDistance($lng1,$lat1,$lng2,$lat2){
    	$earthRadius = 6367000; //approximate radius of earth in meters 


        $lat1 = ($lat1 * pi() ) / 180;
        $lng1 = ($lng1 * pi() ) / 180;
        $lat2 = ($lat2 * pi() ) / 180;
        $lng2 = ($lng2 * pi() ) / 180;
        /*
        Using the
        Haversine formula
        http://en.wikipedia.org/wiki/Haversine_formula
        calculate the distance
        */
        $calcLongitude = $lng2 - $lng1;
        $calcLatitude = $lat2 - $lat1;
        $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin        ($calcLongitude / 2), 2);
        $stepTwo = 2 * asin(min(1, sqrt($stepOne)));
        $calculatedDistance = $earthRadius * $stepTwo;
        return round($calculatedDistance);
    }
?>
