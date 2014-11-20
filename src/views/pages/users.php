<?php 
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/
?>
<!DOCTYPE html>
<html lang="en">
<?php $scripts = '
<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript" src="/js/users.js"></script>
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
								<th class="checkbox-cell">ID<input type="checkbox" name="user-all" id="user-all"></th>
								<th>Google Email</th>
								<th>Academic Email</th>
								<th>Status</th>
								<th>Role</th>
								<th>Extra</th>
							</tr>
						</thead>
						<tbody>
							<?php $i = 0; foreach ( $users['records'] as $user_data ): ?>
							<tr class="<?php echo $i++ % 2 == 0 ? 'even' : 'odd'; ?>">
								<td class="checkbox-cell">
									<label for="user-<?php echo $user_data['user_id']; ?>"><?php echo $user_data['user_id']; ?></label>
									<input type="checkbox" name="users[]" id="user-<?php echo $user_data['user_id']; ?>" value="<?php echo $user_data['user_id']; ?>">
								</td>
								<td><?php echo $user_data['google_email']; ?></td>
								<td><?php echo $user_data['academic_email']; ?></td>
								<td><?php echo $user_data['verified'] ? 'Verified' : 'Not Verified'; ?></td>
								<td><?php echo isset($userPermissions[$user_data['google_email']]) ? ucfirst($userPermissions[$user_data['google_email']]['role']) : ''; ?></td>
								<td><?php echo $user_data['is_admin'] ? $user_data['access_level'] > 0 ? 'Moderator' : 'Administrator' : ''; ?></td>
							<tr>
							<?php endforeach; ?>
						</tbody>
					</table>
					<div class="user-actions">
						<a href="#" class="button button-small" data-action="delete">Delete Selected</a>
						<a href="#" class="button button-small" data-action="revoke-read">Revoke Read Access</a>
						<a href="#" class="button button-small" data-action="grant-read">Grant Read Access</a>
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
								<option value="<?php echo $valid_limit;?>" <?php echo $limit == $valid_limit ? 'selected="selected"' : ''; ?>>
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