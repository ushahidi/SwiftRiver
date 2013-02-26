<!-- TAB: Options -->
<div id="options" class="active">
	<?php echo Form::open(); ?>
	<article class="base settings-category">
		<h1><?php echo __("General"); ?></h1>
		<div class="body-field">
			<h3 class="label"><?php echo __("Bucket name"); ?></h3>
			<?php echo Form::input("bucket_name", $bucket['name']); ?>
		</div>
		<div class="body-field">
			<h3 class="label"><?php echo __("Bucket URL"); ?></h3>
			<?php echo Form::input('bucket_url', URL::site($bucket['url'], TRUE), array('readonly' =>' readonly')); ?>
		</div>
		<div class="settings-category-toolbar" id="update-bucket-name">
			<a href="#" class="button-submit button-primary modal-close" onClick="submitForm(this);"><?php echo __("Change Bucket Name"); ?></a>
		</div>
	</article>
	<?php echo Form::close(); ?>

	<?php echo Form::open(); ?>
	<article class="base settings-category">
		<h1><?php echo __("Display"); ?></h1>
		<div class="body-field">
			<h3 class="label"><?php echo __("Default view"); ?></h3>
			<select name="default_layout">
				<option value="drops" <?php echo $bucket['default_layout'] == 'drops' ? "selected": ''; ?>>Drops</option>
				<option value="list" <?php echo $bucket['default_layout'] == 'list' ? "selected": ''; ?>>List</option>
				<option value="photos" <?php echo $bucket['default_layout'] == 'photos' ? "selected" : ''; ?>>Photos</option>
			</select>
		</div>
		<div class="body-field">
			<h3 class="label"><?php echo __("Who can view this bucket") ?></h3>
			<select name="bucket_publish">
				<option value="1" <?php echo $bucket['public'] ? 'selected' : ''; ?>>Public (Anyone)</option>
				<option value="0" <?php echo $bucket['public'] ? '' : 'selected'; ?>>Private (Collaborators only)</option>
			</select>
		</div>
		<div class="settings-category-toolbar" id="update-bucket-display">
			<a href="#" class="button-submit button-primary modal-close" onClick="submitForm(this);"><?php echo __("Update Display Settings"); ?></a>
		</div>
	</article>
	<?php echo Form::close(); ?>
</div>