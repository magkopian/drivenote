<?php

class Database {
	protected static $instance = null;
	protected $pdo = null;

	protected function __construct () {

		try {
			$this->pdo = new PDO(
				DB_DRIVER . ':host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET, 
				DB_USER, 
				DB_PASSWD
			);
			
			$this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
			$this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		catch ( PDOException $e ) {
			$error_logger = new ErrorLogger($e);
			$error_logger->logError();
			throw new DatabaseException('Unable to connect to database');
		}
	
	}

	public static function getInstance () {

		if ( static::$instance === null ) {
			static::$instance = new static();
		}
		
		return static::$instance;
	
	}

	public function __call ( $method, $args ) {

		if ( is_callable(array($this->pdo, $method)) ) {
			return call_user_func_array(array($this->pdo, $method), $args);
		}
		else {
			throw new BadMethodCallException('Undefined Database::' . $method . ' method');
		}
	
	}

	public function __clone () {

		return false;
	
	}

	public function __wakeup () {

		return false;
	
	}

}