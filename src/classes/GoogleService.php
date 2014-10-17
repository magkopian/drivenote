<?php

abstract class GoogleService {
	protected $client = null;
	protected $accessToken = null;
	protected $sessionName = 'ACCESS_TOKEN';
	
	abstract public function __construct ( Google_Client $client );
	
}