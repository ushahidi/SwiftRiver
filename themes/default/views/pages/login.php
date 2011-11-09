<!DOCTYPE html> 
<html> 
 
<head> 
	<meta charset="utf-8"> 
	<title>Create an account on SwiftRiver</title> 
	<meta name="description" content="SwiftRiver" /> 
	<meta name="keywords" content="SwiftRiver"> 
	<link rel='index' title='SwiftRiver' href='http://swiftriver.com/' /> 
	<link rel="icon" href="/images/favicon.png" type="image/png">
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
			<nav>
				<ul>
					<li class="rivers has_dropdown">
						<a><span class="arrow"></span>Rivers</a>
						<ul class="dropdown">
							<li><a href="/">River 1</a></li>
							<li><a href="/">River 2</a></li>
							<li><a href="/river/new.html"><em>Create new</em></a></li>
						</ul>
					</li>
					<li class="buckets has_dropdown">
						<a><span class="arrow"></span>Buckets</a>
						<ul class="dropdown">
							<li><a href="/bucket/">Bucket 1</a></li>
							<li><a href="/bucket/">Bucket 2</a></li>
							<li class="create_new"><a><em>Create new</em></a></li>
						</ul>
					</li>
					<div class="account">
						<li class="login has_dropdown">
							<a><span class="arrow"></span><?php echo __('Log in'); ?></a>
							<ul class="dropdown">
								<?php echo Form::open(); ?>
									<li><strong><?php echo __('Email'); ?></strong><?php echo Form::input("username", ""); ?></li>
									<li><strong><?php echo __('Password'); ?></strong><?php echo Form::password("password", ""); ?></li>
									<li><div class="buttons btn_click"><button class="save"><?php echo __('Log in'); ?></button></div></li>
								<?php echo Form::close(); ?>
							</ul>
						</li>
					</div>
				</ul>
			</nav>
		</div>
		<div class="right_bar"></div>
	</header>
	
	<article id="login">
		<div class="cf center page_title">
			<hgroup>
				<h1>Create an account</h1>
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
						<input type="text" />
					</div>
				</div>				
				<div class="row controls_buttons cf">
					<p class="button_go btn_click"><a>Get started</a></p>
					<!--p class="other"><a href="#"><span></span>Forgot your password?</a></p-->
				</div>
			<?php echo Form::close(); ?>
			
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