<?php 
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/
?>

<?php $notification = Notifier::pop(); ?>
		
<?php if ( !empty($notification) ): ?>
	<div class="notification <?php echo $notification['type']; ?>">
		<h4><?php echo $notification['title']; ?></h4>
		<p><?php echo $notification['message']; ?></p>
	</div>
<?php endif; ?>