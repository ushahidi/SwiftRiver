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

<div id="content" class="river drops cf">
	<div class="center">

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
					<div class="filters-type">
						<ul>
							<li><a href="#"><span class="total">39</span> Unread</a></li>
							<li><a href="#"><span class="total">165</span> Read</a></li>
						</ul>
					</div>
				
					<div class="filters-type">
						<span class="toggle-filters-display"><span class="total">5</span><span class="icon-arrow-down"></span><span class="icon-arrow-up"></span></span>				
						<span class="filters-type-settings"><a href="/markup/_modals/settings-channels.php" class="modal-trigger"><span class="icon-cog"></span></a></span>
						<h2>Channels</h2>
						<div class="filters-type-details">
							<ul>
								<li class="active"><a href="#"><i class="icon-twitter"></i><span class="total">28</span> @Mainamshy, @rkulei...</a></li>
								<li class="active"><a href="#"><i class="icon-facebook"></i><span class="total">61</span> DailyNation, KTNKenya</a></li>
								<li class="active"><a href="#"><i class="icon-rss"></i><span class="total">83</span> The Kenyan Post</a></li>
								<li class="active"><a href="#"><i class="icon-rss"></i><span class="total">14</span> African Press</a></li>
								<li class="active"><a href="#"><i class="icon-rss"></i><span class="total">19</span> Standard Media</a></li>
							</ul>
						</div>
					</div>
							
					<div class="filters-type">
						<ul>
							<li class="active"><a href="#"><span class="remove icon-cancel"></span><i class="icon-calendar"></i>November 1, 2012 to present</a></li>
							<!--li class=""><a href="#"><span class="remove icon-cancel"></span><i class="icon-pencil"></i>hate, robbed</a></li-->
						</ul>
						<a href="/markup/_modals/add-search-filter.php" class="button-add modal-trigger"><i class="icon-search"></i>Add search filter</a>				
					</div>

					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-close">Done</a>				
					</div>
				</div>
			</div>
		</section>
			
		<?php echo $droplets_view; ?>
	</div>
</div>
