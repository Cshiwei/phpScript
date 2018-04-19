<?php
/**
 * Created by PhpStorm.
 * User: csw
 * Date: 2018/4/18
 * Time: 18:25
 * 配置类名称跟类目录的对应关系
 */
$_config['autoloader']['lib'] = array(
        'orgDB' => 'db_pdo.php',
        'PHPMailer' => 'phpmailer/src/PHPMailer.php',
);

//需要在预加载的函数文件
$_config['autoloader']['function'] = array(
        'common'
);
