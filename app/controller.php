<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/4/20
 * Time: 9:54
 */
abstract class controller {

    //加载器类
    public $_loader;

    //加载配置文件
    public $_configer;

    //命令行参数
    public $_argv;

    //脚本名称
    public $_scriptName;

    //数据库连接池
    private static $dbPull;

    public function __construct()
    {
        global $argv;
        $this->_loader = loader::getInstance();
        $this->_configer = config::getInstance();
        $this->_scriptName = $argv[1];
        $this->_argv = array_slice($argv,2);
    }

    //csw 获取数据库连接
    public function loadDb($connection)
    {
        if(isset(self::$dbPull[$connection]))
            return self::$dbPull[$connection];

        $this->loadConfig('db');
        $dbConfig = $this->configItem($connection);
        $connection = isset($dbConfig['connection']) ? $dbConfig['connection'] : '';
        $dsn = isset($dbConfig['dsn']) ? $dbConfig['dsn'] : '';
        $username = isset($dbConfig['username']) ? $dbConfig['username'] : '';
        $password = isset($dbConfig['password']) ? $dbConfig['password'] : '';

        if(empty($connection) || empty($dsn) || empty($username) || empty($password))
            return false;

        self::$dbPull[$connection] = new orgDB($dsn,$username,$password);
        return self::$dbPull[$connection];
    }

    //csw 加载配置项
    public function loadConfig($configs)
    {
        if(!is_array($configs))
            $this->_configer->load($configs);
        else
        {
            foreach ($configs as $key=>$val)
            {
                $this->loadConfig($val);
            }
        }
    }

    //csw 获取配置项
    public function configItem($configName)
    {
        $config = $this->_configer->item($configName);
        if(!$config)
            $this->loadConfig($configName);

        return $this->_configer->item($configName);
    }

    //csw 每个任务必须有run方法作为启动方法
    abstract public function run();
}