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
        $n = 0;
	    //把每条记录的name属性提取出来
	    for($i=0;$i<count($first_cate);$i++){
            if ($n==0) {
                # code...
                $return_first_cate[$n]='全部';
            ++$n;

            } 
                $return_first_cate[$n]=$first_cate[$i]['name'];
            ++$n;
         
	    	

	    }

        $e=0;
	    for($i=0;$i<count($second_cate);$i++){
	    	if($first_cate[$i]){
	    		for($j=0;$j<count($second_cate[$i]);$j++){
                    if ($e==0) {
                        # code...
                        $return_second_cate[$e][$j]='全部';
                        ++$e;
                    }
	    			$return_second_cate[$e][$j]=$second_cate[$i][$j]['name'];
                    
	    		}
	    	}
            ++$e;

	    }
	    $second_cate=array_values($second_cate);

        $t = 0;
	    for($i=0;$i<count($third_cate);$i++){
	    	if($second_cate[$i]){
	    		for($j=0;$j<count($third_cate[$i]);$j++){
	    			if($second_cate[$i][$j]){
	    				for($k=0;$k<count($third_cate[$i][$j]);$k++){
                             if ($t==0) {
                                    # code...
                                   $return_third_cate[$t][$j][$k]='全部';
                                    ++$t;
                                }
	    					$return_third_cate[$t][$j][$k]=$third_cate[$i][$j][$k]['name'];
	    				}
	    			}
	    		}
	    	}
            ++$t;
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
		if($key){
			$where['name']=array('like','%'.$key.'%');

		}

		if($search_cate || $classify){
			$cnname = $classify?$classify:$search_cate;
			$allcate = db("category")->select();
			$ccid = db("category")->where("name='$cnname'")->value("id");
			$child_data = get_all_child($allcate,$ccid);
			if($child_data){
				$ccid = implode(",",array_values($child_data));
			}
			$cuid_a = db("user_category")->field("uid")->where("cid in (".$ccid.")")->select();
			$uids = "";
			if ($cuid_a) {
				$temp_uid_a= [];
				foreach ($cuid_a as $key => $value) {
					$temp_uid_a[]=$value['uid'];
				}
				$uids=implode(",",array_values($temp_uid_a));
			}
			
		}

		if ($search_cate) {
       		 $where['u.uid']=array('in',$uids);
			
		 
		}

    
    	if($area&&$classify && $area!='全部' && $classify!='全部'){

    		$where['address'] = array('like','%'.$area.'%');
			$where['u.uid']=array('in',$uids);
			 
    		
    	}else if($area && $area!='全部'){
            $where['address'] = array('like','%'.$area.'%');
    		 
    	}else if($classify && $classify!='全部'){
			$where['u.uid']=array('in',$uids);
    		 
    		 
    	}

        if (empty($area)) {
            $cur_area = json_decode(input('cur_area'),true);
            $where['address'] = array('like','%'. $cur_area['province'].'%');
            // city : "广州市"district : "天河区"nation : "中国"province : "广东省"street : "天府路"street_number : "天府路1号"
        }
        $grolist=db("user")->alias("u")
                ->field("u.uid,u.tximg,u.username,v.*")
                ->join("vertify v","u.uid=v.uid")
                ->where($where)->paginate(5)->each(function($item, $key){
                    $uid = $item['uid'];
                    if ($item['type'] == 1) {
                        $item['kecheng_num'] = db("kecheng")->where("uid=$uid and is_delete=2")->count();
                        $item['job_num'] = db("job")->where("uid=$uid")->count();
                    }else{
                        $item['uinfo'] = db("user")->where("uid=$uid")->find();
					}
					$item['name'] = $item['username'];
					
                    $item['plinfo'] = 
                     db("commont_list")->field ( 'sum(star) sum_s,count(id) c' )->where( array("pldx"=>$item['uid'],'status'=>2))->find();


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
		
		if($key){
			$where['name']=array('like','%'.$key.'%');
		}
		if($search_cate || $classify){
			$cnname = $classify?$classify:$search_cate;
			$allcate = db("category")->select();
			$ccid = db("category")->where("name='$cnname'")->value("id");
			$child_data = get_all_child($allcate,$ccid);
			if($child_data){
				$ccid = implode(",",array_values($child_data));
			}
			$cuid_a = db("user_category")->field("uid")->where("cid in (".$ccid.")")->select();
			$uids = "";
			if ($cuid_a) {
				$temp_uid_a= [];
				foreach ($cuid_a as $key => $value) {
					$temp_uid_a[]=$value['uid'];
				}
				$uids=implode(",",array_values($temp_uid_a));
			}
			
		}

		if ($search_cate) {
       		 $where['u.uid']=array('in',$uids);
			
		 
		}

    
    	if($area&&$classify && $area!='全部' && $classify!='全部'){

    		$where['address'] = array('like','%'.$area.'%');
			$where['u.uid']=array('in',$uids);
			 
    		
    	}else if($area && $area!='全部'){
            $where['address'] = array('like','%'.$area.'%');
    		 
    	}else if($classify && $classify!='全部'){
			$where['u.uid']=array('in',$uids);
    		 
    		 
    	}
		if (empty($area)) {
            $cur_area = json_decode(input('cur_area'),true);
            $where['address'] = array('like','%'. $cur_area['province'].'%');
            // city : "广州市"district : "天河区"nation : "中国"province : "广东省"street : "天府路"street_number : "天府路1号"
        }
		$grolist=db("user")->alias("u")
        ->field("u.uid,u.tximg,u.username,v.*")
        ->join("vertify v","u.uid=v.uid")->where($where)->paginate(5)->each(function($item, $key){
                    $uid = $item['uid'];
                    if ($item['type'] == 1) {
                        $item['kecheng_num'] = db("kecheng")->where("uid=$uid and is_delete=2")->count();
                        $item['job_num'] = db("job")->where("uid=$uid")->count();
                    }else{
                        $item['uinfo'] = db("user")->where("uid=$uid")->find();
					}
					$item['name'] = $item['username'];
                      $item['plinfo'] = 
                     db("commont_list")->field ( 'sum(star) sum_s,count(id) c' )->where( array("pldx"=>$item['uid'],'status'=>2))->find();       

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

        $n=0;
	    for($i=0;$i<count($first_cate);$i++){
             if ($n==0) {
                # code...
                $return_first_cate[$n]='全部';
            ++$n;

            } 
                $return_first_cate[$n]=$first_cate[$i]['areaname'];
            ++$n;
	    }

        $e = 0;
	    for($i=0;$i<count($second_cate);$i++){
	    	if($first_cate[$i]){
	    		for($j=0;$j<count($second_cate[$i]);$j++){
                    if ($e==0) {
                            # code...
                             $return_second_cate[$e][$j]='全部';
                        ++$e;

                        } 
                    $return_second_cate[$e][$j]=$second_cate[$i][$j]['areaname'];

	    		}
	    	}
            ++$e;

	    }
	    $second_cate=array_values($second_cate);

        $th = 0;
	    for($i=0;$i<count($third_cate);$i++){
	    	if($second_cate[$i]){
	    		for($j=0;$j<count($third_cate[$i]);$j++){
	    			if($second_cate[$i][$j]){
	    				for($k=0;$k<count($third_cate[$i][$j]);$k++){
                             if ($th==0) {
                                # code...
                            $return_third_cate[$th][$j][$k]='全部';

                                ++$th;

                            } 
                            $return_third_cate[$th][$j][$k]=$third_cate[$i][$j][$k]['areaname'];

	    				}
	    			}
	    		}
	    	}
            ++$th;
	    }
	    return json(['first_cate'=>$return_first_cate,
	    			'second_cate'=>$return_second_cate,
	    			'third_cate'=>$return_third_cate
					]);
    }

	// 学校回访二级picker
	public function getXxhf(){

		
		$uid = input('uid');
		$where = " status < 2 ";
		$where .= " and uid=" . $uid;

		$first_cate = db("huifang")->field("id,uname,kemu,uid")->where($where)->order("id desc")->select();
 
     
    	$return_first_cate=[];
    	$return_second_cate=$return_third_cate=[];
		
		foreach ($first_cate as $key => $value) {
			$return_first_cate[$value['uname']]=$value['uname'];
		}
	 

	    foreach ($first_cate as $key => $value) {
			$return_second_cate[$value['uname']][]=$value['kemu'];
		}
 
	    return json(['first_cate'=>array_values($return_first_cate) ,
	    			'second_cate'=>array_values($return_second_cate),
	    			'third_cate'=>[]
					]);
    }
    // 学校回访查询
	public function getDjcx(){

		
		$uid = input('uid');
		$where = " h.status < 2 ";
		$where .= " and h.uid=" . $uid;

	 


		$first_cate = db("huifang")->alias("h")
			->field(" h.uname,l.send_time")
			->join("huifang_log l","h.id = l.hf_id")
			->where($where)->order("h.id desc")->select();
     
    	$return_first_cate=[];
    	$return_second_cate=$return_third_cate=[];
		if($first_cate){
			foreach ($first_cate as $key => $value) {
				$return_first_cate[$value['uname']]=$value['uname'];
			}
		 
	
			foreach ($first_cate as $key => $value) {
				$return_second_cate[$value['uname']][]="Send：".date("Y-m-d",$value['send_time']);
			}
		}
		
 
	    return json(['first_cate'=>array_values($return_first_cate) ,
	    			'second_cate'=>array_values($return_second_cate),
	    			'third_cate'=>[]
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
