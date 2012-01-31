<!DOCTYPE html> 
<html> 
 
<head> 
	<meta charset="utf-8"> 
	<title>Log into SwiftRiver</title> 
	<meta name="description" content="SwiftRiver" /> 
	<meta name="keywords" content="SwiftRiver"> 
	<link rel='index' title='SwiftRiver' href='http://swiftriver.com/' /> 
	<link rel="icon" href="/themes/default/media/img/favicon.png" type="image/png">
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
			<nav>
				<ul>
					<li class="rivers has_dropdown">
						<a><span class="arrow"></span>Rivers</a>
					</li>
					<li class="buckets has_dropdown">
						<a><span class="arrow"></span>Buckets</a>
					</li>
					<div class="account">
						<li class="create"><a href="<?php echo url::site().'welcome';?>"><?php echo __('Create an account');?></a></li>
						<li class="login has_dropdown">
							<a><span class="arrow"></span><?php echo __('Log in');?></a>
							<?php echo Form::open(); ?>
								<ul class="dropdown">
									<li><strong><?php echo __('Email');?></strong><?php echo Form::input("username", ""); ?></li>
									<li><strong><?php echo __('Password');?></strong><?php echo Form::password("password", ""); ?></li>
									<li><div class="buttons btn_click"><button class="save"><?php echo __('Log in');?></button></div></li>
								</ul>
							<?php echo Form::close(); ?>
						</li>						
					</div>
				</ul>
			</nav>
		</div>
		<div class="right_bar"></div>
	</header>
	
	<article id="login">
		<div class="cf center page-title">
			<hgroup>
				<h1><?php echo __("Create an account");?></h1>
			</hgroup>
		</div>
		
		<div class="center canvas controls">
		
		<?php if (isset($errors)): ?>
			<?php foreach ($errors as $message): ?>
				<div class="system_message system_error">
					<p>><strong><?php echo __("Uh oh."); ?></strong><?php echo $message; ?></p>
				</div>
			<?php endforeach; ?>
		<?php endif; ?>
		
		<?php echo Form::open(); ?>
			<div class="row cf">
				<div class="input">
					<h3><?php echo __('Email'); ?></h3>
					<?php echo Form::input("username", ""); ?>
				</div>
				<div class="input">
					<h3><?php echo __('Password'); ?></h3>
					<?php echo Form::password("password", ""); ?>
				</div>
			</div>
			<div class="row cf">
				<div class="input">
					<h3><?php echo __('URL'); ?></h3>
					<?php echo Form::input("url", ""); ?>
				</div>
			</div>
			<div class="row controls-buttons cf btn_click">
				<p class="button-go"><a onclick="submitForm(this)"><?php echo __('Get started'); ?></a></p>
				<!--p class="other"><a href="#"><span></span>Forgot your password?</a></p-->
			</div>
		<?php echo Form::close(); ?>
		
		</div>
	</article>
</body> 
</html>