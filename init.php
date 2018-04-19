<?php
/**
 * Created by PhpStorm.
 * User: csw
 * Date: 2018/4/18
 * Time: 13:50
 * 初始化，加载项目运行必要的文件
 */
define('ENVIRONMENT', 'development');

//csw 根据环境不同控制报错级别
switch (ENVIRONMENT)
{
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;

    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        if (version_compare(PHP_VERSION, '5.3', '>='))
        {
            error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        }
        else
        {
            error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_USER_NOTICE);
        }
        break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo 'The application environment is not set correctly.';
        exit(1); // EXIT_ERROR
}

//csw定义系统常量  项目路径
define('BASEPATH',dirname(__FILE__));

//csw定义类路径
define('LIBPATH',BASEPATH.DIRECTORY_SEPARATOR.'lib');

//csw定义应用路径
define('APPPATH',BASEPATH.DIRECTORY_SEPARATOR.'app');

//csw定义通用配置文件路径
define('CONFIGPATH',BASEPATH.DIRECTORY_SEPARATOR.'config');

//csw定义环境配置文件路径
define('ENVCONFIGPATH',CONFIGPATH.DIRECTORY_SEPARATOR.ENVIRONMENT);

//csw定义函数库目录
define('FUNCTIONPATH',BASEPATH.DIRECTORY_SEPARATOR.'function');

//csw定义邮件发送类的路径
define('PHPMAILERPATH',LIBPATH.DIRECTORY_SEPARATOR.'phpmailer');

//csw定义模型文件的地址
define('MODELPATH',BASEPATH.DIRECTORY_SEPARATOR.'model');

//csw加载器类
require_once LIBPATH.DIRECTORY_SEPARATOR."loader.php";
$L = loader::getInstance();
//加载文件配置类
$L->lib('config');
$_C = config::getInstance();
$_C->load('autoloader');
//csw 加载函数库
$preFunction = $_C->item('autoloader')['function'];
if($preFunction){
    foreach ($preFunction as $key=>$val)
    {
        $functionFile = FUNCTIONPATH.DIRECTORY_SEPARATOR.$val.'.php';
        if(file_exists($functionFile))
            require_once $functionFile;
    }
}

//自动加载类
function autoLoader($className){
    global $_C;
    $loadArr = $_C->item('autoloader')['lib'];
    if(array_key_exists($className,$loadArr))
        $libFile = LIBPATH.DIRECTORY_SEPARATOR.dealSeparator($loadArr[$className]);
    else
        $libFile = LIBPATH.DIRECTORY_SEPARATOR."{$className}.php";

    if(file_exists($libFile))
        require_once $libFile;
}

//PHP邮件发送类自动加载函数
function PHPMailerAutoload($classname)
{
    //Can't use __DIR__ as it's only in PHP 5.3+
    $filename = PHPMAILERPATH.DIRECTORY_SEPARATOR.'class.'.strtolower($classname).'.php';
    if (is_readable($filename)) {
        require $filename;
    }
}

spl_autoload_register('autoLoader');
spl_autoload_register('PHPMailerAutoload');
?>
