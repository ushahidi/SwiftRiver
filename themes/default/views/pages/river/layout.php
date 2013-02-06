<hgroup class="page-title cf">
	<div class="center">
		<div class="col_9">
			<h1><?php print $page_title; ?></h1>
		</div>
		<div class="page-action col_3">
			<!-- IF: User manages this river -->
			<a href="settings.php" class="button button-white settings"><span class="icon-cog"></span></a>
			<a href="#" class="button button-primary filters-trigger"><i class="icon-filter"></i>Filters</a>
			<!-- ELSE IF: User follows this river
			<a href="#" class="button-follow selected button-primary"><i class="icon-checkmark"></i>Following</a>
			! ELSE
			<a href="#" class="button-follow button-primary"><i class="icon-checkmark"></i>Follow</a>
			-->				
		</div>
	</div>
</hgroup>

<?php echo $droplets_view; ?>