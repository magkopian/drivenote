<?php
/************************************************\
 * Copyright (c) 2016 Manolis Agkopian          *
 * See the file LICENCE for copying permission. *
\************************************************/
?>
<!DOCTYPE html>
<html lang="en">
	<?php include '../src/views/template/head.php'; ?>
	<body>
		<div id="wrapper">
			<?php include '../src/views/template/header.php'; ?>
			<div id="content">

				<?php include '../src/views/template/notification.php'; ?>

				<div id="midcol">
					<h2>Users Awaiting Membership</h2>
					<div style="padding: 15px;">
						<?php if ( !empty($users['records']) ): ?>
							<textarea name="users" class="textarea-static" cols="80" rows="10" readonly><?php
								foreach ( $users['records'] as $user ) {
									echo $user['google_email'] . "\n";
								}
								?></textarea>
						<?php else: ?>
							No users are currently awaiting for membership.
						<?php endif; ?>
					</div>
					<div class="user-actions">
						<a class="button button-small" href="/users.php" title="Click to open users' management page">
							<span>Back To Users</span>
						</a>
						<?php if ( !empty($users['records']) ): ?>
							<form action="/user-edit.php" method="POST">
								<?php foreach ( $users['records'] as $user ): ?>
									<input type="hidden" name="users[]" value="<?php echo $user['user_id']; ?>">
								<?php endforeach; ?>
								<input type="hidden" name="action" value="grant-membership">
								<input type="hidden" name="ajax" value="0">
								<input type="submit" class="button button-small" value="Mark All As Members">
							</form>
						<?php endif; ?>
					</div>
				</div>

			</div>
			<?php include '../src/views/template/footer.php'; ?>
		</div>
	</body>
</html>
