<?php
require_once '../vendor/autoload.php';
require_once '../src/config.php';

$faker = Faker\Factory::create();

for ($i=0; $i < 100; $i++) {

	try {
		$db = Database::getInstance();
		
		$query = 'INSERT INTO `user` (`google_id`, `google_email`, `academic_email`, `verified`)
						 VALUES (:google_id, :google_email, :academic_email, 1)';
			
		$preparedStatement = $db->prepare($query);
			
		$preparedStatement->execute( array(
			':google_id' => $faker->numerify('#####################'),
			':google_email' => $faker->freeEmail(),
			':academic_email' => $faker->regexify('/^cse[0-9]{5}@stef\.teipir\.gr$/')
		));
	}
	catch ( Google_Auth_Exception $e ) {
		$logger = new ExceptionLogger();
		$logger->error($e);
		throw new Exception('Auth error, unable to verify id_token.');
	}
	catch ( PDOException $e ) {
		$logger = new ExceptionLogger();
		$logger->error($e);
		throw new Exception('Database error, unable to insert user.');
	}

}