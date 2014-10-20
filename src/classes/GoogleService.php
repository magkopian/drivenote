<?php
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

abstract class GoogleService {
	protected $client = null;
	protected $accessToken = null;
	protected $sessionName = 'ACCESS_TOKEN';	
}