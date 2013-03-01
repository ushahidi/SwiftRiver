<hgroup class="page-title cf">
	<div class="center">
		<div class="col_9">
			<h1><a href="<?php echo $river_base_url?>"><?php echo $river['name']; ?></a> <em>settings</em></h1>
		</div>
		<div class="page-action col_3">
			<a href="<?php echo $river_base_url?>" class="button button-white">Return to river</a>
			<a href="#" class="button button-primary filters-trigger"><i class="icon-menu"></i>More</a>
		</div>
	</div>
</hgroup>

<div id="content">
	<div class="center body-tabs-container cf">

		<section id="filters" class="col_3">
			<div class="modal-window">
				<div class="modal">		
					<div class="modal-title cf">
						<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
						<h1>Filters</h1>
					</div>
						
					<ul class="filters-primary">
						<?php foreach ($nav as $item): ?>
						<li id="<?php echo $item['id']; ?>" class="<?php echo $item['active'] == $active ? 'active' : ''; ?>">
							<a href="<?php echo $river_base_url.$item['url']; ?>">
								<?php echo $item['label'];?>
							</a>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>															
		</section>
					
		<div id="settings" class="body-tabs-window col_9">
			
		<?php echo $settings_content ?>			
								
		</div>
	</div>
</div>
