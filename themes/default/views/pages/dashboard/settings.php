<script>
$(document).ready(function() {
	$('.settings-navigation').prependTo('#settings');
	$('.settings-navigation-footer').appendTo('#settings');
});
</script>

<article>
	<div class="center page-title cf">
		<hgroup class="edit user">
			<img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" />
			<h1><span class="edit_trigger" title="dashboard" id="edit_<?php echo $user->id; ?>" onclick=""><?php echo $user->name; ?></span></h1>
		</hgroup>
	</div>

	<div class="center canvas cf">
		<section class="panel">
			<div id="settings" class="controls">	
				<ul class="settings-navigation cf">
					<li><a href="/" class="close"><span class="icon"></span><span class="label">Close account settings</span></a></li>
					<li><a href="/" class="back"><span class="icon"></span><span class="label">Dashboard</span></a></li>
				</ul>

				<?php echo Form::open(); ?>
				<div class="panel_body">
					<div id="messages"></div>
					<div id="settings" class="controls">
						<div class="row cf">
							<div class="input">
								<h3><?php echo __('Current Password'); ?></h3>
								<?php echo Form::password("current_password", "", array('id' => 'current_password')); ?>
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
						<div class="row controls list cf">
							<h2><?php echo __('Collaborators'); ?></h2>
							<div class="input">
								<h3><?php echo __('Add people to share this account'); ?></h3>
								<input type="text" placeholder="+ Type name..." />
							</div>
							<div class="data">
								<h3><?php echo __('People who share this account'); ?></h3>

								<article class="item cf">
									<div class="content">
										<h1><a href="#" class="go">Caleb Bell</a></h1>
									</div>
									<div class="summary">
										<section class="actions">
											<div class="button">
												<p class="button_change"><a class="delete" onclick=""><span class="icon"></span><span class="nodisplay"><?php echo __('Remove'); ?></span></a></p>
												<div class="clear"></div>
												<div class="dropdown container">
													<p><?php echo __('Are you sure you want to stop sharing with this person?'); ?></p>
													<ul>
														<li class="confirm"><a onclick=""><?php echo __('Yep'); ?></a></li>
														<li class="cancel"><a onclick=""><?php echo __('No, nevermind'); ?></a></li>
													</ul>
												</div>
											</div>
										</section>
										<section class="meta">
											<p>Editor</p>
										</section>
									</div>
								</article>
							</div>
						</div>
					</div>
			
					<div class="row controls-buttons cf">
						<p class="button-go"><a href="#"><?php echo __('Apply changes'); ?></a></p>
						<p class="other"><a class="close" onclick=""><?php echo __('Cancel'); ?></a></p>
					</div>
				</div>
				<?php echo Form::close(); ?>

				<div class="settings-navigation-footer"></div>
			</div>
		</section>
	</div>	
</article>