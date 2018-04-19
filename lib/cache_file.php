<?php
/**
 * File Cache class file.
 *
 * @package vela.cache
 * @copyright Copyright 2011 Lightinthebox.com R&D Team
 * @author yilee@lightinthebox.com
 * @date 2011/02/14 16:53:50
 * @version Vela 1.4 $
 */
if (!defined('ENVIRONMENT')) die('Illegal Access');

/**
 * @category   classes.cache
 * @package    vela.cache
 * @copyright Copyright 2011 Lightinthebox.com R&D Team
 * @author yilee@lightinthebox.com
 */
class cacheFile {
	const DIRECTORY_SEPARATOR = "/";
	/**
	 * @var string the directory to store cache files. Defaults to null, meaning
	 * using 'protected/runtime/cache' as the directory.
	 */
	public $cachePath;
	/**
	 * @var string cache file suffix. Defaults to '.sql'.
	 */
	public $cacheFileSuffix='.sql';
	/**
	 * @var integer the level of sub-directories to store cache files. Defaults to 0,
	 * meaning no sub-directories. If the system has huge number of cache files (e.g. 10K+),
	 * you may want to set this value to be 1 or 2 so that the file system is not over burdened.
	 * The value of this property should not exceed 16 (less than 3 is recommended).
	 */
	public $directoryLevel=0;

	private $_gcProbability=100;
	private $_gced=false;

	/**
	 *
	 * 构造函数
	 * @param String $contentFormat
	 */
	public function __construct($cachePath = null) {
		//
		$this->cachePath = $cachePath;
		//
		$this->init();
	}

	public function init()
	{
		if($this->cachePath===null)
			$this->cachePath= DIR_CACHE;
		if(!is_dir($this->cachePath))
			mkdir($this->cachePath,0777,true);
	}

	/**
	 * @return integer the probability (parts per million) that garbage collection (GC) should be performed
	 * when storing a piece of data in the cache. Defaults to 100, meaning 0.01% chance.
	 */
	public function getGCProbability()
	{
		return $this->_gcProbability;
	}

	/**
	 * @param integer the probability (parts per million) that garbage collection (GC) should be performed
	 * when storing a piece of data in the cache. Defaults to 100, meaning 0.01% chance.
	 * This number should be between 0 and 1000000. A value 0 meaning no GC will be performed at all.
	 */
	public function setGCProbability($value)
	{
		$value=(int)$value;
		if($value<0)
			$value=0;
		if($value>1000000)
			$value=1000000;
		$this->_gcProbability=$value;
	}

	/**
	 * Deletes all values from cache.
	 * Be careful of performing this operation if the cache is shared by multiple applications.
	 */
	public function flush()
	{
		return $this->gc(false);
	}

	/**
	 * Retrieves a value from cache with a specified key.
	 * This is the implementation of the method declared in the parent class.
	 * @param string a unique key identifying the cached value
	 * @return string the value stored in cache, false if the value is not in the cache or expired.
	 */
	public function getValue($key)
	{

		$cacheFile=$this->getCacheFile($key);

		if(($time=@filemtime($cacheFile))>time())	// || $key = '0000000643'
			return unserialize(file_get_contents($cacheFile));
		else if($time>0)
			@unlink($cacheFile);
		return false;
	}

	/**
	 * Stores a value identified by a key in cache.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string the key identifying the value to be cached
	 * @param string the value to be cached
	 * @param boolean the compression Flag
	 * @param integer the number of seconds in which the cached value will expire. 0 means cache invalid.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	public function setValue($key,$value,$expire=43200,$compression_flag = 0)
	{
		if(!$this->_gced && mt_rand(0,1000000)<$this->_gcProbability)
		{
			$this->gc();
			$this->_gced=true;
		}

		if($expire<=0){
			return false;
		}

		$expire+=time();

		$cacheFile=$this->getCacheFile($key);

        $value = serialize($value);
		if($this->directoryLevel>0)
			@mkdir(dirname($cacheFile),0777,true);
		if(@file_put_contents($cacheFile,$value,LOCK_EX)==strlen($value))
		{
			@chmod($cacheFile,0777);
			return @touch($cacheFile,$expire);
		}
		else
			return false;
	}

	/**
	 * Stores a value identified by a key into cache if the cache does not contain this key.
	 * This is the implementation of the method declared in the parent class.
	 *
	 * @param string the key identifying the value to be cached
	 * @param string the value to be cached
	 * @param boolean the compression Flag
	 * @param integer the number of seconds in which the cached value will expire. 0 means cache invalid.
	 * @return boolean true if the value is successfully stored into cache, false otherwise
	 */
	public function addValue($key,$value,$expire,$compression_flag = 0)
	{
		$cacheFile=$this->getCacheFile($key);
		if(@filemtime($cacheFile)>time())
			return false;
		return $this->setValue($key,$value,$expire);
	}

	/**
	 * Deletes a value with the specified key from cache
	 * This is the implementation of the method declared in the parent class.
	 * @param string the key of the value to be deleted
	 * @return boolean if no error happens during deletion
	 */
	protected function deleteValue($key)
	{
		$cacheFile=$this->getCacheFile($key);
		return @unlink($cacheFile);
	}

	/**
	 * Returns the cache file path given the cache key.
	 * @param string cache key
	 * @return string the cache file path
	 */
	protected function getCacheFile($key)
	{
		if($this->directoryLevel>0)
		{
			$base=$this->cachePath;
			for($i=0;$i<$this->directoryLevel;++$i)
			{
				if(($prefix=substr($key,$i+$i,2))!==false)
					$base.= self::DIRECTORY_SEPARATOR.$prefix;
			}
			return $base.self::DIRECTORY_SEPARATOR.$key.$this->cacheFileSuffix;
		}
		else
			return $this->cachePath.self::DIRECTORY_SEPARATOR.$key.$this->cacheFileSuffix;
	}

	/**
	 * Removes expired cache files.
	 * @param boolean whether to removed expired cache files only. If true, all cache files under {@link cachePath} will be removed.
	 * @param string the path to clean with. If null, it will be {@link cachePath}.
	 */
	protected function gc($expiredOnly=true,$path=null)
	{
		if($path===null)
			$path=$this->cachePath;
		if(($handle=opendir($path))===false)
			return;
		while($file=readdir($handle))
		{
			if($file[0]==='.')
				continue;
			$fullPath=$path.self::DIRECTORY_SEPARATOR.$file;
			if(is_dir($fullPath))
				$this->gc($expiredOnly,$fullPath);
			else if($expiredOnly && @filemtime($fullPath)<time() || !$expiredOnly)
				@unlink($fullPath);
		}
		closedir($handle);
	}
}
