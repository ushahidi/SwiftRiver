	<div id="content" class="settings cf">
		<div class="center">
			<div class="col_12">
				<?php if (isset($errors)): ?>
				<?php foreach ($errors as $message): ?>
				<div class="alert-message red">
					<p><strong>Uh oh.</strong> <?php echo $message; ?></p>
				</div>
				<?php endforeach; ?>
				<?php endif; ?>

				<article class="container base">
					<header class="cf">
						<div class="property-title col_12">
							<h1><?php echo __('What\'s your river about'); ?> <em><?php echo __('and who can see it?'); ?></em></h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<div class="field">
								<input type="text" placeholder="Name your river" value="" name="river_name" />
							</div>
							<div class="field">
								<p class="field-label"><?php echo __('Who can see it'); ?></p>
								<select name="river_public">
									<option value="1"><?php echo __('Public (Anyone)'); ?></option>
									<option value="0"><?php echo __('Private (Collaborators only)'); ?></option>
								</select>						
							</div>
							<div class="save-toolbar">
								<p class="button-blue" onclick="submitForm(this)"><a>Next</a></p>
							</div>							
						</div>												
					</section>
				</article>
			</div>
		</div>
	</div>