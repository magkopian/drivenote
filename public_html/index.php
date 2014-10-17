<?php require_once '../includes/init.php';

$drive = new GoogleDrive($googleClient);

// Grant read access to root directory
//$drive->grantReadAccess('<user_gmail_address>');

// Revoke read access to root directory
//$drive->revokeReadAccess('<user_gmail_address>');

// List all files
$drive->listFiles();