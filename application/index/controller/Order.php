<?php
namespace app\index\controller;
use think\Db;

// 订单接口
class Order extends \think\Controller
{
     public function myorder()
    {
 
        $uid = input("uid");
        $orderlist = db("paylog")->where("touid=$uid  and type in ('paykecheng','topaykecheng') ")->order('id desc')->select();

        foreach ($orderlist as $key => $value) {
            $mod = $this->getkecheng ( $value ['typeid'] );
            $orderlist[$key]['keinfo'] = $mod;
            if (  $orderlist[$key]['keinfo']['jk_time']) {
             $orderlist[$key]['keinfo']['jk_time'] =date('Y-m-d H:i', $orderlist[$key]['keinfo']['jk_time']);
            }
            if ( $value['tk_time']) {
             $orderlist[$key]['tk_time'] =date('Y-m-d H:i',$value['tk_time']);
            } 
            if ( $value['bm_time']) {
             $orderlist[$key]['bm_time'] =date('Y-m-d H:i',$value['bm_time']);
            }
             $orderlist[$key]['time'] =date('Y-m-d H:i',$value['time']);

           $orderlist[$key]['fromusername'] = $this->getuser ( $value ['fromuid'] );

             $s_time=strtotime($mod['start_time']);
             $now=time();
             $sjc=($now -  $s_time)/86400;

              $orderlist[$key]['s_time'] = $s_time;
              $orderlist[$key]['sjc'] = $sjc;
              $orderlist[$key]['now'] = $now;
        }

        return json($orderlist);
        
    }



    public function jieke()
    {
        $typeid = input("typeid");

         $jk_time = time();
       db("paylog")->query ( "UPDATE whatsns_kecheng SET  `status`=2,jk_time=$jk_time WHERE `id`=$typeid " );

         return json("结课成功");

    }

    public function baoming()
    {
        $typeid = input("typeid");
        $uid = input("uid");

         $bm_time = time();
       db("paylog")->query ( "UPDATE whatsns_paylog SET  `status`=2,bm_time=$bm_time WHERE `typeid`=$typeid and type='topaykecheng' and touid=".$uid );
       db("paylog")->query ( "UPDATE whatsns_paylog SET  `status`=2,bm_time=$bm_time WHERE `typeid`=$typeid and type='paykecheng' and fromuid=".$uid );

        $info =db("paylog")->field ( "money,touid,fromuid,id,time,order_sn")->where(" `typeid`=$typeid and type='paykecheng' and fromuid=".$uid )->find ();

        // 确认报名用户点击后直接转款
       db("paylog")->query ( "UPDATE whatsns_user SET  `jine`=jine+'".($info['money']*100)."' WHERE `uid`=".$info['touid'] );


        return json("确认报名成功");

    }

    public function doCommont()
    {
        $uid = input("uid");
         if ($uid>0) {
            $data['plr'] = $uid;
            $data['pldx'] = input( 'pldx' );
            $data['star'] = input( 'star' );
            $data['is_true_name'] = input( 'is_true_name' );
            $data['info'] = input( 'info' );
            if (empty($data['is_true_name'])) {
                unset($data['is_true_name']);
            }
            $data['xingzhi'] = $data['star']<3?2:1;
            
            $data['picture']  =  input( 'image' );

                
            $data['add_time'] = time();
            
            db( 'kecheng_commont')->insert($data );
 
           return json('评论成功！');
        }else{
           return json('请先登录');

        }
    }

    public function tuik()
    {
        $uid = input("uid");
        if ($uid>0) {
            $id = input("typeid");
            $type = input("type");
            if ($type >= 3) {
                $info =db("paylog")->field ( "money,touid,fromuid,id,time,order_sn")->where("`typeid`=$id and type='paykecheng' and touid=".$uid )->find ();

            }else{
                 $info =db("paylog")->field ( "money,touid,fromuid,id,time,order_sn")->where("`typeid`=$id and type='paykecheng' and fromuid=".$uid )->find ();


            }
                $order_sn =  $info['order_sn'] ;

             $tk_time = time();
             if (empty($info)) {
                exit();
             }
             // 开课7日内，资金冻结期
            if ($type == 1) {
                // 用户97%和学校3%，分账

                
                
                // 买卖家直接分成
                 db("paylog")->query ( "UPDATE whatsns_user SET  `jine`=jine+'".($info['money']*0.03*100)."' WHERE `uid`=".$info['touid'] );

                 db("paylog")->query ( "UPDATE whatsns_user SET  `jine`=jine+'".($info['money']*0.97*100)."' WHERE `uid`=".$info['fromuid'] );

                  // 更改状态为退款及退款时间
           db("paylog")->query ( "UPDATE whatsns_paylog SET  `tk_status`=2,tk_time=$tk_time WHERE `typeid`=$id and type='topaykecheng' and touid=".$uid );
           db("paylog")->query ( "UPDATE whatsns_paylog SET  `tk_status`=2,tk_time=$tk_time WHERE `typeid`=$id and type='paykecheng' and fromuid=".$uid );

                 

                  // 并各发一条消息
                  
                 
                   sendMsg ( $msgfrom, 0,  $info['touid'],  "用户退款", '<a href="/kecheng/myorder">课程订单'.$order_sn.'</a>退款成功，由于是开课7天前，所以你获得误课费：'.$info['money']*0.03, attentionuser );
                   sendMsg ( $msgfrom, 0,  $info['fromuid'],  "退款成功", '<a href="/kecheng/myorder">课程订单'.$order_sn.'</a>退款成功，由于是开课7天前，所以你需支付误课费：'.$info['money']*0.03, attentionuser );
             return json( '退款成功！' );


            }elseif($type == 2){
                // 开课期间用户退款
                $tkyy = input('tkyy');
                $ksf = input('ksf');
                $qtsm = input('qtsm');
                $file_path = input('image');

 
                 

                // 开课了填写的退款理由、误课费的保存
                 db("paylog")->query ( "UPDATE whatsns_paylog SET  `tk_status`=3,tk_time=$tk_time,tkyy='$tkyy',ksf='$ksf',qtsm='$qtsm',file_path='$file_path' WHERE `typeid`=$id and type='topaykecheng' and touid=".$uid );


           db("paylog")->query ( "UPDATE whatsns_paylog SET  `tk_status`=3,tk_time=$tk_time,tkyy='$tkyy',ksf='$ksf',qtsm='$qtsm',file_path='$file_path' WHERE `typeid`=$id and type='paykecheng' and fromuid=".$uid );

                
             

                  // 并各发一条消息
                 
                $msgfrom =  '管理员';
                 
                   sendMsg ( $msgfrom, 0,  $info['touid'],  "退款申请", '<a href="/kecheng/myorder">课程订单'.$order_sn.'</a>正在进行退款申请，请及时处理', attentionuser );
                    
                   sendMsg ( $msgfrom, 0,  $info['fromuid'],  "退款正在处理中", '<a href="/kecheng/myorder">课程订单'.$order_sn.'</a>的退款正在处理中，请耐心等候...', attentionuser );
                    // exit();
            return json( '申请成功，请耐心等候...');
            }
            elseif ($type == 3) {
                
                $ksf = $info['ksf'];
    
                  // 退款=总金额-（3%误课费+已上课的课时费）。
                 $jigou_money = $info['money']-($info['money']*0.03+$ksf);
                 $user_money = $info['money'] - $jigou_money;
     
                db("paylog")->query ( "UPDATE whatsns_paylog SET  `tk_status`=2,tk_time=$tk_time WHERE `typeid`=$id and type='topaykecheng' and touid=".$info['fromuid']  );
           db("paylog")->query ( "UPDATE whatsns_paylog SET  `tk_status`=2,tk_time=$tk_time WHERE `typeid`=$id and type='paykecheng' and fromuid=".$info['fromuid'] );


                 db("paylog")->query ( "UPDATE whatsns_user SET  `jine`=jine+'".($jigou_money*100)."' WHERE `uid`=".$info['touid'] );

                 db("paylog")->query ( "UPDATE whatsns_user SET  `jine`=jine+'".($user_money*100)."' WHERE `uid`=".$info['fromuid'] );


                  
                $msgfrom =  '管理员';
                 
                    
                   sendMsg ( $msgfrom, 0,  $info['touid'],  "用户退款", '<a href="/kecheng/myorder">课程订单'.$order_sn.'</a>退款成功，由于是开课7天期间申请退款，所以你获得误课费：'.$jigou_money, attentionuser );
                   sendMsg ( $msgfrom, 0,  $info['fromuid'],  "退款成功", '<a href="/kecheng/myorder">课程订单'.$order_sn.'</a>机构已同意退款，由于是开课7天期间申请退款，所以你需支付：'.$jigou_money.'给机构', attentionuser );
            return json(  '退款处理成功！'  );
                     
            }
            elseif ($type == 4) {
                 // 交由平台介入
                  if ($this->user['vertify']['type'] != 1) {
                    $this->message ('你不是机构，不能进行此操作', 'user/default' );
                }

                db("paylog")->query ( "UPDATE whatsns_paylog SET  status=3 WHERE `typeid`=$id and type='topaykecheng' and touid=".$info['fromuid']  );
           db("paylog")->query ( "UPDATE whatsns_paylog SET  status=3 WHERE `typeid`=$id and type='paykecheng' and fromuid=".$info['fromuid'] );

                  
                $msgfrom =  '管理员';
                 
                    
                   sendMsg ( $msgfrom, 0,  $info['touid'],  "用户退款", '<a href="/kecheng/myorder">课程订单'.$order_sn.'</a>已交由平台介入', attentionuser );
                   sendMsg ( $msgfrom, 0,  $info['fromuid'],  "正在退款", '<a href="/kecheng/myorder">课程订单'.$order_sn.'</a>已交由平台介入', attentionuser );
            return json(  '已交由平台介入！'  );
            }
        }else{
            return json(  '请先登录' );

        }
    }

    public function getkecheng($id) {

        $topic = db("kecheng")->where("id='$id'" )->find(); 
        if ($topic) { 

            $topic ['title'] =$topic ['kecheng_name'];  
        }

        return $topic; 
    }


    public function getuser($uid) {

        $user = db("user")->where("uid='$uid'" )->find();

        return $user;

    }
}
