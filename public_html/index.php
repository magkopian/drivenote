<?php require_once '../src/init.php'; 
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Drivenote | <?php echo SCHOOL_NAME; ?></title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0;">
	<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="/css/basestyle.css">
	<!--[if IE]><link rel="shortcut icon" href="favicon.ico"><![endif]-->
	<link rel="icon" href="favicon.png">
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
							<input type="text" name="academic_email" id="academic_email" placeholder="e.g. <?php echo ACADEMIC_EMAIL_EXAMPLE; ?>">
							<input class="button" type="submit" value="Send Verification">
						</form>
					<?php else: ?>
						<a class="button" href="<?php echo $drive->getFileURL(DIRECTORY_ID); ?>" title="Click to open google drive folder"><span>Open Google Drive</span></a>
					<?php endif; ?>
					<a class="button" href="signout.php" title="Click to Sign out"><span>Sign out</span></a>
				<?php endif; ?>
			</div>
		</div>
		<footer>
			<p id="copyright">
				Designed by
				<a href="https://twitter.com/magkopian" title="@magkopian" target="_blank">Manolis Agkopian</a>
				&copy; 2012 - <?php echo date('Y'); ?>
			</p>
		</footer>
	</div>
</body>
</html>