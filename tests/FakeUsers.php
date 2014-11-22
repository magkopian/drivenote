<?php
require_once '../vendor/autoload.php';
require_once '../src/config.php';

$options = getopt('n:', array('clean'));

try {
	$db = Database::getInstance();

	if ( isset($options['clean']) && $options['clean'] === false ) {
		$query = 'DELETE FROM `user` WHERE `user_id` NOT IN (SELECT `user_id` FROM `admin`)';
		$statement = $db->exec($query);
	}
	else {
		
		$num = 1;
		if ( isset($options['n']) && $options['n'] > 0 ) {
			$num = (int) $options['n'];
		}
		
		$query = 'INSERT INTO `user` (`google_id`, `google_email`, `academic_email`, `verified`)
							 VALUES (:google_id, :google_email, :academic_email, :verified)';
		
		$preparedStatement = $db->prepare($query);
		
		$faker = Faker\Factory::create();
		
		for ($i=0; $i < $num; $i++) {
			$preparedStatement->execute( array(
				':google_id' => $faker->numerify('#####################'),
				':google_email' => $faker->freeEmail(),
				':academic_email' => $faker->regexify('/^cse[0-9]{5}@stef\.teipir\.gr$/'),
				':verified' => $faker->numberBetween(0, 1)
			));
		}
		
	}
}
catch ( PDOException $e ) {
	$logger = new ExceptionLogger();
	$logger->error($e);
	throw new Exception('Database error, unable to update users table.');
}
