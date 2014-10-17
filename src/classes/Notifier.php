<?php

class Notifier {
	private static $prefix = 'notify_';
	
	private static $notificationTypes = array (
		'info' 		=> 'Info',
		'success' 	=> 'Success',
		'warning' 	=> 'Warning',
		'error' 	=> 'Error',
		'tip' 		=> 'Tip',
		'secure' 	=> 'Secure',
		'message' 	=> 'Message',
		'download' 	=> 'Download',
		'purchase' 	=> 'Purchase',
		'print' 	=> 'Print'
	);

	public static function push ( $type = 'info', $msg ) {

		if ( !array_key_exists($type, self::$notificationTypes) ) {
			throw new InvalidArgumentException('Invalid notification type specified');
		}
		
		$_SESSION[self::$prefix . $type] = $msg;
	
	}

	public static function pop () {

		$notification = array();
		
		foreach ( self::$notificationTypes as $type => $title ) {
			
			if ( isset($_SESSION[self::$prefix . $type]) ) {
				$notification['type'] = $type;
				$notification['title'] = $title;
				$notification['message'] = $_SESSION[self::$prefix . $type];
				
				unset($_SESSION[self::$prefix . $type]);
			}
		
		}
		
		return $notification;
	
	}

}