<div id="content" class="settings cf">
	<div class="center">
		<div class="col_12">
			<?php if (isset($errors)): ?>
				<div class="alert-message red">
				<?php foreach ($errors as $message): ?>
					<p><strong>Uh oh.</strong> <?php echo $message; ?></p>
				<?php endforeach; ?>
				</div>
			<?php endif; ?>
			
			<?php if (isset($messages)): ?>
				<div class="alert-message blue">
				<?php foreach ($messages as $message): ?>
					<p><strong>Success</strong> <?php echo $message; ?></p>
				<?php endforeach; ?>
				</div>
			<?php endif; ?>
			
			<?php echo Form::open(); ?>
			<article class="container base">
				<header class="cf">
					<div class="property-title col_12">
						<h1>Name</h1>
					</div>
				</header>
				<section class="property-parameters">
					<div class="parameter">
						<div class="field">
							<p class="field-label">Display name</p>
							<input type="text" value="<?php echo $river->river_name ?>" name="river_name" />
						</div>
					</div>
				</section>
			</article>

			<article class="container base">
				<header class="cf">
					<div class="property-title col_12">
						<h1>Default view</h1>
					</div>
				</header>
				<section class="property-parameters">
					<div class="parameter">
						<select name="default_layout">
							<option value="list" <?php echo ($river->default_layout == "list") ? 'selected' : ''; ?>>List</option>
							<option value="drops" <?php echo ($river->default_layout == "drops") ? 'selected' : ''; ?>>Drops</option>
							<option value="photos" <?php echo ($river->default_layout == "photos") ? 'selected' : ''; ?>>Photos</option>
						</select>
					</div>
				</section>
			</article>
			
			<article class="container base">
				<header class="cf">
					<div class="property-title col_12">
						<h1>Who can view this river</h1>
					</div>
				</header>
				<section class="property-parameters">
					<div class="parameter">
						<select name="river_public">
							<option value="1" <?php echo $river->river_public ? 'selected' : ''; ?>>Public (Anyone)</option>
							<option value="0" <?php echo $river->river_public ? '' : 'selected'; ?>>Private (Collaborators only)</option>
						</select>
					</div>
				</section>
			</article>
			
			<div class="save-toolbar">
				<p class="button-blue"><a href="#" onclick="if ($(this).parents('.save-toolbar').hasClass('visible')) submitForm(this); return false;">Save changes</a></p>
				<p class="button-blank"><a href="#">Cancel</a></p>
			</div>
			<?php echo Form::close(); ?>
		</div>
	</div>
</div>
