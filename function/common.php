<?php
/**
 * Created by PhpStorm.
 * User: csw
 * Date: 2018/4/18
 * Time: 15:17
 * 通用函数文件
 */

/**csw
 *1. 去除字符串首尾两端的目录分隔符
 * 2. 将目录分割符替换为系统使用的目录分隔符
 */
if(!function_exists('dealSeparator'))
{
    function dealSeparator($dealStr)
    {
        $search = array('/','\\');
        $replace = DIRECTORY_SEPARATOR;
        $dealStr = str_replace($search,$replace,$dealStr);
        return trim($dealStr,$replace);
    }
}

/**csw
 * 1.通过将输出内容保存到静态变量里实现跟踪打印
 */
if(!function_exists('myEcho'))
{
    function myEcho($input,$isEnd=false)
    {
        static $output = array();
        if(!$isEnd)
            $output[] = $input;
        else
        {
            echo "<pre>";
            foreach ($output as $key=>$val)
            {
                $type = gettype($val);
                echo "<span style='color:red;'>{$type}:</span>";
                print_r($val);
                echo "<hr/>";
            }
        }
    }
}

/**csw
 * 获取二维数组里第一个元素组成一个新的一维数组
 */
if(!function_exists('array_column'))
{
    function array_column($input,$colum_key,$index_key=null)
    {
        $newArr = array();
        foreach ($input as $key=>$val)
        {
            if(!empty($index_key))
                $newArr[$val[$index_key]] = $val[$colum_key];
            else
                $newArr[] = $val[$colum_key];
        }
        return $newArr;
    }
}

/**csw
 * 生成日志函数
 */
if( ! function_exists('logMsg'))
{
    function logMsg($msg,$path='',$file='',$cover=false)
    {
        $log_path = LOGPATH.DIRECTORY_SEPARATOR;               //指定记录日志的文件路径
        $config = config::getInstance()->item('config');
        $file_ext =
            (isset($config['log_file_extension']) && $config['log_file_extension'] !== '')
                ? ltrim($config['log_file_extension'], '.')
                : 'php';
        if( ! $file)
        {
            $file = 'log-'.date('Y-m-d');
        }
        $path = dealSeparator($path);
        $filePath = $log_path.$path.DIRECTORY_SEPARATOR;

        if(!file_exists($filePath))
        {
            if(!mkdir($filePath,0777,true))
                return false;
        }

        $file = $filePath.$file.'.'.$file_ext;
        $message ='';
        if ( ! file_exists($file))
        {
            $newfile = TRUE;
            if ($file_ext === 'php')
            {
                $message .= "<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>\n\n";
            }
        }
        if( ! $cover)
        {
            if ( ! $fp = fopen($file, 'ab'))                    //新建或者打开文件(以附加内容的方式打开文件)
                return FALSE;
        }
        else
        {
            if ( ! $fp = fopen($file, 'w'))                    //新建或者打开文件(以覆盖方式写入文件)
                return FALSE;
        }
        $date = date('Y-m-d H:i:s',time());
        $message .= $date.' --> '.$msg."\n";
        flock($fp, LOCK_EX);
        for ($written = 0, $length = strlen($message); $written < $length; $written += $result)
        {
            if (($result = fwrite($fp, substr($message, $written))) === FALSE)
            {
                break;
            }
        }
        flock($fp, LOCK_UN);
        fclose($fp);
        if (isset($newfile) && $newfile === TRUE)
        {
            chmod($file, 0644);
        }
        return is_int($result);
    }
}
