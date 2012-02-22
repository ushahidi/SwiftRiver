<!DOCTYPE html> 
<html> 
 
<head> 
	<meta charset="utf-8"> 
	<title>SwiftRiver</title> 
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
	<?php
	echo(Html::script("https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"));
	echo(Html::script("themes/default/media/js/jquery.outside.js"));
	echo(Html::script("themes/default/media/js/underscore-min.js"));
	echo(Html::script("themes/default/media/js/backbone-min.js"));
	echo(Html::script("themes/default/media/js/global.js"));

	// Dynamic JS Files
	echo(Html::script('media/js'));
	?>
	<script type="text/javascript"><?php
		// Dynamic inline JS
		echo $js;
	?></script>
	
	<?php
	    // SwiftRiver Plugin Hook
	    Swiftriver_Event::run('swiftriver.template.head');
	?>
</head> 
 
<body> 
	<header>
		<div class="left_bar"></div>
		<div class="center cf">
			<hgroup>
				<h1 class="logo"><a href="<?php echo URL::site().$user->account->account_path; ?>"><span class="nodisplay">SwiftRiver</span></a></h1>
			</hgroup>
			<?php echo $nav_header;?>
		</div>
		<div class="right_bar"></div>
	</header>
	