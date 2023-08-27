<?php
/* 发送邮件 sendemail.php */

// 允许的前端域名
$allowedOrigin = "https://sau.cc";

// 设置允许的请求方法和请求头
header("Access-Control-Allow-Origin: $allowedOrigin");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// 处理预检请求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("Access-Control-Allow-Origin: $allowedOrigin");
    header("Access-Control-Allow-Methods: POST");
    header("Access-Control-Allow-Headers: Content-Type");
    exit;
}

// // 检查 Referer-如果要开启此项请把你的jQuery本地化
// if ($_SERVER['HTTP_REFERER'] !== $allowedOrigin) {
//     $error_response = array(
//         "status" => null,
//         "msg" => "Unauthorized Referer"
//     );
//     echo json_encode($error_response);
//     exit;
// }

// 检查邮箱是否为空且是否包含 @ 和 .
if (empty($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $error_response = array(
        "status" => null,
        "msg" => "Invalid Email Address"
    );
    echo json_encode($error_response);
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $error_response = array(
        "status" => null,
        "msg" => "Unauthorized Access"
    );
    echo json_encode($error_response);
    exit;
}

require 'information.php';
require 'max.php';


// 在发送邮件之前判断是否允许发送
if (!$allow) {
    echo json_encode(array('status' => 'error', 'msg' => 'The sending limit for the day has been reached'));
    exit; // 结束脚本运行
}


// 读取 auth.log 文件内容
$authLogContent = file_get_contents("auth.log");





// 调用 count.php 文件来检查限制
require 'count.php';


require 'setup.php';
$urlStatus = 200;
$code = rand(10000, 99999);




$iptxt = fopen("ip.dat", "a+");
$ifok = $i = 1;
while (!feof($iptxt)) {
    $now = fgets($iptxt);
    if (!empty($now)) {
        $now_arr = explode('#', $now);
        if ($now_arr[3] == $ip && $now_arr[7] == $_G['gp_show'] . "\r\n" && $now_arr[5] == $_POST['email']) {
            $ifok = 0;
            if ($now_arr['1'] + 180 > time() || $now_arr[7] == "TA9NG") {
                echo json_encode(array('status' => $urlStatus, 'msg' => '3分钟内只能使用一次'));
                exit;
            } else {
                sendMail($_POST['name'], $_POST['email'], $code);
                $all = file_get_contents('ip.dat');
                $count = $now_arr[2];
                $count++;
                $new = date('Y-m-d H:i:s', time()) . "#" . time() . "#" . $count . "#" . $ip . "#" . $ip_location['location'] . "#" . $_POST['email'] . "#" . $code . "#" . $_G['gp_show'] . "\r\n";
                $update_str = str_replace($now, $new, $all);
                file_put_contents('ip.dat', $update_str);
                exit;
            }
        }
    }
    $i++;
}
if ($ifok) {
    sendMail($_POST['name'], $_POST['email'], $code);
    $now = date('Y-m-d H:i:s', time()) . "#" . time() . "#" . '1' . "#" . $ip . "#" . $ip_location['location'] . "#" . $_POST['email'] . "#" . $code . "#" . $_G['gp_show'];
    fwrite($iptxt, $now . "\r\n");
}
fclose($iptxt);
?>
