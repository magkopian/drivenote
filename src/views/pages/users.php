<?php
/************************************************\
 * Copyright (c) 2016 Manolis Agkopian          *
 * See the file LICENCE for copying permission. *
\************************************************/
?>
<!DOCTYPE html>
<html lang="en">
	<?php $scripts = '
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="/js/users.js"></script>
<script type="text/javascript" src="/js/notification.js"></script>
'; ?>
	<?php include '../src/views/template/head.php'; ?>
	<body>
		<div id="wrapper">
			<?php include '../src/views/template/header.php'; ?>
			<div id="content">

				<?php include '../src/views/template/notification.php'; ?>

				<div id="midcol">
					<?php if ( !empty($users['records']) ): ?>
						<table id="users">
							<thead>
								<tr>
									<th class="checkbox-cell">ID<input type="checkbox" name="user-all" id="user-all">
									</th>
									<th>Google Email</th>
									<th>Academic Email</th>
									<th>Verified</th>
									<th>Role</th>
									<th>Member</th>
									<th>Extra</th>
								</tr>
							</thead>
							<tbody>
								<?php $i = 0;
								foreach ( $users['records'] as $user_data ): ?>
									<tr class="<?php echo $i++ % 2 == 0 ? 'odd' : 'even'; ?>">
										<td class="uid checkbox-cell">
											<label for="user-<?php echo $user_data['user_id']; ?>"><?php echo $user_data['user_id']; ?></label>
											<input type="checkbox" name="users[]" id="user-<?php echo $user_data['user_id']; ?>" value="<?php echo $user_data['user_id']; ?>">
										</td>
										<td class="gmail"><?php echo $user_data['google_email']; ?></td>
										<td class="amail"><?php echo $user_data['academic_email']; ?></td>
										<td class="status <?php echo $user_data['verified'] ? 'table-success' : 'table-error'; ?>"><?php echo $user_data['verified'] ? 'Yes' : 'No'; ?></td>
										<td class="role"><?php echo isset($userPermissions[$user_data['google_email']]) ? ucfirst($userPermissions[$user_data['google_email']]['role']) : ''; ?></td>
										<td class="member <?php echo $user_data['approved'] ? 'table-success' : 'table-error'; ?>"><?php echo $user_data['approved'] ? 'Yes' : 'No'; ?></td>
										<td class="extra"><?php echo $user_data['is_admin'] ? $user_data['access_level'] > 0 ? 'Moderator' : 'Administrator' : ''; ?></td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
						<div class="user-actions">
							<a href="#" class="button button-small button-danger" data-action="delete">Delete</a>
							<a href="#" class="button button-small button-danger" data-action="revoke-access">Revoke
								Access</a>
							<a href="#" class="button button-small" data-action="grant-read">Grant Read Access</a>
							<a href="#" class="button button-small button-danger" data-action="revoke-membership">Revoke
								Membership</a>
							<a href="#" class="button button-small" data-action="grant-membership">Mark As Member</a>
							<a href="/members.php" class="button button-small"><strong>&plus;</strong> Add Members</a>
						</div>
						<?php if ( $last_page > 1 ): ?>
							<ul class="page-nav">
								<?php for ( $i = 1; $i <= $last_page; ++$i ): ?>
									<li <?php echo $page == $i ? 'class="selected"' : ''; ?>>
										<?php if ( $page != $i ): ?>
											<a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
										<?php else: ?>
											<?php echo $i; ?>
										<?php endif; ?>
									</li>
								<?php endfor; ?>
							</ul>
							<form action="" method="post" class="elements-per-page">
								<p>Displaying:
									<select name="limit" id="limit" onchange="this.form.submit()">
										<?php foreach ( $valid_limits as $valid_limit ): ?>
											<option value="<?php echo $valid_limit; ?>" <?php echo $limit == $valid_limit ? 'selected="selected"' : ''; ?>>
												<?php echo $valid_limit; ?>
											</option>
										<?php endforeach; ?>
									</select>
								</p>
								<p>
									<?php echo 'Results: ', $offset + 1, ' - ', $offset + count($users['records']), ' from ', $users['total_users']; ?>
								</p>
							</form>
						<?php endif; ?>
					<?php else: ?>
						<p>No users found in the database</p>
					<?php endif; ?>
				</div>

			</div>
			<?php include '../src/views/template/footer.php'; ?>
		</div>
	</body>
</html>
