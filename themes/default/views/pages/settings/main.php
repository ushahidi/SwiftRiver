<?php echo Form::open(); ?>
	<div id="settings">
		<div id="messages"></div>

		<div class="controls">
			<div class="row cf">
				<div class="input">
					<h3><?php echo __('Site Name'); ?></h3>
					<?php echo Form::input("site_name", ''); ?>
				</div>
			</div>
			<div class="row cf">
				<div class="input">
					<h3><?php echo __('Site Locale'); ?></h3>
					<?php echo Form::password("site_locale", ''); ?>
				</div>
			</div>

		<div class="row controls_buttons cf">
			<p class="button_go"><a href="#"><?php echo __('Apply changes'); ?></a></p>
			<p class="other"><a class="close" onclick=""><?php echo __('Cancel'); ?></a></p>
		</div>
	</div>
<?php echo Form::close(); ?>