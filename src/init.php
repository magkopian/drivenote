<?php
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

require_once '../vendor/autoload.php';
require_once '../src/config.php';

session_start();

$db = Database::getInstance();
$user = new User($db);

$googleClient = new Google_Client();
$googleClient->setApplicationName(APP_NAME);
$auth = new GoogleAuth($googleClient, $user, $db);

$googleClientService = new Google_Client();
$googleClientService->setApplicationName(APP_NAME);
$drive = new GoogleDrive($googleClientService);