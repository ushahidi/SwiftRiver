<!DOCTYPE html> 
<html> 
 
<head> 
	<meta charset="utf-8" /> 
	<title><?php echo (isset($title) ? $title.' ~ ' : '').$site_name; ?></title> 
	<meta name="description" content="SwiftRiver" /> 
	<meta name="keywords" content="SwiftRiver">
	<?php Swiftriver_Event::run('swiftriver.template.meta'); ?>
	<link rel="index" title="SwiftRiver" href="<?php echo URL::base(TRUE,TRUE); ?>" /> 
	<link rel="icon" href="<?php echo url::base(); ?>themes/default/media/img/favicon.png" type="image/png">
	<?php
	echo $meta; 
	
	echo(HTML::style("themes/default/media/css/styles.css"));
	
	// Inline css
	echo $css;
	Swiftriver_Event::run('swiftriver.template.head.css');
	?>

	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	
	<script type="text/javascript">
		// Globals
		<?php if ( ! empty($user)): ?>
			<?php if ($user->account->id): ?>
				window.logged_in_account = <?php echo $user->account->id; ?>;
				window.logged_in_account_path = "<?php echo $user->account->account_path; ?>";
			<?php endif; ?>
			
			window.logged_in_user = <?php echo $user->id; ?>;
		<?php else: ?>
			window.logged_in_account = null;
			window.logged_in_account_path = null;
			window.logged_in_user = null;
		<?php endif; ?>
		window.public_registration_enabled = <?php echo Model_Setting::get_setting('public_registration_enabled'); ?>;
		window.site_url = "<?php echo URL::base(TRUE, FALSE); ?>";
	</script>
	
	<?php
	echo(HTML::script("themes/default/media/js/jquery-1.7.2.min.js"));
	
	// Outside events plugin
	echo(HTML::script("themes/default/media/js/jquery.outside.js"));
	
	// Masonry plugin
	echo(HTML::script("themes/default/media/js/jquery.masonry.min.js"));
	echo(HTML::script("themes/default/media/js/jquery.imagesloaded.min.js"));
	echo(HTML::script("themes/default/media/js/modernizr.custom.01220.js"));
	
	// Fileupload jQuery plugin
	echo(HTML::script("themes/default/media/js/jquery.ui.widget.js"));
	echo(HTML::script("themes/default/media/js/jquery.iframe-transport.js"));
	echo(HTML::script("themes/default/media/js/jquery.fileupload.js"));

	// Touch Plugin
	// echo(HTML::script("themes/default/media/js/jquery.touch.min.js"));

	// Backbone
	echo(HTML::script("themes/default/media/js/underscore-min.js"));
	echo(HTML::script("themes/default/media/js/backbone-min.js"));
	
	// SwiftRiver global JS
	echo(HTML::script("themes/default/media/js/global.js"));
	echo(HTML::script("themes/default/media/js/assets.js"));

	// Dynamic inline JS
	echo $js;
	Swiftriver_Event::run('swiftriver.template.head.js');
	?>
	
	<script type="text/javascript">
		// Loading image
		var loading_image_html = '<?php echo HTML::image("themes/default/media/img/loading.gif", array("class"=>"loading_image")); ?>';
		window.loading_image = $(loading_image_html);
		window.loading_message = $('<div class="loading"></div>').append(loading_image);
		// Preload loading_image
		(new Image()).src = window.loading_image.attr('src');

		//Preload default avatar
		var default_avatar_html = '<?php echo HTML::image("themes/default/media/img/avatar_default.gif"); ?>';
		window.default_avatar = $(default_avatar_html);
		(new Image()).src = window.default_avatar.attr('src');
	</script>
	
	<?php if (isset($bucket_list)): ?>
		<script type="text/javascript">
			$(function() {
				// Bootstrap the global bucket list		
				Assets.bucketList.reset(<?php echo $bucket_list; ?>);
				
				// Bootstrap the global bucket list		
				Assets.riverList.reset(<?php echo $river_list; ?>);
			});
		</script>
	<?php endif; ?>
	<?php Swiftriver_Event::run('swiftriver.template.head'); ?>
</head> 
 
<body> 
	<?php if ($show_nav): ?>
		<header class="toolbar">
			<div class="center">
				<div class="col_4">
					<h1 class="logo">
						<a href="<?php echo $dashboard_url; ?>">
						<span class="nodisplay">SwiftRiver</span>
						</a>
					</h1>
				<?php echo $nav_header;?>
			</div>
		</header>
	<?php endif; ?>
