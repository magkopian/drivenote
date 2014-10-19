<?php require_once '../src/init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Drivenote | Teipir</title>
	<link rel="stylesheet" type="text/css" href="/css/basestyle.css">
</head>
<body>
	<div id="wrapper">
		<header>
			<h1 id="title">Drivenote | Teipir</h1>
		</header>
		<div id="content">
			<?php $notification = Notifier::pop(); ?>
		
			<?php if ( !empty($notification) ): ?>
				<div class="notification <?php echo $notification['type']; ?>">
					<h4><?php echo $notification['title']; ?></h4>
					<p><?php echo $notification['message']; ?></p>
				</div>
			<?php endif; ?>	
		
			<?php if ( $user->isSignedIn() === false ): ?>
				<a class="button" href="<?php echo $auth->getAuthURL(); ?>" title="Click to Sign in"><span>Sign in with Google</span></a>
			<?php else: ?>
				<?php if ( $user->isVerified() === false ): ?>
					<form action="activate-account.php" method="post">
						<label for="academic_emal">Academic Email:</label>
						<input type="text" name="academic_email" id="academic_email" placeholder="e.g. cse12345@stef.teipir.gr">
						<input class="button" type="submit" value="Send Verification">
					</form>
				<?php else: ?>
					<a class="button" href="<?php echo $drive->getFileURL(DIRECTORY_ID); ?>" title="Click to open google drive folder"><span>Open Google Drive</span></a>
				<?php endif; ?>
				<a class="button" href="signout.php" title="Click to Sign out"><span>Sign out</span></a>
			<?php endif; ?>
		</div>
		<footer>
		
		</footer>
	</div>
</body>
</html>