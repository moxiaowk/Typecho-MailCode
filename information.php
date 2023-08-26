<?php
header('Access-Control-Allow-Origin:*');/*防跨域设置*/
header("Content-type:text/html;charset=utf-8");
/*获取信息 information.php*/

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /* 获取状态码 */
        function GetHttpStatusCode(){ 
        	$curl = curl_init();
        	curl_setopt($curl,CURLOPT_URL,'https://'.$_SERVER['SERVER_NAME'].$_SERVER["REQUEST_URI"]);//获取内容url
        	curl_setopt($curl,CURLOPT_HEADER,1);//获取http头信息
        	curl_setopt($curl,CURLOPT_NOBODY,1);//不返回html的body信息
        	curl_setopt($curl,CURLOPT_RETURNTRANSFER,1);//返回数据流，不直接输出
        	curl_setopt($curl,CURLOPT_TIMEOUT,30); //超时时长，单位秒
        	curl_exec($curl);
        	$rtn= curl_getinfo($curl,CURLINFO_HTTP_CODE);
        	curl_close($curl);
        	return  $rtn;
        }
        
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /* 获取IP地址 */
        function getIp()
        {
            if ($_SERVER["HTTP_CLIENT_IP"] && strcasecmp($_SERVER["HTTP_CLIENT_IP"], "unknown")) {
                $ip = $_SERVER["HTTP_CLIENT_IP"];
            } else {
                if ($_SERVER["HTTP_X_FORWARDED_FOR"] && strcasecmp($_SERVER["HTTP_X_FORWARDED_FOR"], "unknown")) {
                    $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
                } else {
                    if ($_SERVER["REMOTE_ADDR"] && strcasecmp($_SERVER["REMOTE_ADDR"], "unknown")) {
                        $ip = $_SERVER["REMOTE_ADDR"];
                    } else {
                        if (isset ($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'],
                                "unknown")
                        ) {
                            $ip = $_SERVER['REMOTE_ADDR'];
                        } else {
                            $ip = "unknown";
                        }
                    }
                }
            }
            return ($ip);
        }
        $ip = GetIP();
        
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /* 获取IP归属地 */
        $ch = curl_init();
        $url = 'http://sp0.baidu.com/8aQDcjqpAAV3otqbppnN2DJv/api.php?query='.$ip.'&co=&resource_id=6006';
        //用curl发送接收数据
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //请求为https
        curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $location = curl_exec($ch);
        curl_close($ch);
        //转码
        $location = mb_convert_encoding($location, 'utf-8','GB2312');
        //var_dump($location);
        //截取{}中的字符串
        $location = substr($location, strlen('[{')+strpos($location, '[{'),(strlen($location) - strpos($location, '}]'))*(-1));
        //将截取的字符串$location中的‘，’替换成‘&’   将字符串中的‘：‘替换成‘=’
        $location = str_replace('"',"",str_replace(":","=",str_replace(",","&",$location)));
        //php内置函数，将处理成类似于url参数的格式的字符串  转换成数组
        parse_str($location,$ip_location);
            

php?>