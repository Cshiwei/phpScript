<?php
/**
 * Created by PhpStorm.
 * User: csw
 * Date: 2018/4/19
 * Time: 14:46
 * 加载器类，按需加载文件
 */

class loader {

    private static $instance;

    //已经加载的模块，防止重复加载
    private $isLoaded=array(
        'function' =>'',
        'lib'      =>'',
        'model'    => '',
        'controller' => '',
    );

    private function __construct()
    {}


    public static function getInstance()
    {
        if(!self::$instance)
            self::$instance=new loader();

        return self::$instance;
    }

    //csw 按需加载函数文件
    public function helper($fileName)
    {
        return $this->_load('function',$fileName);
    }

    //csw 按需加载模型
    public function model($modelName)
    {
        return $this->_load('model',$modelName);
    }

    //csw 按需加载类文件
    public function lib($libName)
    {
        return $this->_load('lib',$libName);
    }

    //csw 按需加载控制器
    public function controller($controller)
    {
        return $this->_load('controller',$controller);
    }

    private function _load($module,$name)
    {
        switch($module)
        {
            case 'function' :
                $modulePath = FUNCTIONPATH;
                break;
            case 'model' :
                $modulePath = MODELPATH;
                break;
            case 'lib' :
                $modulePath = LIBPATH;
                break;
            case 'controller' :
                $modulePath = APPPATH;
                break;
            default :
                $modulePath = false;
        }

        if($modulePath)
        {
            $file = $modulePath.DIRECTORY_SEPARATOR.$name.'.php';
            if(file_exists($file) && !isset($this->isLoaded[$module][$name]))
            {
                require_once $file;
                $this->isLoaded[$module][$name] = 1;
                return true;
            }
        }
        return false;
    }
}