<hgroup class="page-title cf">
	<div class="center">
		<div class="col_9">
			<h1><?php echo $bucket['name']; ?> <em><?php echo __("settings"); ?></em></h1>
		</div>
		<div class="page-action col_3">
			<a class="button button-white" href="<?php echo $bucket_base_url; ?>"><?php echo __('Return to bucket'); ?></a>
		</div>			
	</div>
</hgroup>

<div id="content" class="river drops cf">
	<div class="center body-tabs-container">
		<section id-"filters" class="col_3">
			<div class="modal-window">
				<div class="modal">
					<ul class="filters-primary">
						<?php foreach ($nav as $item): ?>
						<li id="<?php echo $item['id']; ?>" class="<?php echo $item['active'] == $active ? 'active' : ''; ?>">
							<a href="<?php echo $bucket_base_url.$item['url']; ?>">
								<?php echo $item['label'];?>
							</a>
						</li>
						<?php endforeach; ?>
				</div>
			</div>
		</section>
		
		<div id="settings" class="body-tabs-window col_9">
			<?php echo $settings_content; ?>
		</div>
	</div>
</div>

<!-- System messages -->
<?php if (isset($message) OR isset($error)): ?>
	<script type="text/javascript">
	<?php if (isset($message)): ?>
		showSuccessMessage('<?php echo $message; ?>', {flash: true});
	<?php elseif (isset($error)): ?>
		showFailureMessage('<?php echo $error; ?>');
	<?php endif; ?>
	</script>
<?php endif; ?>