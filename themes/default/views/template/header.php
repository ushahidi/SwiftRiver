<?xml version="1.0"?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en" />
	<meta name="robots" content="noindex,nofollow" />
	
	<?php echo(Html::style("themes/default/media/css/reset.css")); ?>
	<?php echo(Html::style("themes/default/media/css/jquery-ui-1.8.16.custom.css")); ?>
	<?php echo(Html::style("themes/default/media/css/main.css")); ?>
	<?php echo(Html::style("themes/default/media/css/2col.css", array("title" => "2col"))); ?>
	<?php echo(Html::style("themes/default/media/css/1col.css", array("title" => "1col"))); ?>
	<!--[if lte IE 6]>
		<?php echo(Html::style("themes/default/media/css/main-ie6.css")); ?>
	<![endif]-->
	<?php echo(Html::style("themes/default/media/css/style.css")); ?>
	<?php echo(Html::style("themes/default/media/css/pagination.css")); ?>
	<?php echo(Html::style("themes/default/media/css/ui.slider.extras.css")); ?>
	<?php
	// Sweeper Plugin Hook -- Add CSS
	Event::run('sweeper.header.css');
	?>
	
	<?php echo(Html::script("themes/default/media/js/jquery.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.tablesorter.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.switcher.js")); ?>
	<?php echo(Html::script("themes/default/media/js/toggle.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.ui.core.min.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.ui.widget.min.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.ui.mouse.min.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.ui.tabs.min.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.ui.dialog.min.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.ui.position.min.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.ui.accordion.min.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.ui.draggable.min.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.ui.resizable.min.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.ui.selectable.min.js")); ?>
	<?php echo(Html::script("themes/default/media/js/jquery.ui.slider.min.js")); ?>
	<?php echo(Html::script("themes/default/media/js/selectToUISlider.jQuery.js")); ?>
	<?php
	// Sweeper Plugin Hook -- Add Script JS
	Event::run('sweeper.header.js');
	?>
	<script type="text/javascript">
		<?php
			// Dynamic Javascript
			echo $js;
		?>
	</script>
	<title>Sweeper</title>
</head>

<body>

<div id="main">

	<!-- Tray -->
	<div id="tray" class="box">

		<p class="f-left box">

			<!-- Switcher -->
			<span class="f-left" id="switcher">
				<a href="#" rel="1col" class="styleswitch ico-col1" title="Display one column"><img src="<?php echo URL::base();?>themes/default/media/img/switcher-1col.gif" alt="1 Column" /></a>
				<a href="#" rel="2col" class="styleswitch ico-col2" title="Display two columns"><img src="<?php echo URL::base();?>themes/default/media/img/switcher-2col.gif" alt="2 Columns" /></a>
			</span>

			<?php echo __('Project');?>: <strong><?php echo $active_project; ?></strong>

		</p>

		<p class="f-right"><?php echo __('User');?>: <strong><?php echo $user->name; ?></strong> (<a href="<?php echo URL::site('/profile');?>"><?php echo __('My Profile');?></a>) &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <strong><a href="<?php echo URL::site('/login/done');?>" id="logout"><?php echo __('Log Out');?></a></strong></p>

	</div> <!--  /tray -->
	 <!-- /header -->

	<hr class="noscreen" />

	<!-- Columns -->
	<div id="cols" class="box">
		
		<?php echo $menu; ?>

		<hr class="noscreen" />

		<!-- Content (Right Column) -->
		<div id="content" class="box">

			<h1><?php echo $page_title; ?></h1>
			<?php echo $tab_menu; ?>