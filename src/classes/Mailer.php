<?php

class Mailer {
	protected static $from = 'no-reply@drivenote-teipir.tk';
	protected static $fromName = 'drivenote-teipir.tk';
	protected static $instance = null;
	protected $mail = null;

	protected function __construct () {
		
		$this->mail = new PHPMailer();
		$this->mail->CharSet = SMTP_CHARSET;
		$this->mail->IsSMTP();
		$this->mail->Host = SMTP_HOST;
		$this->mail->Port = SMTP_PORT;
		$this->mail->SMTPAuth = SMTP_AUTH;
		$this->mail->Username = SMTP_USER;
		$this->mail->Password = SMTP_PASSWD;
		$this->mail->SMTPSecure = SMTP_ENCRYPTION;
		$this->mail->XMailer = ' '; // Disable X-Mailer Header
		
	}

	public static function getInstance () {

		if ( static::$instance === null ) {
			static::$instance = new static();
		}
		
		return static::$instance;
	
	}

	public function send ( $to, $subject, $message, $isHTML = false, $from = null, $fromName = null, $alternateBody = null ) {

		if ( $from === null ) {
			$from = self::$from; // If from is not provided use default
		}
		
		if ( $fromName === null ) {
			$fromName = self::$fromName; // If from name is not provided use default
		}
		
		$this->mail->setFrom($from, $fromName);
		
		$this->mail->ClearAllRecipients(); // Clear previous all recipients
		$this->mail->AddAddress($to); // Name is optional
		
		$this->mail->IsHTML($isHTML); // Set email format to HTML
		
		$this->mail->Subject = $subject;
		$this->mail->Body = $message;
		
		if ( $alternateBody !== null && $isHTML === true ) {
			$this->mail->AltBody = $alternateBody;
		}
		
		return $this->mail->Send();
	
	}

}