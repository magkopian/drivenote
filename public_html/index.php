<?php require_once '../src/init.php'; 
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

if ( $user->isSignedIn() === true && $user->isVerified() === true ) {
	
	try {
		$directoryURL = $drive->getFileURL(DIRECTORY_ID);
	}
	catch ( Google_Service_Exception $e ) {
		$directoryURL = '#';
		$logger = new ExceptionLogger();
		$logger->error($e);
		Notifier::push('error', 'Unable to establish connection with Google Service, please try again later. If the error persists contact the administrator.');
	}
	
}

require '../src/views/pages/home.php';
