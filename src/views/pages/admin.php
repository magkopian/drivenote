<?php 
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/
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
				<table>
					<thead>
						<tr>
							<th>ID</th>
							<th>Google Email</th>
							<th>Academic Email</th>
							<th>Status</th>
							<th>Extra</th>
						</tr>
					</thead>
					<tbody>
						<?php $i = 0; foreach ( $users as $user_data ): ?>
						<tr class="<?php echo $i++ % 2 == 0 ? 'even' : 'odd'; ?>">
							<td><?php echo $user_data['user_id']; ?></td>
							<td><?php echo $user_data['google_email']; ?></td>
							<td><?php echo $user_data['academic_email']; ?></td>
							<td><?php echo $user_data['verified'] ? 'Verified' : 'Not Verified'; ?></td>
							<td><?php echo $user_data['is_admin'] ? $user_data['access_level'] > 0 ? 'Moderator' : 'Administrator' : ''; ?></td>
						<tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			
		</div>
		<?php include '../src/views/template/footer.php'; ?>
	</div>
</body>
</html>