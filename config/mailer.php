<?php
/**
 * Created by PhpStorm.
 * User: csw
 * Date: 2018/4/19
 * Time: 10:51
 * 邮件发送通用配置
 * 请在环境配置文件里修改特殊的配置信息
 */
$_config['mailer'] = array(
    'Charset'   => 'UTF-8', //编码字符集
    'host'      => 'smtp.exmail.qq.com', //您的企业邮局域名
    'SMTPAuth'  => true,     //启用SMTP验证功能
    'Username'  => 'hrsystem@lightinthebox.com',  //邮局用户名
    'Password' => 'Hrsys20122013', //邮局密码
    'Port'      => 25,      //端口
);
