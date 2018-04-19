<?php
class config {

    private static $instance;

    private $config = array();

    private function __construct()
    {
    }

    public static function getInstance()
    {
        if(self::$instance)
            return self::$instance;

        self::$instance = new config();
        return self::$instance;
    }

    public function item($configName)
    {
        if(isset($this->config[$configName]))
            return $this->config[$configName];

        return false;
    }

    public function load($config)
    {
        $configFile = CONFIGPATH."/{$config}.php";
        $envConfigFile  = ENVCONFIGPATH."/{$config}.php";

        if(file_exists($configFile))
            require_once $configFile;

        if(file_exists($envConfigFile))
            require_once $envConfigFile;

        $this->config = $_config ? array_merge($this->config,$_config) : $this->config;
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function __get($name)
    {
        $config = $this->item($name);
        return $config;
    }

}