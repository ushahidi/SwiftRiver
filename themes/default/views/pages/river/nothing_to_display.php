<div class="center no-content" style="display:none;">
	<div class="col_12">
		<article class="container base">
			<div class="alert-message blue">
				<p>
					<strong><?php echo __('Nothing to display yet.') ?></strong>
					<?php echo __('The river will start flowing as soon as there is content '
					    .'and this page will update automatically or you can refresh manually',
					    array('refresh manually' => HTML::anchor($river_url, __('refresh manually'))));
					?>
				</p>
				<p>
					<?php echo __('Taking too long? Save this link and come back later') ?>
					<?php echo HTML::anchor($river_url, $river_url); ?>
				</p>
				<?php if ($anonymous): ?>
					<p>
						<strong><?php echo __('Already have an account?') ?></strong> 
						<a href="<?php echo URL::site('login') ?>" class="previous">
							<?php echo __('Log in here') ?>
						</a>.
					</p>
				<?php endif; ?>
			</div>
		</article>
			
		<?php if ($anonymous): ?>
			<h3 class="push-up"><span><?php echo __('In the mean time...') ?></h3>
			
			<?php if ((bool) Swiftriver::get_setting('public_registration_enabled')): ?>
			<div class="panel-left">
				<div class="login" id="nothing_to_display_login_form">
					<div class="loading center"></div>
					<div class="system_error" style="display:none"></div>
					<div class="system_success" style="display:none"></div>
					<div class="form">
						<h3><?php echo __('Create An Account') ?></h3>
						<p>
							<strong><label><?php echo __('Your email address') ?></label></strong>
							<?php echo Form::input("new_email", ""); ?>
							<div class="buttons"><button class="save"><?php echo __('Create My Account!');?></button></div>
						</p>
					</div>							
					<div class="help-text highlight-box">
						<h3><?php echo __('How much does this cost?') ?></h3>
						<p><?php echo __('SwiftRiver is free while we are in beta.') ?></p>
					</div>
					<p class="or"><?php echo __('Or') ?></p>
				</div>
			</div>
			<?php endif; ?>
				
			<div class="panel-right help-text">
				<h3><?php echo __('Learn More About SwiftRiver') ?></h3>
				<p><?php echo __('SwiftRiver is made up of Drops, Buckets and Rivers.') ?></p>
				
				<h4><?php echo __('What is a Drop?') ?></h4>
				<p><?php echo __('A tweet, Facebook update, blog post, SMS... the basic unit of content inside of SwiftRiver.') ?></p>
        	
				<h4><?php echo __('What is a Bucket?') ?></h4>
				<p><?php echo __('A group of hand-picked drops that are meaningful to you.') ?></p>
        	
				<h4><?php echo __('What is a River?') ?></h4>
				<p><?php echo __('The torrent of drops that come from your predefined channels.') ?></p>
				<p><strong><a href="#"><?php echo __('Learn More') ?> &raquo;</a></p>
				
			</div>
		<?php endif; ?>
	</div>
</div>