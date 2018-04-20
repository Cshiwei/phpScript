<?php  
if (!defined('ENVIRONMENT')) die('Illegal Access');

class orgDB{

	public $dbh = null;

	public function __construct($dsn,$username,$password) {
		$dbh = new PDO($dsn,$username,$password);
		if ($dbh){
			$this->dbh = $dbh;
		}
	}
	
	public function fetchone( $query, $args='' ){
		$result = $this->fetch($query, $args);
		return $result === false ? false : $result[0];
	}

	public function fetch( $query, $args='' ){
		if($this->dbh){
			$sth = $this->dbh->prepare($query);
			if (!empty($args)) {
				foreach ($args as $k => $arg) {
					if (is_array($arg))
						$sth->bindValue($k + 1, $arg[0], $arg[1]);
					else
						$sth->bindValue($k + 1, $arg);
				}
			}
			$sth->execute();
			return $sth->fetchAll(PDO::FETCH_NAMED);
		}
		return false;
	}


	public function execute( $query , $args=''){
		$success = false;
		if($this->dbh){
			$sth = $this->dbh->prepare($query);
			if (!empty($args)) {
				foreach ($args as $k => $arg) {
					if (is_array($arg))
						$sth->bindValue($k + 1, $arg[0], $arg[1]);
					else
						$sth->bindValue($k + 1, $arg);
				}
			}
			$success = $sth->execute();
			if (!$success)
				echo gbk2utf8(odbc_errormsg($this->dbh));
		}
		return $success;
	}
	
	public function beginTransaction(){
		$success = false;
		if($this->dbh){
			$success = $this->dbh->beginTransaction();
			if (!$success)
				echo gbk2utf8(odbc_errormsg($this->dbh));
		}
		return $success;
	}
	
	public function commit(){
		$success = false;
		if($this->dbh){
			$success = $this->dbh->commit();
			if (!$success)
				echo gbk2utf8(odbc_errormsg($this->dbh));
		}
		return $success;
	}
	
	public function rollback(){
		$success = false;
		if($this->dbh){
			$success = $this->dbh->rollback();
			if (!$success)
				echo gbk2utf8(odbc_errormsg($this->dbh));
		}
		return $success;
	}
}
