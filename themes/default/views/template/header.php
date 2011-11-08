<!DOCTYPE html> 
<html> 
 
<head> 
	<meta charset="utf-8"> 
	<title>SwiftRiver</title> 
	<meta name="description" content="SwiftRiver" /> 
	<meta name="keywords" content="SwiftRiver"> 
	<link rel='index' title='SwiftRiver' href='http://swiftriver.com/' /> 
	<link rel="icon" href="/themes/default/media/img/favicon.png" type="image/png">
	<?php echo(Html::style("themes/default/media/css/styles.css")); ?>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<?php
	echo(Html::script("https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"));
	echo(Html::script("themes/default/media/js/jquery.outside.js"));
	echo(Html::script("themes/default/media/js/global.js"));
	?>
</head> 
 
<body> 
	<header>
		<div class="left_bar"></div>
		<div class="center cf">
			<hgroup>
				<h1 class="logo"><a href="/"><span class="nodisplay">SwiftRiver</span></a></h1>
			</hgroup>
			<?php echo $nav_header;?>
		</div>
		<div class="right_bar"></div>
	</header>
	
	<article>
		<div class="cf center page_title">
			<hgroup class="edit">
				<h1><span class="edit_trigger" onclick="">River 1</span></h1>
			</hgroup>
			<?php
			if (isset($droplets))
			{
				?>
				<section class="meter">
					<p style="padding-left:<?php echo $meter; ?>%;"><strong><?php echo $droplets; ?></strong> droplets</p>
					<div><span style="width:<?php echo $meter; ?>%;"></span></div>
				</section>
				<?php
			}
			?>
		</div>
		
		<div class="center canvas">
			<section class="panel">		
				<?php echo $nav_canvas; ?>
				<div class="panel_body"></div>
			</section>