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
                url: 'https://cx.mibor.cn/Email/sendemail.php',
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
                    url: 'https://cx.mibor.cn/Email/checking.php',
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

提示：sendmail.php没对post的域限制，记得改一下防止被恶意调用
前端提交评论时没对验证码框进行校验，因为不同主题不一样，记得自己根据具体的主题修改一下防止被绕过

