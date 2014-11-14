<?php require_once '../src/init.php'; 
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

if ( $user->isSignedIn() === true && $user->isVerified() === true ) {
	
	try {
		$directoryURL = $drive->getFileURL(DIRECTORY_ID);
	}
	catch ( Google_Service_Exception $e ) {
		$directoryURL = '#';
		$logger = new ExceptionLogger();
		$logger->error($e);
		Notifier::push('error', 'Unable to establish connection with Google Service, please try again later. If the error persists contact the administrator.');
	}
	
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Drivenote | <?php echo SCHOOL_NAME; ?></title>
	<meta name="description" content="<?php echo DESCRIPTION; ?>">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0;">
	<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="/css/basestyle.css">
	
	<!--[if IE]><link rel="shortcut icon" href="favicon.ico"><![endif]-->
	<link rel="icon" href="favicon.png">
	
	<?php if ( GOOGLE_ANALYTICS_ID !== false ): ?>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		ga('create', '<?php echo GOOGLE_ANALYTICS_ID; ?>', 'auto');
		ga('send', 'pageview');
	</script>
	<?php endif; ?>
</head>
<body>
	<div id="wrapper">
		<header>
			<h1 id="title">Drivenote | <?php echo SCHOOL_NAME; ?></h1>
		</header>
		<div id="content">
			<?php $notification = Notifier::pop(); ?>
		
			<?php if ( !empty($notification) ): ?>
				<div class="notification <?php echo $notification['type']; ?>">
					<h4><?php echo $notification['title']; ?></h4>
					<p><?php echo $notification['message']; ?></p>
				</div>
			<?php endif; ?>	
		
			<div id="midcol">
				<?php if ( $user->isSignedIn() === false ): ?>
					<a class="button" href="<?php echo $auth->getAuthURL(); ?>" rel="nofollow" title="Click to Sign in"><span>Sign in with Google</span></a>
				<?php else: ?>
					<?php if ( $user->isVerified() === false ): ?>
						<form action="activate-account.php" method="post">
							<label for="academic_emal">Academic Email:</label>
							<input type="email" name="academic_email" id="academic_email" placeholder="e.g. <?php echo ACADEMIC_EMAIL_EXAMPLE; ?>">
							<input class="button" type="submit" value="Send Verification">
						</form>
					<?php else: ?>
						<a class="button" href="<?php echo $directoryURL; ?>" title="Click to open google drive folder"><span>Open Google Drive</span></a>
					<?php endif; ?>
					<a class="button" href="signout.php" title="Click to Sign out"><span>Sign out</span></a>
				<?php endif; ?>
			</div>
		</div>
		<footer>
			<p id="copyright">
				Designed by
				<a href="https://twitter.com/magkopian" title="@magkopian" target="_blank">Manolis Agkopian</a>
				&copy; <?php echo date('Y'); ?>
			</p>
		</footer>
	</div>
</body>
</html>