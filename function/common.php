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