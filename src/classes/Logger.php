<?php
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

class Logger {
	const CRITICAL 	= '0';
	const ERROR 	= '1';
	const WARNING 	= '2';
	const NOTICE 	= '3';
	const INFO 		= '4';
	const DEBUG 	= '5';

	protected $filename = '/../logs/php-error.log';
	protected $level = self::WARNING;
	protected $file = null;

	public function __construct ( $filename = null, $level = null ) {
		
		if ( $filename !== null ) {
			$this->filename = $filename;
		}
		else {
			$this->filename = $_SERVER['DOCUMENT_ROOT'] . $this->filename;
		}
		
		
		if ( $level !== null ) {
			$this->level = $level;
		}
		
		$this->open();
		
	}
	
	public function debug ( $string ) {
	
		return $this->level < self::DEBUG ?: $this->log($string, self::DEBUG);
	
	}
	
	public function info ( $string ) {
		
		return $this->level < self::INFO ?: $this->log($string, self::INFO);
	  
	}
	
	public function warning ( $string ) {
		
		return $this->level < self::WARNING ?: $this->log($string, self::WARNING);
	  
	}
	
	public function error ( $string ) {
		
		return $this->level < self::ERROR ?: $this->log($string, self::ERROR);
	  
	}
	
	public function critical ( $string ) {
	
		return $this->level < self::CRITICAL ?: $this->log($string, self::CRITICAL);
	
	}
	
	public function clear () {
	
		$this->close();
		$this->open('w');
		$this->close();
		$this->open();
		
	}
	
	protected function strlevel ( $level ) {
		
		switch ( $level ) {
			case self::CRITICAL:
				return 'CRITICAL';
			case self::ERROR:
				return 'ERROR';
			case self::WARNING:
				return 'WARNING';
			case self::NOTICE:
				return 'NOTICE';
			case self::INFO:
				return 'INFO';
			case self::DEBUG:
				return 'DEBUG';
		}
		
	}
	
	protected function log ( $string, $level ) {
		
		$this->write(
			'[' . date('l jS F Y : h:i:sa') . '] '. 
			'[' . $this->strlevel($level)  . '] ' . 
			$string . PHP_EOL
		);

	}
  
	protected function write ( $string ) {
		
		if ( fwrite($this->file, $string) === false ) {
			throw new Exception('Unable to write "' . $this->filename . '" file.');
		}

	}
	
	protected function open ( $m = 'a' ) {

		if ( ($this->file = fopen($this->filename, $m)) === false ) {
			throw new Exception('Unable to open or create "' . $this->filename . '" file for write.');
		}
		
	}
	
	protected function close() {
		
		return fclose($this->file);
	  
	}
	
}
