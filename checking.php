<?php
/*验证验证码 checking.php*/

require 'information.php';
$urlStatus=200;
$iptxt = fopen("ip.dat", "a+");
$ifok = $i = 1;
$nam=0;
while (!feof($iptxt)) { 
	$now = fgets($iptxt);//获取遍历行的内容
	if(!empty($now)){
		$now_arr = explode('#',$now);
		if($now_arr[3]==$ip&&$now_arr[7]==$_G['gp_show']."\r\n"){
			$ifok = 0;
			if ($now_arr[6]==$_POST['code']&&$now_arr[5]==$_POST['email']) {
			    //验证码正确
			    $nam=1;
			    echo json_encode(array('status'=>$urlStatus,'msg'=>'Verification successful'));
			    exit;
			}
		}
	}
	$i++; 
}
if ($nam==0) {
    echo json_encode(array('status'=>$urlStatus,'msg'=>'Erification code error'));
    exit;
}

fclose($iptxt); 


php?>