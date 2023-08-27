<h1 align="center">🌿Typecho-MailCode🌿</h1>

开源的Typecho评论邮箱验证解决方案

由于typecho各种各样的主题太多，很多主题都对typecho高度自定义了，所有这个实现方式是在默认主题上测试的，基于不同的主题可能需要微调


### 修改了：

1.限制对后端get请求和后端对空值传入的处理

2.增加了调用限制，防止有人恶意请求邮件，可以在文件里修改单用户每日发信上限和总上限（同一IP或同一邮箱都视为同一用户）

3.增加了前端请求代码


### 具体实现教程

修改comment.php

第一步，加入监听

```php
    <!-- 引入 jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- 邮箱验证码的前端 Ajax 请求代码 -->
    <script>
    $(document).ready(function() {
        $('#mail').on('blur', function() {
            var name = $('#author').val(); // 获取昵称的值
            var email = $('#mail').val(); // 获取邮箱地址
            var captcha = $('#email_captcha').val(); // 获取验证码输入框的值
            // 判断邮箱输入框是否处于只读状态
            if ($('#mail').prop('readonly')) {
                return; // 如果邮箱输入框是只读状态，不执行后续代码
            }
            // 判断邮箱是否为空
            if (email.trim() === '') {
                $('#msg-e').html('<span style="color: red;">请输入邮箱</span>');
                return; // 如果邮箱为空，不执行后续代码
            }
            // 判断名字是否为空
            if (name.trim() === '') {
                $('#msg-e').html('<span style="color: red;">请输入昵称</span>');
                return; // 如果名字为空，不执行后续代码
            }
            // 执行邮件发送请求
            $.ajax({
                type: 'POST',
                url: '/Email/sendemail.php',
                data: {
                    name: name,
                    email: email
                },
                dataType: 'json',
                success: function(response) {
                    if (response.msg === 'Email sent successfully') {
                        $('#msg-e').html('<span style="color: green;">邮件发送成功</span>');
                        $('#mail').prop('readonly', true);
    
                        // 等待邮件发送成功后，执行验证码验证
                        verifyCaptcha(name, email);
                    } else {
                        $('#msg-e').html('<span style="color: red;">邮件发送失败：' + response.msg + '</span>');
                    }
                },
                error: function() {
                    $('#msg-e').html('<span style="color: red;">发生错误，请重试</span>');
                }
            }); 
        // 验证验证码
        function verifyCaptcha(name, email) {
            $('#email_captcha').on('blur', function() {
                var captcha = $(this).val();
                // 执行验证码验证请求
                $.ajax({
                    type: 'POST',
                    url: '/Email/checking.php',
                    data: {
                        name: name,
                        email: email,
                        code: captcha
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.msg === 'Verification successful') {
                            $('.captcha-msg').html('<span style="color: green;">验证码正确</span>');
                            $('.submit').prop('disabled', false);
                            $('#email_captcha').prop('readonly', true); // 设置验证码输入框为只读状态
                        } else {
                            $('.captcha-msg').html('<span style="color: red;">验证码错误</span>');
                            $('.submit').prop('disabled', true);
                        }
                    },
                    error: function() {
                        $('.captcha-msg').html('<span style="color: red;">发生错误，请重试</span>');
                        $('.submit').prop('disabled', true);
                    }
                });
            });
        }
        });
    });
    </script>
```

第二步   修改   邮箱和验证码模块

```php
    <p>
        <label for="mail"<?php if ($this->options->commentsRequireMail): ?> class="required"<?php endif; ?>><?php _e('Email'); ?></label>
        <input type="email" name="mail" id="mail" class="text"
               value="<?php $this->remember('mail'); ?>"<?php if ($this->options->commentsRequireMail): ?> required<?php endif; ?> />
        <span id="msg-e"></span>
    </p>
    <!-- 添加验证码输入框 -->
    <p>
        <label for="email_captcha" class="required"><?php _e('验证码'); ?></label>
        <input type="text" name="email_captcha" id="email_captcha" class="text"
               placeholder="<?php _e('请输入验证码'); ?>" required />
        <span id="captcha-msg" class="captcha-msg"></span> <!-- 用于显示验证码状态的 span 元素 -->
    </p>
```

第三步

你需要设置自己的smtp发件信息和每天发信的最大限制以及修改一下owo.php这个储存邮件内容的文件

smtp发件信息在setup.php里改

限制在max.php里修改
```php
$maxDailyScriptSendLimit = 100; // 一天内脚本的最大发信次数
$maxDailyUserSendLimit = 10; // 一天内单个用户的最大发信次数
```

### 警告：

1.sendmail.php和checking.php没对post的域限制，记得改一下防止被恶意调用

2.生产环境请谨慎使用并对接口做好防护，包括但不限于加强对获取的IP校验，对post信息处理以防止sql注入等等

### 前端提交评论时没对验证码框进行校验，因为不同主题不一样，记得自己根据具体的主题修改一下防止被绕过

### 防范恶意调用：

鼓励你对该后端的api接口进行校验，以确保后端接口不被恶意调用，以下给出一个示例方法：

前台生成一个签名，当需要访问接口的时候，把时间戳，随机数，签名通过URL或者post传递到后台。后台拿到时间戳，随机数后，通过一样的算法规则计算出签名，然后和传递过来的签名进行对比，一样的话，返回数据

可以在comments.php加入前端算法，然后后端sendmail.php请求时对前端传来的签名和后端计算出来的签名进行校验，如果校验通过则允许此次邮件发送调用

![示例图-来自CSDN](https://github.com/moxiaowk/Typecho-MailCode/assets/62387130/b5553269-b6a5-4c7f-9283-fc50c8dcfc0d)

为防止前端包含算法的部分被破解，你可以用SG11扩展对修改后的comments.php文件进行加密，并且可以限制域名使用（你可以寻找互联网在线加密网站或者某宝商家等等方式加密，记得保留源文件以便之后可能修改）


以下给出一段前端修改示例，这里需要CryptoJS 库，记得本地化引用

```php
// 引用CryptoJS 库
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.min.js"></script>
```

```php
// 计算字符串的MD5哈希
function calculateMD5(str) {
    return CryptoJS.MD5(str).toString();
}

// 计算签名
function calculateSignature(xxx, xxx) {
// 设计你自己的签名算法
    return finalSignature;
}

// 计算时间戳的哈希
function calculateTimestampHash() {
    const currentTimestamp = Math.floor(Date.now() / 1000);
    const md5Timestamp = calculateMD5(currentTimestamp.toString()).slice(1); // 删除前一位
    return md5Timestamp;
}

// 预先计算签名和时间戳的哈希
const signature = calculateSignature(xxx, xxx);
const timestampHash = calculateTimestampHash();

// 执行邮件发送请求
$.ajax({
    type: 'POST',
    url: '/sendemail.php',
    data: {
        name: name,
        email: email,
        signature: signature,
        timestamp: timestampHash
    },
    dataType: 'json',
    success: function(response) {
        if (response.msg === 'Email sent successfully') {
            $('#msg-e').html('<span style="color: green;">邮件发送成功</span>');
            $('#mail').prop('readonly', true);

            // 等待邮件发送成功后，执行验证码验证
            verifyCaptcha(name, email);
        } else {
            $('#msg-e').html('<span style="color: red;">邮件发送失败：' + response.msg + '</span>');
        }
    },
    error: function() {
        $('#msg-e').html('<span style="color: red;">发生错误，请重试</span>');
    }
});
```


### 致谢 Thanks

特别感谢Tonm提供的初始版本，正是基于他的版本才有这个粗略的修改和解决方案

- [Tonm](https://owo-bo.cn "基础代码原作者")
