<?php echo Form::open(); ?>
	<div id="account_settings">
		<div class="controls">
			<div class="row cf">
				<div class="input">
					<h3><?php echo __('Username'); ?></h3>
					<?php echo Form::input("username", $user->username); ?>
				</div>
				<div class="input">
					<h3><?php echo __('Email'); ?></h3>
					<?php echo Form::input("username", $user->email); ?>
				</div>
			</div>
			<div class="row cf">
				<h2><?php echo __('Change password'); ?></h2>
				<div class="input">
					<h3><?php echo __('Current password'); ?></h3>
					<?php echo Form::password("current_password", ""); ?>
				</div>
				<div class="input">
					<h3><?php echo __('New password'); ?></h3>
					<?php echo Form::password("password", ""); ?>
				</div>
			</div>
			<div class="row cf">
				<h2><?php echo __('Photo'); ?></h2>
				<div class="input">
					<h3><?php echo __('Current photo'); ?></h3>
					<img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" />
					<p class="button_change"><a><?php echo __('Upload new photo'); ?></a></p>
				</div>
			</div>
			<div class="row controls cf">
				<h2><?php echo __('Collaborators'); ?></h2>
				<div class="input">
					<h3><?php echo __('Add people to share this account'); ?></h3>
					<input type="text" placeholder="+ Type name..." />
				</div>
				<div class="list_stream">
					<h3><?php echo __('People who share this account'); ?></h3>
					<ul class="users">
						<li>
							<a href="#">Caleb Bell</a>
							<div class="actions">
								<span class="button_delete"><a onclick=""><?php echo __('Remove'); ?></a></span>
								<ul class="dropdown right">
									<p><?php echo __('Are you sure you want to stop sharing with this person?'); ?></p>
									<li class="confirm"><a onclick=""><?php echo __('Yep'); ?></a></li>
									<li class="cancel"><a onclick=""><?php echo __('No, nevermind'); ?></a></li>
								</ul>
							</div>
						</li>
						<li>
							<a href="#">David Kobia</a>
							<div class="actions">
								<span class="button_delete"><a onclick=""><?php echo __('Remove'); ?></a></span>
								<ul class="dropdown right">
									<p><?php echo __('Are you sure you want to stop sharing with this person?'); ?></p>
									<li class="confirm"><a onclick=""><?php echo __('Yep'); ?></a></li>
									<li class="cancel"><a onclick=""><?php echo __('No, nevermind'); ?></a></li>
								</ul>
							</div>
						</li>
					</ul>
				</div>
		</div>

		<div class="row controls_buttons cf">
			<p class="button_go"><a href="#"><?php echo __('Apply changes'); ?></a></p>
			<p class="other"><a class="close" onclick=""><?php echo __('Cancel'); ?></a></p>
		</div>
	</div>
<?php echo Form::close(); ?>