### Description:
Drivenote is a permission management system for Google Drive shared folders. 
Its main purpose is to help students share their class notes securely by granting access only 
to verified students.

### How to Install:
To install Drivenote first download a stable release from the [releases page](https://github.com/magkopian/drivenote/releases) and extract the 
contents of the zip/tar.gz file to the directory you wish to install 
it e.g. `/var/www/drivenote/`.

For the next step you need to have installed `composer` in order to install the 
dependencies. If composer is not installed in your system you can download it 
and find installation instuctions [here](https://getcomposer.org/doc/00-intro.md#globally).

After installing `composer` you need to `cd` to the directory where you have 
extracted the files e.g `cd /var/www/drivenote/`, and run `composer install` to install the dependencies.

Lastly, you need to rename `src/config.template.php` to `src/config.php` and
edit it in order to reflect the setup of your own application by filling your Google Web Application and Web Service account credentials, the credentials of your database, the format and the example of the academic email etc.

### Requirements:
PHP 5.3  
PHPMailer 5.\*  
Google APIs Client Library for PHP 1.0.\*  