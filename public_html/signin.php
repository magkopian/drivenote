<?php require_once '../includes/init.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>CSE Google Drive</title>
</head>
<body>
	<?php if ( $auth->isSignedIn() === false ): ?>
		<a href="<?php echo $auth->getAuthURL(); ?>" title="Click to Sign in">Sign in with Google</a>
	<?php else: ?>
		<a href="signout.php" title="Click to Sign out">Sign out</a>
	<?php endif; ?>
</body>
</html>