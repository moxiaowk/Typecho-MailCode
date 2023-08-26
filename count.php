<?php



// // 读取 auth.log 文件并获取统计数据
// $authLogContents = file("auth.log");

// $maxDailyScriptSendLimit = 120; // 一天内脚本的最大发信次数
// $maxDailyUserSendLimit = 20; // 一天内单个用户的最大发信次数

// $totalScriptSendCount = 0; // 当天脚本已经发信的总次数

// foreach ($authLogContents as $line) {
//     $lineParts = explode('#', $line);
//     if (trim($lineParts[0]) === date("Y-m-d")) {
//         $totalScriptSendCount = (int) trim($lineParts[1]);
//         break;
//     }
// }

// if ($totalScriptSendCount >= $maxDailyScriptSendLimit) {
//     echo json_encode(array('status' => 'error', 'msg' => 'Too Many Emails'));
//     exit;
// }

// // 获取当前 IP 和邮箱的统计数据
// $currentIP = $_SERVER['REMOTE_ADDR']; // 获取当前 IP
// $currentEmail = $_POST['email']; // 获取当前邮箱

// $ipLimitExceeded = false;
// $emailLimitExceeded = false;

// // 读取 auth.log 文件并获取 IP 和邮箱的统计数据
// foreach ($authLogContents as $line) {
//     $lineParts = explode('#', $line);
//     if (trim($lineParts[0]) === date("Y-m-d")) {
//         $ipData = explode('#', trim($lineParts[2]));
//         foreach ($ipData as $ipEntry) {
//             list($ip, $ipCount) = explode(':', $ipEntry);
//             if ($ip === $currentIP && $ipCount >= $maxDailyUserSendLimit) {
//                 $ipLimitExceeded = true;
//                 break;
//             }
//         }

//         $emailData = explode('#', trim($lineParts[3]));
//         foreach ($emailData as $emailEntry) {
//             list($email, $emailCount) = explode(':', $emailEntry);
//             if ($email === $currentEmail && $emailCount >= $maxDailyUserSendLimit) {
//                 $emailLimitExceeded = true;
//                 break;
//             }
//         }
//         break;
//     }
// }

// // 如果单个 IP 或邮箱达到限制，也返回失败，但不停止脚本
// if ($ipLimitExceeded || $emailLimitExceeded) {
//     echo json_encode(array('status' => 'error', 'msg' => 'IP or Email Limit Exceeded'));
//     exit;
// }




// 清空 access.log 文件
$accessLog = fopen("access.log", "w");
fclose($accessLog);


// 获取当前日期
$todayDate = date("Y-m-d");

// 读取 ip.dat 文件并获取筛选后的数据
$ipData = file("ip.dat", FILE_IGNORE_NEW_LINES);

// 将数据存储到 access.log 文件中
$accessLog = fopen("access.log", "a+");
foreach ($ipData as $line) {
    if (strpos($line, $todayDate) !== false) {
        fwrite($accessLog, $line . PHP_EOL);
    }
}
fclose($accessLog);



// 对 access.log 数据进行统计
$logData = file("access.log", FILE_IGNORE_NEW_LINES);

// 定义统计变量
$ipCalls = [];
$emailCalls = [];
$dateCalls = [];

foreach ($logData as $line) {
    $lineParts = explode('#', $line);

    // 提取日期部分
    $datePart = explode(' ', $lineParts[0])[0];

    // 统计总调用次数
    if (!isset($dateCalls[$datePart])) {
        $dateCalls[$datePart] = 0;
    }
    $dateCalls[$datePart]++;

    // 统计每个IP的调用次数
    $ip = $lineParts[3];
    if (!isset($ipCalls[$datePart][$ip])) {
        $ipCalls[$datePart][$ip] = 0;
    }
    $ipCalls[$datePart][$ip]++;

    // 提取邮箱部分
    $emailFullPart = explode('@', $lineParts[count($lineParts) - 3])[0]; // 获取@前的内容
    $emailSuffix = explode('@', $lineParts[count($lineParts) - 3])[1]; // 获取@与倒数第三个#之间的内容
    $emailPart = $emailFullPart . '@' . $emailSuffix; // 拼接邮箱部分
    
    // 判断邮箱是否有效，包含@符号
    if (strpos($emailPart, '@') !== false) {
        // 统计每个邮箱的调用次数
        if (!isset($emailCalls[$datePart][$emailPart])) {
            $emailCalls[$datePart][$emailPart] = 0;
        }
        $emailCalls[$datePart][$emailPart]++;
    }
}

$authLog = fopen("auth.log", "w"); // 清空并重新写入文件

// 写入统计结果到 auth.log 文件
foreach ($dateCalls as $date => $calls) {
    fwrite($authLog, $date . " #" . $calls . PHP_EOL);
}
foreach ($ipCalls[$date] as $ip => $calls) {
    fwrite($authLog, $date . " #" . $ip . "#" . $calls . PHP_EOL);
}
// 写入每个邮箱的统计数据
    $emailPartCalls = (array) $emailCalls[$date];
    // 将邮箱部分统计数据强制转换为数组
    foreach ($emailPartCalls as $email => $calls) {
        fwrite($authLog, $date . " #" . $email . "#" . $calls . PHP_EOL);
}

fclose($authLog);

?>
