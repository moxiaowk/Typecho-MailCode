<?php


function checkMaxLimit($ip, $email) {

// 获取当前日期
$todayDate = date("Y-m-d");

// 定义限制值
$maxDailyScriptSendLimit = 100;
$maxDailyUserSendLimit = 10;


// 读取 auth.log 文件内容
$authLogContent = file_get_contents("auth.log");
$authLogContents = file("auth.log");


// 判断是否达到每日脚本发送限制
$scriptSendCount = substr_count($authLogContent, $todayDate);

if ($scriptSendCount >= $maxDailyScriptSendLimit) {
    echo json_encode(array('status' => 'error', 'msg' => 'Too Many Emails'));
    exit;
}

// 获取当前IP地址和邮箱

// $currentIP = $ip; // 获取当前 IP
$email = $_POST['email']; // 获取当前邮箱


$limitExceeded = false;

foreach ($authLogContents as $line) {
    $lineParts = explode('#', $line);

    // 提取日期部分
    $datePart = trim($lineParts[0]);

    if ($datePart === date("Y-m-d")) {
        // 提取 IP 数据部分
        $ipData = explode('#', trim($lineParts[1]));
        if ($ipData[1] === $ip) {
            $ipCount = intval(end($ipData)); // 提取发送次数
            if ($ipCount >= $maxDailyUserSendLimit) {
                $limitExceeded = true;
                break; // 如果达到限制，跳出循环
            }
        }
    }
}
// echo json_encode(array('status' => 'success', 'msg' => 'No Limit Exceeded'));

foreach ($authLogContents as $line) {
    $lineParts = explode('#', $line);

    // 提取日期部分
    $datePart = trim($lineParts[0]);

    if ($datePart === date("Y-m-d")) {
        // 提取邮箱数据部分
        $emailData = explode('#', trim($lineParts[2]));
        if ($emailData[1] === $email) {
            $emailCount = intval(end($emailData)); // 提取发送次数
            if ($emailCount >= $maxDailyUserSendLimit) {
                $limitExceeded = true;
                break; // 如果达到限制，跳出循环
            }
        }
    }
}


// 根据检查结果输出相应信息
if ($limitExceeded) {
    echo json_encode(array('status' => 'error', 'msg' => 'Too Many Emails'));
    $allow = false; // 返回false表示不允许发送
} else {
    
    $allow = true; // 返回true表示允许发送
}



return ($allow);

}
$allow = checkMaxLimit($ip, $email);


?>