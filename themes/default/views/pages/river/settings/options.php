<div id="options" class="active">
	<article class="base settings-category">
		<h1>Basics</h1>
		<?php echo Form::open(); ?>
		<div class="body-field">
			<h3 class="label">River name</h3>
			<input type="text" value="<?php echo $river['name']; ?>" name="river_name" />
		</div>
		<div class="body-field">
			<h3 class="label">Description</h3>
			<input type="text" value="<?php echo $river['description']; ?>" name="river_description" />
		</div>
		<div class="body-field">
			<h3 class="label">Who can view this river</h3>
			<select name="river_public">
				<option value="1" <?php if ($river['public']) echo 'selected'; ?>>Public</option>								
				<option value="0" <?php if ( ! $river['public']) echo 'selected'; ?>>Only collaborators</option>
			</select>
		</div>
		<div class="settings-category-toolbar">
			<a class="button-submit button-primary modal-close" href="#" onclick="submitForm(this); return false;">Save</a>
		</div>
		<?php echo Form::close(); ?>
	</article>																			
</div>