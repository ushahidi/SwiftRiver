<?php
/**
 * Login Template
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="content-language" content="en" />
	<meta name="robots" content="noindex,nofollow" />
	<link rel="stylesheet" media="screen,projection" type="text/css" href="themes/default/media/css/reset.css" />
	<link rel="stylesheet" media="screen,projection" type="text/css" href="themes/default/media/css/main.css" />
	<!--[if lte IE 6]><link rel="stylesheet" media="screen,projection" type="text/css" href="themes/default/media/css/main-ie6.css" /><![endif]-->
	<link rel="stylesheet" media="screen,projection" type="text/css" href="themes/default/media/css/style.css" />
	<script type="text/javascript" src="themes/default/media/js/toggle.js"></script>
	<title>Sweeper - Log In</title>
</head>

<body id="login">

<div id="main-02">

	<div id="login-top"></div>

	<div id="login-box">

		<!-- Logo -->
		<p class="nom t-center"><a href="#"><img src="themes/default/media/img/logo.png" alt="Sweeper" title="Sweeper" /></a></p>

		<!-- Messages -->
		<p class="msg info"><?php echo __('Please enter your Username and Password to log in'); ?></p>
		
		<?php
		if (isset($errors))
		{
			foreach ($errors as $message)
			{
				?><p class="msg error"><?php echo $message;?></p><?php
			}
		}
		?>

		<!-- Form -->
		<?php echo Form::open(); ?>
			<table class="nom nostyle">
				<tr>
					<td style="width:75px;"><label for="username"><strong><?php echo __('Username');?>:</strong></label></td>
					<td><?php echo Form::input("username", "", array("size" => 45, "class" => "input-text")); ?></td>
				</tr>
				<tr>
					<td><label for="password"><strong><?php echo __('Password');?>:</strong></label></td>
					<td><?php echo Form::password("password", "", array("size" => 45, "class" => "input-text")); ?></td>
				</tr>
				<tr>
					<td></td>
					<td>
						<span class="f-right"><a href="javascript:toggle('sendpass');"><?php echo __('Forgot Password');?>?</a></span>
						<span class="f-left low"><?php echo Form::checkbox('remember', 'remember', TRUE); ?> <label for="login-remember"><?php echo __('Remember Me');?></label></span>
					</td>
				</tr>
				<!-- Show/Hide -->
				<tr id="sendpass" style="display:none;">
					<td><label for="login-sendpass"><strong><?php echo __('E-Mail');?>:</strong></label></td>
					<td>
						<input type="text" size="35" name="" class="input-text f-left" id="login-sendpass" />
						<span class="f-right"><input type="submit" value="Send" /></span>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="t-right"><input type="submit" class="input-submit" value="<?php echo __('Submit');?> &raquo;" />&nbsp;&nbsp;<?php echo __('or');?>&nbsp;&nbsp;<input type="submit" class="input-submit" value="<?php echo __('Sign In With RiverID');?> &raquo;" /></td>
				</tr>
			</table>
		<?php echo Form::close(); ?>

	</div> <!-- /login-box -->

	<div id="login-bottom"></div>

</div> <!-- /main -->

</body>
</html>