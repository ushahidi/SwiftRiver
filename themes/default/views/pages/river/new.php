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
					<li <?php if ($active == 'main' OR ! $active) echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().'dashboard/';?>"><?php echo __('Activity'); ?></a>
					</li>
					<li <?php if ($active == 'rivers') echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().'dashboard/rivers';?>"><?php echo __('Rivers'); ?></a>
					</li>
					<li <?php if ($active == 'buckets') echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().'dashboard/buckets';?>"><?php echo __('Buckets'); ?></a>
					</li>
					<li <?php if ($active == 'teams') echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().'dashboard/teams';?>"><?php echo __('Teams'); ?></a>
					</li>
				</ul>
				<ul class="actions">
					<li class="view-panel">
						<a href="<?php echo URL::site().'dashboard/settings';?>" class="settings">
							<span class="icon"></span>
							<span class="label"><?php echo __('Account settings'); ?></span>
						</a>
					</li>
				</ul>
			</nav>
		</section>

		<div class="container">
			<div class="controls">
				<?php echo Form::open(); ?>

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
								<input type="radio" name="river_public" value="1" checked="checked">
								<?php echo __("Public (Anyone)"); ?>
							</label>
						</p>
						<p class="checkbox">
							<label>
								<input type="radio" name="river_public" value="0">
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
		
			<?php
			if (isset($errors))
			{
				foreach ($errors as $message)
				{
					?>
					<div class="system_message system_error">
						<p><strong><?php echo __('Uh oh.'); ?></strong> <?php echo $message; ?></p>
					</div>
					<?php
				}
			}
			?>
			<?php if ( ! $is_new_river) echo $settings_control; ?>

			<?php echo Form::close(); ?>
			</div>
		</div>
</article>