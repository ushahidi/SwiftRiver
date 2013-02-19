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
					<ul class="body-tabs-menu filters-primary">
						<li class="active"><a href="#options" class="modal-close"><?php echo __("Options"); ?></a></li>
						<li><a href="#collaborators" class="modal-close"><?php echo __("Collaborators"); ?></a></li>
					</ul>
				</div>
			</div>
		</section>
		
		<div id="settings" class="body-tabs-window col_9">
			<?php echo $settings_content; ?>
		</div>
	</div>
</div>
