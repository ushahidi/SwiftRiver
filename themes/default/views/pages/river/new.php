<article>
	<?php echo Form::open(); ?>
		<div class="cf center page_title">
			<hgroup class="edit">
			<h1><span class="edit_input"><?php echo Form::input('river_name', $post['river_name'], array('placeholder' => __('Name your River'))); ?></span></h1>
			</hgroup>
		</div>
		
		<div class="center canvas">
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
			<section class="panel">
				<div class="panel_body"><?php echo $settings_control; ?></div>
			</section>
		</div>
	<?php echo Form::close(); ?>
</article>