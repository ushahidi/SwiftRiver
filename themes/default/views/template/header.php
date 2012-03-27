<!DOCTYPE html> 
<html> 
 
<head> 
	<meta charset="utf-8"> 
	<title><?php echo (isset($title) ? $title.' - ' : '').$site_name; ?></title> 
	<meta name="description" content="SwiftRiver" /> 
	<meta name="keywords" content="SwiftRiver"> 
	<link rel='index' title='SwiftRiver' href='http://swiftriver.com/' /> 
	<link rel="icon" href="<?php echo url::base(); ?>themes/default/media/img/favicon.png" type="image/png">
	<?php
	echo(Html::style("themes/default/media/css/styles.css"));
	
	// System and Other CSS
	echo(Html::script('media/css'));
	?>

	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	
	<script type="text/javascript">
		// Globals
		window.buckets_url = "<?php echo url::site().$user->account->account_path.'/bucket/buckets/manage'; ?>";
		window.logged_in_account = <?php echo $user->account->id; ?>;
		window.site_url = "<?php URL::site(); ?>";
	</script>
	
	<?php
	echo(Html::script("themes/default/media/js/jquery-1.7.1.min.js"));
	echo(Html::script("themes/default/media/js/jquery.cycle.all.latest.min.js"));
	echo(Html::script("themes/default/media/js/jquery.outside.js"));
	echo(Html::script("themes/default/media/js/jquery.masonry.js"));
	echo(Html::script("themes/default/media/js/underscore-min.js"));
	echo(Html::script("themes/default/media/js/backbone-min.js"));
	echo(Html::script("themes/default/media/js/jquery.sparkline.min.js"));
	echo(Html::script("themes/default/media/js/global.js"));
	echo(Html::script("themes/default/media/js/buckets.js"));

	// Dynamic JS Files
	echo(Html::script('media/js'));
	?>
		
	
	<?php 
		// Dynamic inline JS
		echo $js; 
	?>
	
	<?php
	    // SwiftRiver Plugin Hook
	    Swiftriver_Event::run('swiftriver.template.head');
	?>
	
</head> 
 
<body> 
	<header class="toolbar">
		<div class="center">
			<h1 class="logo"><a href="<?php echo URL::site().$user->account->account_path; ?>"><span class="nodisplay">SwiftRiver</span></a></h1>
			<?php echo $nav_header;?>
		</div>
	</header>