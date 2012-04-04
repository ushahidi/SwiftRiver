<!DOCTYPE html> 
<html> 
 
<head> 
    <meta charset="utf-8"> 
	<title>Log in ~ SwiftRiver</title> 
	<meta name="description" content="SwiftRiver" /> 
	<meta name="keywords" content="SwiftRiver"> 
	<link rel='index' title='SwiftRiver' href='http://swiftriver.com/' /> 
	<link rel="icon" href="/images/favicon.png" type="image/png">	<?php echo(Html::style("themes/default/media/css/styles.css")); ?>
	<?php
	echo(Html::script("themes/default/media/js/jquery-1.7.2.min.js"));
	echo(Html::script("themes/default/media/js/jquery.outside.js"));
	echo(Html::script("themes/default/media/js/global.js"));
	?>
	<script type="text/javascript">
		$(function() {

			// Focus email field
		    $("input[name=username]").focus();
		});
	</script>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
</head> 
 
<body> 
	<header class="toolbar">
		<div class="center">
			<h1 class="logo"><a href="/markup/"><span class="nodisplay">SwiftRiver</span></a></h1>
			<ul class="toolbar-menu">
				<li class="search"><a href="/markup/modal-search.php" class="modal-trigger"><span class="icon"></span><span class="label">Search</span></a></li>
				<li class="create"><a href="/markup/modal-create.php" class="modal-trigger"><span class="icon"></span><span class="label">Create</span></a></li>
				<li class="login"><a href="/login" class="modal-trigger"><span class="label">Log in</span></a></li>
			</ul>
		</div>
	</header>
	
	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_12">
				<h1>Get started</h1>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li class="active"><a href="/login">Log in</a></li>
			<li><a href="/markup/user/create.php">Create an account</a></li>
		</ul>
	</nav>

	<div id="content" class="settings cf">
		<div class="center">
			<div class="modal col_9">
			<?php
			if (isset($errors))
			{
				foreach ($errors as $message)
				{
					?>
					<div class="alert-message red">
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
					<div class="alert-message blue">
						<p><strong><?php echo __('Success!'); ?></strong> <?php echo $message; ?></p>
					</div>
					<?php
				}
			}
			?>					
			<?php echo Form::open(); ?>
				<article class="container base">
					<header class="cf">
						<div class="property-title">
							<h1>Enter your account information</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="username">
								<p class="field"><?php echo __('Email'); ?></p>
								<?php echo Form::input("username", ""); ?>
							</label>
						</div>
						<div class="parameter">
							<label for="password">
								<p class="field"><?php echo __('Password'); ?></p>
								<?php echo Form::password("password", ""); ?>
							</label>
						</div>
						<div class="parameter">
							<label for="remember">
								<?php echo Form::checkbox('remember', 1); ?>
								<?php echo __('Remember me'); ?>
							</label>
						</div>
					</section>
				</article>

				<div class="save-toolbar">
					<?php echo Form::hidden('referrer', $referrer); ?>
					<p class="button-blue" onclick="submitForm(this)"><a>Log in</a></p>
				</div>
			<?php echo Form::close(); ?>
			</div>

			<div class="col_3">
				<section class="meta-data">
					<h3 class="arrow"><span class="icon"></span>Forgot your password?</h3>
					<div class="meta-data-content">
					<?php echo Form::open(); ?>
						<label>
							<?php echo __('Your email address') ?>
							<?php echo Form::input("recover_email", ""); ?>
						</label>
						<p class="button-blue button-small" onclick="submitForm(this)"><a><?php echo __('Reset password');?></a></p>
					<?php echo Form::close(); ?>
					</div>
				</section>
			</div>
		</div>
	</div>

<div id="zoom-container">
	<div class="modal-window"></div>
</div>

<div id="modal-container">
	<div class="modal-window"></div>
</div>

</body> 
</html>
