<?php require_once '../src/init.php';

$drive = new GoogleDrive($googleClient);

// Grant read access to root directory
//$drive->grantReadAccess('<user_gmail_address>', DIRECTORY_ID);
//$drive->redirect(DIRECTORY_ID);

// Revoke read access to root directory
//$drive->revokeReadAccess('<user_gmail_address>', DIRECTORY_ID);

// List all files
$drive->listFiles();