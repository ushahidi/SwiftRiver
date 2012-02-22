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
					<li <?php if ($active == 'buckets') echo 'class="active"'; ?>>
						<a><?php echo __('Buckets'); ?></a>
					</li>
				</ul>
			</nav>
		</section>

		<div class="container">
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
			<div class="controls">
				<?php echo Form::open(); ?>

				<div class="row cf">
					<h2><?php echo __("Create a new Bucket"); ?></h2>
					<div class="input new-bucket">
						<?php echo Form::input('bucket_name', $post['bucket_name'], array('placeholder' => __('Name your Bucket'))); ?>
					</div>
					<div class="clear"></div>
					<div class="input">
						<?php echo Form::input('bucket_description', $post['bucket_name'], array('placeholder' => __('Bucket description (optional)'))); ?>
					</div>
				</div>
				<div class="input">
					<div class="controls edit-advanced">
						<div class="row controls-buttons cf">
							<p class="button-go create-new" onclick="submitForm(this)">
								<a><?php echo __("Create Bucket"); ?></a>
							</p>
						</div>
					</div>
				</div>					
			<?php echo Form::close(); ?>
			</div>
		</div>
</article>