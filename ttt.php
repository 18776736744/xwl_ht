<?php
echo virtualFollow(2010,2010,10);
	
	// $uid 用户ID
	// $vid 视频ID
	// $follow 已点赞数量
  function virtualFollow($uid = 0, $vid = 0, $follow = 0)
            {
                 $guding = intval($vid % 100 + $uid % 100);

                $op = $guding % 2;
 
                $opArr = ['+', '-'];
                $increment = $uid + $vid;

                // 用户和视频ID相加小于3000就固定为3000
                // 大于8000,就固定为8000
                $increment = $increment < 3000 ? 3000 : ($increment > 8000 ? 8000 : $increment);
                $virtualFollow = ceil(($increment) / 3);

                $virtualFollow += $follow;

                eval("\$virtualFollow" . $opArr[$op] . '=' . $guding . ';');

                // 判断点赞量是否大于1W
                if ($virtualFollow >= 10000) {
                	// 大于1W就格式化为1W输出
                    $virtualFollow = number_format($virtualFollow / 10000, 1) . 'w';
                }
                return $virtualFollow;
            }
?>