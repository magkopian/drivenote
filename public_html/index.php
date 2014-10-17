<?php require_once '../src/init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Drivenote | Teipir</title>
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
	<?php else: ?>
		<a href="signout.php" title="Click to Sign out">Sign out</a>
		<?php if ( $auth->isVerified() === false ): ?>
			<form action="activate-account.php" method="post">
				<label for="academic_emal">Academic Email:</label>
				<input type="text" name="academic_email" id="academic_email">
				<input type="submit" value="Send Verification">
			</form>
		<?php endif; ?>
	<?php endif; ?>
</body>
</html>