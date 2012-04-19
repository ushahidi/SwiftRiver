<!DOCTYPE html> 
<html> 
 
<head> 
	<meta charset="utf-8"> 
	<title><?php echo (isset($title) ? $title.' ~ ' : '').$site_name; ?></title> 
	<meta name="description" content="SwiftRiver" /> 
	<meta name="keywords" content="SwiftRiver"> 
	<link rel='index' title='SwiftRiver' href='http://swiftriver.com/' /> 
	<link rel="icon" href="<?php echo url::base(); ?>themes/default/media/img/favicon.png" type="image/png">
	<?php
	echo $meta; 
	
	echo(Html::style("themes/default/media/css/styles.css"));
	
	// Inline css
	echo $css; 
	
	// System and Other CSS
	echo(Html::script('media/css'));
	?>

	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	
	<script type="text/javascript">
		// Globals
		<?php if ( ! empty($user)): ?>
			<?php if ($user->account->id): ?>
			window.logged_in_account = <?php echo $user->account->id; ?>;
			<?php endif; ?>
			
			window.logged_in_user = <?php echo $user->id; ?>;
		<?php endif; ?>

		window.site_url = "<?php echo URL::base(TRUE, FALSE); ?>";
	</script>
	
	<?php
	echo(Html::script("themes/default/media/js/jquery-1.7.2.min.js"));
	
	// Outside events plugin
	echo(Html::script("themes/default/media/js/jquery.outside.js"));
	
	// Masonry plugin
	echo(Html::script("themes/default/media/js/jquery.masonry.min.js"));
	echo(Html::script("themes/default/media/js/modernizr.custom.01220.js"));
	
	// Fileupload jQuery plugin
	echo(Html::script("themes/default/media/js/jquery.ui.widget.js"));
	echo(Html::script("themes/default/media/js/jquery.iframe-transport.js"));
	echo(Html::script("themes/default/media/js/jquery.fileupload.js"));

	// Touch Plugin
	echo(Html::script("themes/default/media/js/jquery.touch.min.js"));

	// Backbone
	echo(Html::script("themes/default/media/js/underscore-min.js"));
	echo(Html::script("themes/default/media/js/backbone-min.js"));
	
	// SwiftRiver global JS
	echo(Html::script("themes/default/media/js/global.js"));

	// Dynamic JS Files
	echo(Html::script('media/js'));

	// Dynamic inline JS
	echo $js; 
	
    // SwiftRiver Plugin Hook
    Swiftriver_Event::run('swiftriver.template.head');
	?>
</head> 
 
<body> 
	<header class="toolbar">
		<div class="center">
			<h1 class="logo">
				<a href="<?php echo URL::site(); ?>">
					<span class="nodisplay">SwiftRiver</span></a>
				</h1>
			<?php echo $nav_header;?>
		</div>
	</header>