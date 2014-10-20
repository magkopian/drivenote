<?php
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

class ExceptionLogger extends Logger {
	
	public function debug ( $e ) {
	
		return $this->level < static::DEBUG ?: $this->log($this->exceptionToString($e), static::DEBUG);
	
	}
	
	public function info ( $e ) {
	
		return $this->level < static::INFO ?: $this->log($this->exceptionToString($e), static::INFO);
		 
	}
	
	public function warning ( $e ) {
	
		return $this->level < static::WARNING ?: $this->log($this->exceptionToString($e), static::WARNING);
		 
	}
	
	public function error ( $e ) {

		return $this->level < static::ERROR ?: $this->log($this->exceptionToString($e), static::ERROR);
		 
	}
	
	public function critical ( $e ) {
	
		return $this->level < static::CRITICAL ?: $this->log($this->exceptionToString($e), static::CRITICAL);
	
	}
	
	private function exceptionToString ( Exception $e ) {
		
		$trace = $e->getTrace();
		
		$errorLog = $e->getMessage();
		$errorLog = preg_replace('!\s+!', ' ', $errorLog);
		$errorLog .= isset($trace[0]['file']) ? ' in ' . $trace[0]['file'] : '';
		$errorLog .= ' at call of ';
		$errorLog .= isset($trace[0]['class']) ? $trace[0]['class'] . '::' . $trace[0]['function'] . '()' : $trace[0]['function'] . '();';
		$errorLog .= isset($trace[0]['line']) ? ' on line ' . $trace[0]['line'] : '';

		return $errorLog;
		
	}
}