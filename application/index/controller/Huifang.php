<?php

defined ( 'BASEPATH' ) or exit ( 'No direct script access allowed' );

class Huifang extends CI_Controller {

	var $whitelist;

	public function __construct() {

		$this->whitelist = "student,teacher,two,getlslist,dohf,checkName,chaxun,djcx";

		parent::__construct ();
 		 

	}

	function student() {

		$navtitle = "回访学生";
		if ($this->user ['uid'] == 0 || $this->user ['uid'] == null) {
			$this->message ('你不是教师，也不是机构，不能'.$navtitle, 'BACK');
		}
		include template ( "huifang_one" );

	}

	function chaxun() {

		$navtitle = "登记查询";
		if ($this->user ['uid'] == 0 || $this->user ['uid'] == null) {
			$this->message ('你不是教师，也不是机构，不能'.$navtitle, 'BACK');
		}
		include template ( "huifang_chaxun" );

	}
	function teacher() {

		 
		$navtitle = "登记回访数据";
		if ($this->user['vertify']['type'] != 1) {
			$this->message ('你不是机构，不能'.$navtitle, 'BACK');
		}

		include template ( "huifang_two" );

	}

	public function two()
	{
		if ($this->user['vertify']['type'] != 1) {
			$this->message ('你不是机构，不能'.$navtitle, 'user/default' );
		}

		$data['uid'] = $this->user ['uid'];
		// $data['student_list'] = $this->input->post ( 'student_list' );
		$data['uname'] = $this->input->post ( 'uname' );
		$data['status'] = $this->input->post ( 'status' );
		$edit_id = $this->input->post ( 'edit_id' );
		$data['kemu'] = $this->input->post ( 'kemu' );
		$data['start_time'] = strtotime($this->input->post ( 'start_time' ));
		if ($edit_id>0) {
			 $this->db->where ( 'id', $edit_id);

			$this->db->update( 'huifang', $data );
			$hf_id = $edit_id;

		}else{
		 $this->db->insert ( 'huifang', $data );
$hf_id = $this->db->insert_id();

		}
 
		$this->db->query ( "update " . $this->db->dbprefix . "vertify set is_huifang=1 WHERE name='".$data['uname']."' and type = 0" );

		if ($data['status'] ==1) {
			 $stu_list = $this->input->post ( 'student_list' );
		 if ($stu_list) {
		 	// preg_match_all('/(.*?!\d)(\d{11})*/', $stu_list, $all);
		 	// print_r($all);exit();
		 	$tmp_stu_a = explode(1, trim($stu_list));
			$n = 0;
			$stu_a = []; 
			foreach ($tmp_stu_a as $key => $value) {
				if ($key == 0) {
					$stu_a[$n]['xm'] = $value;
				}elseif($key != 0 && $key<count($tmp_stu_a)-1){
					$r = preg_split('/\d/', $value);
					$last = strlen($str) - strlen(end($r));
					$next_xm  = substr($value, $last);
					if (is_numeric($value)) {
						$stu_a[$n]['mobile'] .= '1'.$value;
						
					}elseif($value){

						$stu_a[$n]['mobile'] .= '1'.str_replace($next_xm, '', $value);
						$stu_a[++$n]['xm'] =  str_replace(array("\r\n", "\r", "\n"), "",$next_xm);
						
					}else{
						$stu_a[$n]['mobile'] .= '1';
					}
					
					 
				}else{
					$stu_a[$n]['mobile'] .= '1'.$value;
				}
				
			}
			unset($tmp_stu_a);
		 }  
		 if($stu_a){
		 	if ($edit_id > 0) {
		 		$this->db->query( "delete from  `" . $this->db->dbprefix . "huifang_student`    where hf_id=".$hf_id  );
		 	}
		 	$pl_a = [];
		 	foreach ($stu_a as $key => $value) {
		 		$s_data['hf_id'] = $hf_id;
		 		$s_data['student_name'] = $value['xm'];
		 		$s_data['mobile'] = $value['mobile'];
		 		 $pl_a[] =$s_data;
				 
		 	}
					$this->db->insert_batch ( 'huifang_student', $pl_a );

		 	echo '添加成功';
		 }else{
		echo 'error';

		 }
		}
		
	}

	public function getlslist()
	{
		$where = " status < 2 ";
		if ($this->user['vertify']['type'] == 1) {
			$where .=  " and uid=".$this->user ['uid'];
		}else{
			$where .=  " and uname='".$this->user['username']."'  " ;
		}
		$query = $this->db->query ( "SELECT * FROM `" . $this->db->dbprefix . "huifang` where  $where  order by id desc" );
		 
		foreach ( $query->result_array () as $question ) {
			$commont_list [$question['uname']]['uname']= $question['uname'];
			$question['student_list'] = $this->getlslist_xs($question['id']);
			$commont_list [$question['uname']]['list'][] = $question;
		}
		echo json_encode(array_values($commont_list));
	}
	public function getlslist_xs($hf_id)
	{
		
		$query = $this->db->query ( "SELECT * FROM `" . $this->db->dbprefix . "huifang_student` where hf_id=".$hf_id."   order by id asc"  );
		 
		foreach ( $query->result_array () as $question ) {
			 
			$commont_list [] = $question;
		}
		return $commont_list;
	}

	public function dohf()
	{ 
		if ($this->user ['uid'] == 0 || $this->user ['uid'] == null) {
			$this->message ('你不是教师，也不是机构，不能提交回复', 'BACK');
		}
		$data['uid'] = $this->user ['uid']; 
		$edit_id = $this->input->post ( 'edit_id' );
		$uname = $this->input->post ( 'uname' );
		$student_list = json_decode($this->input->post ( 'student_list' ),true);

		$zt = ['差','较差','一般','良好','优秀'];
 

 		$cfz = $this->db->query ( "SELECT credit2 FROM " . $this->db->dbprefix . "user WHERE   uid=".$this->user ['uid'] )->row_array();
 		if (count($student_list)*1 > $cfz['credit2']) {
		 	echo json_encode(['status'=>2,'cfz'=>$cfz['credit2'],'rs'=>count($student_list)]);exit();
 			
 		}

 		$uinfo = $this->db->query ( "SELECT v.name FROM " . $this->db->dbprefix . "user u left join " . $this->db->dbprefix . "vertify v on u.uid=v.uid WHERE   u.username='".$uname."' " )->row_array();

 		if ($uinfo) {
 			// echo $uinfo['name'];
 			$uname = substr($uinfo['name'], 0,3);
 			// echo "update `" . $this->db->dbprefix . "user` set credit2= credit2-0.5  where uid=".$this->user ['uid'];
 			// exit();
 		}

		 foreach ($student_list as $key => $value) {
		 	$this->db->query("update `" . $this->db->dbprefix . "huifang_student` set zhuangtai= '".$value['zhuangtai']."',chengxiao= '".$value['chengxiao']."',jilv= '".$value['jilv']."',other_msg= '".$value['other_msg']."'  where id=".$value['id']);

			$this->db->query("update `" . $this->db->dbprefix . "user` set credit2= credit2-1 where uid=".$this->user ['uid']);
		 

		   sendmsg($value['mobile'],'#school_class#='.$uname.'&#name#='.$value['student_name'].'&#content#=纪律'.$zt[$value['jilv']-1].'、成效'.$zt[$value['chengxiao']-1].'、状态'.$zt[$value['zhuangtai']-1].','.$value['other_msg']);

		   $this->db->query("insert `" . $this->db->dbprefix . "huifang_log` (hf_id,student_id,send_time,zhuangtai,chengxiao,jilv,other_msg,student_name) values(".$value['hf_id'].",".$value['id'].",".time().",'".$value['zhuangtai']."','".$value['chengxiao']."','".$value['jilv']."','".$value['other_msg']."','".$value['student_name']."') ");
		 
		 }
		 echo json_encode(['status'=>1]);
 
		
	}

	// 登记查询
	public function djcx()
	{
		$where = " h.status < 2 ";
		if ($this->user['vertify']['type'] == 1) {
			$where .=  " and h.uid=".$this->user ['uid'];
		}else{
			$where .=  " and h.uname='".$this->user['username']."'  " ;
		}
		$query = $this->db->query ( "SELECT h.*,l.send_time,l.zhuangtai,l.chengxiao,l.jilv,l.other_msg,l.student_id FROM `" . $this->db->dbprefix . "huifang` h right join  `" . $this->db->dbprefix . "huifang_log` l on h.id = l.hf_id  where  $where  order by h.id desc" );
		 
		 $stu_a = [];
		foreach ( $query->result_array () as $question ) {
			// $commont_list[$question['send_time']]['send_time']=  date('Y-m-d H:i:s',$question['send_time']);

			$stu_a[$question['send_time']][] = $this->getlslist_xs_log($question);
			// $question['send_time']= date('Y-m-d H:i:s',$question['send_time']);
			$question['student_list'] = $stu_a[$question['send_time']];
			$commont_list[$question['hf_id']]['uname'] = $question['kemu'];
			$commont_list[$question['hf_id']]['list'][$question['send_time']] = $question;
		}

		echo json_encode(array_values($commont_list));
	}
	public function getlslist_xs_log($hf)
	{
		 
		return $this->db->query ( "SELECT  * FROM   `" . $this->db->dbprefix . "huifang_log`   where student_id=".$hf['student_id'].' and send_time='.$hf['send_time']   )->row_array();
		 
		 
		 
	}
	public function checkName()
	{
		$uname = $this->input->post ( 'uname' ); 

		$info = $this->db->query ( "SELECT uid FROM " . $this->db->dbprefix . "user WHERE username='$uname' " )->row_array();

		if ($info['uid'] > 0) {
			echo 1;
		}else{
			echo 2;
		}
	}
 
}
 


function sendmsg($mobile,$tpl_value)
{
	$sendUrl = 'http://v.juhe.cn/sms/send'; //短信接口的URL
 
	  //
	$smsConf = array(
	    'key'   => 'd6481fdd36387e34b9b43c2bbd573aff', //您申请的APPKEY
	    'mobile'    =>  $mobile, //接受短信的用户手机号码
	    'tpl_id'    => '107322', //您申请的短信模板ID，根据实际情况修改
	    'tpl_value' =>$tpl_value //您设置的模板变量，根据实际情况修改
	);

 




	 
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

  


?>