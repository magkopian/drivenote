<?php require_once '../src/init.php'; 
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/

// Check if user is signed in and is admin
if ( !$user->isSignedIn() || !$user->isAdmin() ) {
	header('Location: /');
	die();
}
$users = $user->search(null, 0, 9999);
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
				<table>
					<tr>
						<th>ID</th>
						<th>Google Email</th>
						<th>Academic Email</th>
						<th>Status</th>
						<th>Extra</th>
					</tr>
					<?php foreach ( $users as $user ): ?>
					<tr>
						<td><?php echo $user['user_id']; ?></td>
						<td><?php echo $user['google_email']; ?></td>
						<td><?php echo $user['academic_email']; ?></td>
						<td><?php echo $user['verified'] ? 'Verified' : 'Not Verified'; ?></td>
						<td><?php echo $user['is_admin'] ? $user['access_level'] > 0 ? 'Moderator' : 'Administrator' : ''; ?></td>
					<tr>
					<?php endforeach; ?>
				</table>
			</div>
			
		</div>
		<?php include '../src/template/footer.php'; ?>
	</div>
</body>
</html>