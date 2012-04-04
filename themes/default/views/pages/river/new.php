<article class="<?php echo $template_type; ?>">
	<div class="center page-title cf">
		<hgroup class="edit user">
			<img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" />
			<h1><span class="edit-trigger" title="dashboard" id="edit_<?php echo $user->id; ?>" onclick=""><?php echo $user->name; ?></span></h1>
		</hgroup>
	</div>


	<div class="center canvas cf">
		<section class="panel">		
			<nav class="cf">
				<ul class="views">
					<li <?php if ($active == 'rivers') echo 'class="active"'; ?>>
						<a><?php echo __('Rivers'); ?></a>
					</li>
				</ul>
			</nav>
		</section>

		<div class="container">
			<div class="controls">
				<?php if (isset($errors)): ?>
				<?php foreach ($errors as $message): ?>

						<div class="system_message system_error">
							<p><strong><?php echo __('Uh oh.'); ?></strong> <?php echo $message; ?></p>
						</div>
				
				<?php endforeach; ?>
				<?php endif; ?>

				<?php echo Form::open(); ?>
				<?php echo Form::input('form_auth_id', CSRF::token(), array('type' => 'hidden')); ?>
				<div class="row cf">
					<h2><?php echo __("Create a new River"); ?></h2>
					<div class="input new-river">
						<?php echo Form::input('river_name', $post['river_name'], array('placeholder' => __('Name your River'))); ?>
					</div>
				</div>

				<div class="row cf">
					<h2><?php echo __("Access to the River"); ?></h2>
					<div class="input">
						<p class="checkbox">
							<label>
								<?php 
									$public = (! isset($post) OR (isset($post) AND $post["river_public"] == 1))
									   ? "checked=\"checked\""
									   : ""
								?>
								<input type="radio" name="river_public" value="1" <?php echo $public; ?>>
								<?php echo __("Public (Anyone)"); ?>
							</label>
						</p>
						<p class="checkbox">
							<label>
								<?php 
									$private = (isset($post) AND $post['river_public'] == 0)
										? "checked=\"checked\"" 
										: "" ;
								?>
								<input type="radio" name="river_public" value="0" <?php echo $private; ?>>
								<?php echo __("Private (Only People I specifiy)"); ?>
							</label>
						</p>
					</div>
				</div>

				<?php if ($is_new_river): ?>
				<div class="form-buttons">
					<p class="button-go create-new" onclick="submitForm(this)">
						<a><?php echo __("Create River"); ?></a>
					</p>
				</div>
				<?php endif; ?>
		
				<?php if ( ! $is_new_river) echo $settings_control; ?>

			<?php echo Form::close(); ?>
			</div>
		</div>
</article>