<hgroup class="page-title cf">
	<div class="center">
		<div class="col_9">
			<h1><?php echo __("Search results for "); ?><em><?php echo "'".$search_term."'"; ?></em></h1>
		</div>
	</div>
</hgroup>

<div id="content">
	<div class="center body-tabs-container cf">

		<section id="filters" class="col_3">
			<div class="modal-window">
				<div class="modal">		
					<div class="modal-title cf">
						<a href="#" class="modal-close button-white"><i class="icon-cancel"></i><?php echo __("Close"); ?></a>
						<h1><?php echo __("Filters"); ?></h1>
					</div>
						
					<ul class="filters-primary">
						<?php foreach ($navigation_links as $key => $nav): ?>
						<li id="<?php echo $key; ?>-navigation-link" class="<?php echo $active == $key ? 'active' : ''; ?>">
							<a href="<?php echo $nav['link']; ?>"><?php echo $nav['label']; ?></a>
						</li>
					<?php endforeach; ?>
					</ul>
				</div>
			</div>
		</section>
					
		<?php echo $search_results; ?>
	</div>
</div>
