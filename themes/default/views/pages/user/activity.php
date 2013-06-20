<div id="filters-trigger" class="col_12 cf">
	<a href="#" class="button button-primary filters-trigger"><i class="icon-filter"></i>Filters</a>
</div>

<section id="filters" class="col_3">
	<div class="modal-window">
		<div class="modal">		
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1>Filters</h1>
			</div>

			<ul class="filters-primary">
				<li class="active"><a href="#" class="modal-close"><?php echo __("Everything"); ?></a></li>
				<li><a href="#" class="modal-close"><?php echo __("Pending"); ?></a></li>
			</ul>
		</div>
	</div>
</section>

<div class="col_9">
	<?php if ($owner): ?>
		<?php echo $no_activity_view; ?>
	<?php endif; ?>
	<div id="news-feed" class="container base"></div>
</div>

<?php echo $activity_stream; ?>
