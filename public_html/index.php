<?php require_once '../src/init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Teipir Drivenote</title>
</head>
<body>
	<?php $notification = Notifier::pop(); ?>

	<?php if ( !empty($notification) ): ?>
		<div class="notification">
			<span class="notification-title"><?php echo $notification['title']; ?></span>
			<span class="notification-body"><?php echo $notification['message']; ?></span>
		</div>
	<?php endif; ?>
	
	<?php if ( $auth->isSignedIn() === false ): ?>
		<a href="<?php echo $auth->getAuthURL(); ?>" title="Click to Sign in">Sign in with Google</a>
	<?php elseif ( $auth->isVerified() === false ): ?>
		<a href="signout.php" title="Click to Sign out">Sign out</a>
		<form action="activate-account.php" method="post">
			<label for="academic_emal">Academic Email:</label>
			<input type="text" name="academic_email" id="academic_email">
			<input type="submit" value="Verify Account">
		</form>
	<?php else: ?>
		<a href="signout.php" title="Click to Sign out">Sign out</a>
	<?php endif; ?>
</body>
</html>


<?php 
//require_once '../src/init.php';

//$drive = new GoogleDrive($googleClient);

// Grant read access to root directory
//$drive->grantReadAccess('<user_gmail_address>', DIRECTORY_ID);
//$drive->redirect(DIRECTORY_ID);

// Revoke read access to root directory
//$drive->revokeReadAccess('<user_gmail_address>', DIRECTORY_ID);

// List all files
//$drive->listFiles();