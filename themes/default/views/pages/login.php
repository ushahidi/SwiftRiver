<!DOCTYPE html> 
<html> 
 
<head> 
	<meta charset="utf-8"> 
	<title>Log in - SwiftRiver</title> 
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
		<div class="cf center page-title">
			<hgroup>
				<h1>Log in</h1>
			</hgroup>
		</div>
		
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
			<?php echo Form::open(); ?>
				<div class="row cf">
					<div class="input">
						<h3><?php echo __('Email'); ?></h3>
						<?php echo Form::input("username", ""); ?>
					</div>
				</div>
				<div class="row cf">
					<div class="input">
						<h3><?php echo __('Password'); ?></h3>
						<?php echo Form::password("password", ""); ?>
					</div>
				</div>
				<div class="row controls_buttons cf">
					<p class="button_go" onclick="submitForm(this)"><a>Get started</a></p>
					<!--p class="other"><a href="#"><span></span>Forgot your password?</a></p-->
				</div>
			<?php echo Form::close(); ?>
			<div class="row cf">
			    <?php echo Form::open(); ?>
				<div class="input has_dropdown">
					<a><?php echo __('Create an account'); ?></a>
					<ul class="dropdown"  style="display: none">
						<li><strong><?php echo __('Enter your email address below') ?></strong><?php echo Form::input("new_email", ""); ?></li>
						<li><div class="buttons" onclick="submitForm(this)"><button class="save"><?php echo __('Register');?></button></div></li>
					</ul>
				</div>
				<?php echo Form::close(); ?>
				<?php echo Form::open(); ?>
				<div class="input has_dropdown">
					<a><?php echo __('Forgot your password?'); ?></a>
					<ul class="dropdown"  style="display: none">
						<li><strong><?php echo __('Enter the e-mail address used for registration') ?></strong><?php echo Form::input("recover_email", ""); ?></li>
						<li><div class="buttons" onclick="submitForm(this)"><button class="save"><?php echo __('Reset password');?></button></div></li>
					</ul>
				</div>
				<?php echo Form::close(); ?>
			</div>							
			
			<section class="detail cf">
				<div class="arrow top"><span></span></div>
				<div class="canyon cf">
					<h2>Why SwiftRiver kicks ass.</h2>
				</div>
				<div class="arrow bottom"><span></span></div>
			</section>
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