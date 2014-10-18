<?php
require_once '../vendor/autoload.php';
require_once '../src/config.php';

session_start();

$db = Database::getInstance();
$user = new User($db);

$googleClient = new Google_Client();
$googleClient->setApplicationName(APP_NAME);

$auth = new GoogleAuth($googleClient, $user, $db);