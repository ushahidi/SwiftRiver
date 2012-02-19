<div class="panel-body">
	<div id="messages"></div>
	<div class="loading"></div>
	<div id="settings" class="controls">
		<div class="row cf">
			<div class="input">
				<h3><?php echo __('Current Password'); ?></h3>
				<?php echo Form::password("current_password", "", array('id' => 'current_password')); ?>
			</div>
		</div>
		<div class="row cf">
			<div class="input">
				<h3><?php echo __('Nickname'); ?></h3>
				<?php echo Form::input("nickname", $user->account->account_path, array('id' => 'nickname')); ?>
			</div>
		</div>						    
		<div class="row cf">
			<div class="input">
				<h3><?php echo __('Name'); ?></h3>
				<?php echo Form::input("name", $user->name, array('id' => 'name')); ?>
			</div>
			<div class="input">
				<h3><?php echo __('Email'); ?></h3>
				<?php echo Form::input("email", $user->email, array('id' => 'email')); ?>
			</div>
		</div>
		<div class="row cf">
			<h2><?php echo __('Change password'); ?></h2>
			<div class="input">
				<h3><?php echo __('Password'); ?></h3>
				<?php echo Form::password("password", "", array('id' => 'password')); ?>
			</div>
			<div class="input">
				<h3><?php echo __('Confirm password'); ?></h3>
				<?php echo Form::password("password_confirm", "", array('id' => 'password_confirm')); ?>
			</div>
		</div>
		<div class="row cf">
			<h2><?php echo __('Photo'); ?></h2>
			<div class="input">
				<h3><?php echo __('Current photo'); ?></h3>
				<img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" />
				<p class="button_change"><a href="http://www.gravatar.com" target="_blank"><?php echo __('Upload new photo'); ?></a></p>
			</div>
		</div>
		
		<?php echo $collaborators_control; ?>
		
	</div>

	<div class="row controls-buttons cf">
		<p class="button-go"><a href="#"><?php echo __('Apply changes'); ?></a></p>
		<p class="other"><a class="close" onclick=""><?php echo __('Cancel'); ?></a></p>
	</div>
</div>