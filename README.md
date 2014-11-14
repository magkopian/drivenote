## Description:
Drivenote is a permission management system for Google Drive shared folders. 
Its main purpose is to help students share their class notes securely by granting access only 
to verified students.

## How to Install:
To install Drivenote first download a stable release from the 
[releases page](https://github.com/magkopian/drivenote/releases) and extract the 
contents of the zip/tar.gz file to the directory you wish to install 
it e.g. `/var/www/drivenote/`.

For the next step you need to have installed `composer` in order to install the 
dependencies. If composer is not installed in your system you can download it 
and find installation instructions [here](https://getcomposer.org/doc/00-intro.md#globally).

After installing `composer` you need to `cd` to the directory where you have 
extracted the files e.g `cd /var/www/drivenote/`, and run `composer install` to install the dependencies.

Lastly, you need to rename `src/config.template.php` to `src/config.php` and
edit it in order to reflect the setup of your own application, by filling your Google Web Application 
and Web Service account credentials, the credentials of your database, the format and the example of 
the academic email etc.

## Configuration:
After installing the application you need to configure it by renaming the file
`src/config.template.php` to `src/config.php` and editing it.

### Google API Setup
First go to the [Google Developers Console](https://console.developers.google.com) and set up a new project. 

After that, you need to enable the `Drive API` for the
project you just created. 

Lastly, you have to generate two types of OAuth `Client ID`s for your application,
one `Service Account` type and one `Client ID for web application` type. 
Fill the `src/config.php` file with the credentials you just generated and download
the key file of the service account and place it inside the `keys/` directory.

### Adding the files
After generating the credentials for the Google API you will have to give the application access to the files
you want to manage their permissions.

At first, you will have to make sure that all the files you want the application to manage are inside a 
specific folder in your Google Drive, the structure inside the folder doesn't matter. You will have to share 
this folder with the application with write access, but you doesn't need to make the application owner of it. 

The email of the application that you have to share the folder with, is the same email that was generated
when you created the `Google Service Account` on the previous step.

After you give the application write access to the folder, you will have to find its ID and fill the
appropriate field inside your `config.php` file. For this purpose create a PHP file with the following code 
and put it inside the `public_html` directory:

```PHP
<?php require_once '../src/init.php';
$drive->listFiles();
```

When you run the script from your browser, it will list all the files and folders that the application manages.
Now you just have to find the folder that you shared with the application in this list so you can find its ID. 
The record of the folder will be most likely near the top of the list. When you are done, delete the PHP file 
you created to find the ID of the folder.

### Database Setup
The application needs a simple MySQL database in order to work. You can create an
empty database and import the `sql/drivenote.sql` file in order to generate the right database schema. 
Also, create a user with access to the same database.

Fill the `src/config.php` file with the credentials of the database you just created.

### SMTP Configuration
The application needs access to an SMTP server in order to be able to send account
activation emails. Fill the `src/config.php` file with the credentials of your SMTP 
server. If don't have an SMTP server installed you can use a relay server like [Mandrill](https://mandrill.com/).

### Academic Email Verification
In order to been able to accept students only from a specific school the application
uses a regular expression to validate the users academic email address. You need to
provide a regular expression that matches the academic email address format you want.

You also need to provide an academic email address example to indicate to your users
which type of email addresses are accepted as valid.

## Dependencies:
PHP 5.3  
PHPMailer 5.\*  
Google APIs Client Library for PHP 1.0.\*  