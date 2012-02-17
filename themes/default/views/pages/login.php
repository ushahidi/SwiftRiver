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
	<style>
.col_6 {
   padding: 0 1.041666667%;
}

article#login .body {
   padding: 2.083333333% 1.041666667%;
}

article#login .canvas {
   -moz-box-shadow: 0 2px 3px rgba(0,0,0,.07);
   -webkit-box-shadow: 0 2px 3px rgba(0,0,0,.07);
   box-shadow: 0 2px 3px rgba(0,0,0,.07);
   -webkit-border-radius: 3px;
   -moz-border-radius: 3px;
   border-radius: 3px;
   margin: 20px auto;
}

article#login hgroup.page-title {
   border-bottom: 1px solid #f0f0f0;
   background: #f9f9f9;
   padding: 15px 2.083333333%;
}

article#login hgroup.page-title h1 {
   color: #555555;
   font: bold 2.4em/1em Helvetica Neue, Helvetica, Arial, sans-serif;
   text-shadow: 0 1px 1px #fff;
}

article#login .form .field {
   color: #666666;
   padding: 2.083333333% 0;
}

article#login .form .field label {
   display: inline-block;
   width: 30.43478261%;
   font-weight: bold;
}

article#login .form .field input {
   box-shadow: 0 1px 1px rgba(0,0,0,.4) inset;
   -moz-box-shadow: 0 1px 1px rgba(0,0,0,.15) inset;
   -webkit-box-shadow: 0 1px 1px rgba(256,256,256,.4) inset;
   -webkit-border-radius: 3px;
   -moz-border-radius: 3px;
   border-radius: 3px;
   border: 1px solid #e0e0e0;
}

article#login .form .field .dropdown .buttons {
   float: none;
}

/* --- ####### MEDIUM VIEWPORT ###### --- */
@media screen and (min-width: 615px) {
.vr {
   box-shadow: -1px 0 0 rgba(0,0,0,.1) inset;
   -moz-box-shadow: -1px 0 0 rgba(0,0,0,.1) inset;
   -webkit-box-shadow: -1px 0 0 rgba(0,0,0,.1) inset;
}

.col_6 {
   float: left;
   width: 47.91666667%;
   padding: 0 1.041666667%;
}

article#login hgroup.page-title {
   padding: 2.083333333%;
}

}
	</style>
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
				<h1 class="logo"><a href="<?php echo url::site() ?>"><span class="nodisplay">SwiftRiver</span></a></h1>
			</hgroup>
		</div>
		<div class="right_bar"></div>
	</header>
	
	<article id="login">
		<div class="center canvas controls">
			<hgroup class="page-title cf">
				<h1>Log in</h1>
			</hgroup>
			<div class="body cf">
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
				<div class="form vr col_6">
					<div class="field cf">						
						<?php if ($riverid_auth): ?>
							<label><?php echo __('RiverID'); ?></label>
						<?php else: ?>
							<label><?php echo __('Email'); ?></label>
						<?Php endif; ?>
						<?php echo Form::input("username", ""); ?>
					</div>
					<div class="field cf">
						<label><?php echo __('Password'); ?></label>
						<?php echo Form::password("password", ""); ?>
					</div>
					<div class="field cf">
						<?php echo Form::checkbox('remember', 1); ?>
						<strong><?php echo __('Remember me'); ?></strong>
					</div>				
					<div class="submit controls-buttons cf">
						<p class="button-go" onclick="submitForm(this)"><a>Get started</a></p>
						<!--p class="other"><a href="#"><span></span>Forgot your password?</a></p-->
					</div>
				</div>
			<?php echo Form::close(); ?>

				<div class="form col_6">
			    <?php echo Form::open(); ?>
					<div class="field has_dropdown cf">
						<?php if ($riverid_auth): ?>
							<a><?php echo __('Create a RiverID'); ?></a>
						<?php else: ?>
							<a><?php echo __('Create an account'); ?></a>
						<?Php endif; ?>
						<div class="dropdown" style="display:none;">
							<label><?php echo __('Your email address') ?></label>
							<?php echo Form::input("new_email", ""); ?>
							<div class="buttons" onclick="submitForm(this)"><button class="save"><?php echo __('Register');?></button></div>
						</div>
					</div>
				<?php echo Form::close(); ?>

				<?php echo Form::open(); ?>
					<div class="field has_dropdown cf">						
						<?php if ($riverid_auth): ?>
							<a><?php echo __('Forgot your RiverID password?'); ?></a>
						<?php else: ?>
							<a><?php echo __('Forgot your password?'); ?></a>
						<?Php endif; ?>						
						<div class="dropdown" style="display:none;">
							<label><?php echo __('Your email address') ?></label>
							<?php echo Form::input("recover_email", ""); ?>
							<div class="buttons" onclick="submitForm(this)"><button class="save"><?php echo __('Reset password');?></button></div>
						</div>
					</div>
				<?php echo Form::close(); ?>
				</div>
			</div>
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
