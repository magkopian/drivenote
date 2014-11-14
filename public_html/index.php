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
<?php include '../src/template/head.php'; ?>
<body>
	<div id="wrapper">
		<?php include '../src/template/header.php'; ?>
		<div id="content">
		
			<?php include '../src/template/notification.php'; ?>
			
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
					<?php if ( $user->isAdmin() === true ): ?>
						<a class="button" href="admin.php" title="Click to open admin panel"><span>Admin Panel</span></a>
					<?php endif; ?>
					<a class="button" href="signout.php" title="Click to Sign out"><span>Sign out</span></a>
				<?php endif; ?>
			</div>
			
		</div>
		<?php include '../src/template/footer.php'; ?>
	</div>
</body>
</html>