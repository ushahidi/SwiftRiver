<!DOCTYPE html> 
<html> 
 
<head> 
	<meta charset="utf-8"> 
	<title>Set Password - SwiftRiver</title> 
	<meta name="description" content="SwiftRiver" /> 
	<meta name="keywords" content="SwiftRiver"> 
	<link rel='index' title='SwiftRiver' href='http://swiftriver.com/' /> 
	<link rel="icon" href="/images/favicon.png" type="image/png">
	<?php echo(Html::style("themes/default/media/css/styles.css")); ?>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
	<?php
	echo(Html::script("https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"));
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
		</div>
		<div class="right_bar"></div>
	</header>
	
	<article id="login">
		
		<div class="center canvas controls">
			<?php
			if (isset($errors))
			{
				foreach ($errors as $message)
				{
					?>
					<div class="system_message system_error">
						<p><strong>Uh oh.</strong> <?php echo $message; ?></p>
					</div>
					<?php
				}
			}
			?>	
			<?php
			if (isset($messages))
			{
				foreach ($messages as $message)
				{
					?>
					<div class="system_message system_success">
						<p><strong><?php echo __('Success!'); ?></strong> <?php echo $message; ?></p>
					</div>
					<?php
				}
			}
			?>							
		</div>
	</article>
	
	<footer class="center">
		<p><a href="/">SwiftRiver</a></p>
		<ul>
			<li><a href="#">Who uses it.</a></li>
			<li><a href="#">How it works.</a></li>
		</ul>
	</footer>
</body> 
</html>