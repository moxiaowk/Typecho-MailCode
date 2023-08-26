<?php
header("Content-type:text/html;charset=utf-8");
/*HTML邮件*/
$html = "<div style='width: 550px;height: auto;border-radius: 5px;margin:0 auto;box-shadow: 0px 0px 20px #888888;position: relative;padding-bottom: 5px;'>
    <div style='width:550px;height: 10px;background-size: cover;background-repeat: no-repeat;border-radius: 5px 5px 0px 0px;'></div>

    <div style='line-height:180%;width:520px;margin:0px auto;color:#555555;font-family:Century Gothic,Trebuchet MS,Hiragino Sans GB,微软雅黑,Microsoft Yahei,Tahoma,Helvetica,Arial,SimSun,sans-serif;font-size:12px;margin-bottom: 0px;'>  
        <h2 style='font-size:14px;font-weight:normal;padding:8px 14px;'><span style='font-size:15px;'>Dear：$Name ，</span>
            <p style='line-height:36px;margin:10px 0px;'>　　您好！<br>　　为确保是您本人操作，通过该邮件地址获取验证码验证身份。请在邮件验证码输入框输入下方验证码：</p>
        </h2>  
        <div style='padding:0 12px 0 12px;margin-top:-30px'>
            
            <p class='comment' style='background-color: #f5f5f5;border: 0px solid #DDD;border-radius: 5px;padding: 10px 15px;margin:18px 0;font-weight: bold;color: #257db9;letter-spacing: 2px;'>$Code<span style='color:red;font-weight: bold;'>(此验证码输入一次后失效)</span></p>  
            
        </div>  
    </div>
    
    <div style='color:#8c8c8c;;font-family: Century Gothic,Trebuchet MS,Hiragino Sans GB,微软雅黑,Microsoft Yahei,Tahoma,Helvetica,Arial,SimSun,sans-serif;font-size: 10px;width: 100%;text-align: center;word-wrap:break-word;margin-top: -18px;'>
        <p style='padding:20px 20px 10px;'>萤火虫消失之后，那光的轨迹仍久久地印在我的脑际。那微弱浅淡的光点，仿佛迷失方向的魂灵，在漆黑厚重的夜幕中彷徨。——《挪威的森林》村上春树</p>
    </div>
    <div style='color:#8c8c8c;;font-family: Century Gothic,Trebuchet MS,Hiragino Sans GB,微软雅黑,Microsoft Yahei,Tahoma,Helvetica,Arial,SimSun,sans-serif;font-size: 10px;width: 100%;text-align: center;margin-top: 0px;'>
        <p>".date('Y-m-d H:i:s')."</p>
        <p>本邮件为系统自动发送，请勿直接回复~</p>
    </div>
    <div style='color:#8c8c8c;;font-family: Century Gothic,Trebuchet MS,Hiragino Sans GB,微软雅黑,Microsoft Yahei,Tahoma,Helvetica,Arial,SimSun,sans-serif;font-size: 10px;width: 100%;text-align: center;padding-bottom: 1px;'>
        <p>Copyright © 2023 <a style='text-decoration:none;color: #ff7272;' href='https://i.sau.cc/'>柠宇</a></p>
    </div>
    
</div>";
// echo $html;

php?>


