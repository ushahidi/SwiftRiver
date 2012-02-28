<div class="panel-body">
	<div id="settings" class="controls">

		<div class="row cf">
			<h2><?php echo __("Bucket Name"); ?></h2>
			<div class="input">
				<?php echo Form::input('bucket_name', $bucket->bucket_name, 
				    array('id' => 'bucket_name')); ?>
			</div>
			<div class="input">
				<button type="button" class="channel-button" id="rename_bucket">
					<span><?php echo __("Rename the bucket"); ?></span>
				</button>
			</div>
		</div>

		<div class="row cf">
			<h2><?php echo __("Access to the Bucket"); ?></h2>
			<div class="input">
				<p class="checkbox">
					<label>
						<input type="radio" name="bucket_publish" value="1" checked="checked">
						<?php echo __("Public (Anyone)"); ?>
					</label>
				</p>
				<p class="checkbox">
					<label>
						<input type="radio" name="bucket_publish" value="0">
						<?php echo __("Private (Only People I specifiy)"); ?>
					</label>
				</p>
			</div>
		</div>

		<div class="row cf">
			<!-- collaborators -->
			<?php echo $collaborators_control; ?>
			<!-- /collaborators -->
		</div>

		<div class="row controls-buttons cf">
			<section class="actions item">
				<p class="button-delete"><a><?php echo __('Delete Bucket'); ?></a></p>
				<div class="clear"></div>
				<ul class="dropdown">
					<p>Are you sure you want to delete this Bucket?</p>
					<li class="confirm"><a onclick="">Yep.</a></li>
					<li class="cancel"><a onclick="">No, nevermind.</a></li>
				</ul>
			</section>
		</div>
	</div>
</div>

<?php echo $settings_js; ?>