<hgroup class="page-title cf">
	<div class="center">
		<div class="col_9">
			<h1>
				<?php $bucket_name = $bucket['name']; ?>
				<?php if ($bucket['is_owner']): ?>
					<span><?php echo $bucket_name; ?></span>
				<?php else: ?>
					<a href="<?php echo URL::site().$bucket['account']['account_path'] ?>">
							<?php echo $bucket['account']['account_path']; ?>
					</a>
					<span><?php echo "/".$bucket_name; ?></span>
				<?php endif; ?>
			</h1>		
		</div>
		<div class="page-action col_3">
			<?php if ($bucket['is_owner']): ?>
			<a href="<?php echo $discussion_url; ?>" class="button button-white settings"><span class="icon-comment"></span></a>
			<a href="<?php echo $settings_url; ?>" class="button button-white settings"><span class="icon-cog"></span></a>
			<?php endif; ?>
			<?php if (isset($follow_button)): ?>
			<span class="button-follow" id="follow-button">
				<?php echo $follow_button; ?>
			</span>
			<?php endif; ?>
		</div>		
	</div>
</hgroup>

<div id="content" class="river cf">
	<div class="center">
		<section id="filters" class="col_3">
			<div class="modal-window">
				<div class="modal">
					<ul class="filters-primary">
						<li id="drops-navigation-link" class="active">
							<a href="drops">
								<span class="total" style="display: none;"><?php echo $drop_count; ?></span>
								<?php echo __("Drops"); ?>
							</a>
						</li>
						<li id="list-navigation-link">
							<a href="list">
								<span class="total" style="display: none;"><?php echo $drop_count; ?></span>
								<?php echo __("List"); ?>
							</a>
						</li>		
						<li id="photos-navigation-link">
							<a href="photos">
								<span class="total" style="display: none;"><?php echo $photos_drop_count; ?></span>
								<?php echo __("Photos"); ?>
							</a>
						</li>
					</ul>

					<div class="filters-type" id="drop-search-filter">
						<ul id="search-filter-list">
						</ul>
						<a href="#" class="button-add modal-trigger">
							<i class="icon-search"></i>
							<?php echo __("Add search filter"); ?>
						</a>
					</div>

				</div>
			</div>
		</section>
		<?php echo $droplets_view; ?>
	</div>
</div>