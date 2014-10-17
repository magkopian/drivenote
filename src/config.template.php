<?php
define('APP_NAME', 'Drivenote'); // The application name
define('DOMAIN', '');

define('SERVICE_CLIENT_ID', ''); // Client ID of service account
define('SERVICE_ACCOUNT_NAME', ''); // Email Address of service account
define('SERVICE_KEY_FILENAME', '../keys/key.p12'); // Path to service account key file
define('SERVICE_API_SCOPE', 'https://www.googleapis.com/auth/drive'); // We want to access the google drive api
define('DIRECTORY_ID', ''); // ID of the directory to get shared

define('WEBAPP_CLIENT_ID', ''); // Client ID of web application
define('WEBAPP_ACCOUNT_NAME', ''); // Email Address of web application
define('WEBAPP_SECRET', ''); // Secret of web application
define('WEBAPP_REDIRECT_URI', 'http://<domain>/oauth2callback.php'); // Callback URL of web application
define('WEBAPP_API_SCOPE', 'email'); // We just want the user's email

define('DB_DRIVER', 'mysql');
define('DB_CHARSET', 'utf8');
define('DB_HOST', '');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASSWD', '');

define('SMTP_USER', '');
define('SMTP_PASSWD', '');
define('SMTP_HOST', '');
define('SMTP_CHARSET', 'UTF-8');
define('SMTP_PORT', '');
define('SMTP_AUTH', true); // Enable SMTP authentication
define('SMTP_ENCRYPTION', 'tls'); // 'ssl' also accepted