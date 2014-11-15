<?php 
/**********************************************\
* Copyright (c) 2014 Manolis Agkopian          *
* See the file LICENCE for copying permission. *
\**********************************************/
?>
<head>
	<meta charset="UTF-8">
	<title>Drivenote | <?php echo SCHOOL_NAME; ?></title>
	<meta name="description" content="<?php echo DESCRIPTION; ?>">
	
	<meta name="viewport" content="width=device-width, initial-scale=1.0;">
	<link href='http://fonts.googleapis.com/css?family=Cantarell' rel='stylesheet' type='text/css'>
	<link rel="stylesheet" type="text/css" href="/css/basestyle.css?v=1.1.0">
	
	<!--[if IE]><link rel="shortcut icon" href="favicon.ico"><![endif]-->
	<link rel="icon" href="favicon.png">
	
	<?php if ( GOOGLE_ANALYTICS_ID !== false ): ?>
	<script>
		(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
		(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
		m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
		})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
		
		ga('create', '<?php echo GOOGLE_ANALYTICS_ID; ?>', 'auto');
		ga('send', 'pageview');
	</script>
	<?php endif; ?>
</head>