<div id="stream" class="col_9">
</div>

<script type="text/template" id="drop-listing-template">
<div id="drops-view"></div>
</script>

<script type="text/template" id="drop-drops-view-template">
	<% if (image != null) { %>
		<a href="/markup/drop" class="drop-image-wrap zoom-trigger"><img src="<%= image.thumbnails[0].url %>" class="drop-image" /></a>
	<% } %>
	<h1>
		<a href="#" class="zoom-trigger"><%= title %></a>
	</h1>
	<div class="drop-actions cf">
		<ul class="dual-buttons drop-move">
			<li class="share">
				<a href="#" class="button-primary modal-trigger"><span class="icon-share"></span></a>
			</li>
			<li class="bucket">
				<a href="#" class="button-primary modal-trigger">
					<span class="icon-add-to-bucket"></span>
					<% if (buckets != null && buckets.length > 0) { %>
					<span class="bucket-total"><%= buckets.length %></a>
					<% } %>
				</a>
			</li>	
		</ul>
		<span class="drop-score"><a href="#" class="button-white"><span class="icon-star"></span></a>
		<ul class="drop-status cf">
			<li class="drop-status-read">
				<a href="#"><span class="icon-checkmark"></span></a>
			</li>
			<li class="drop-status-remove">
				<a href="#"><span class="icon-cancel"></span></a>
			</li>
		</ul>
	</div>
	<section class="drop-source cf">
		<a href="#" class="avatar-wrap"><img src="<%= source.avatar %>" /></a>
		<div class="byline">
			<h2><%= source.name %></h2>
			<p class="drop-source-channel">
				<a href="#">
					<span class="icon-<%= channel %>"></span>
					<?php echo __("via "); ?> <%= channel %>
				</a>
			</p>
		</div>
	</section>
</script>

<script type="text/template" id="drop-list-view-template">
	<section class="drop-source cf">
		<a href="#" class="avatar-wrap"><img src="<%= source.avatar %>" /></a>
		<div class="byline">
			<h2><%= source.name %></h2>
			<p class="drop-source-channel">
				<a href="#">
					<span class="icon-<%= channel %>"></span>
					<?php echo __("via"); ?> <%= channel %>
				</a>
			</p>
		</div>
	</section>
	<div class="drop-body">
		<div class="drop-content">
			<h1><a href="#" class="zoom-trigger"><%= title %></a>
		</div>
		<div class="drop-details">
			<p class="metadata">
				<%= date_published %>
				<a href="#">
					<i class="icon-comment"></i>
					<strong><%= comment_count %></strong> <?php echo __("comments"); ?>
				</a>
			</p>
			<div class="drop-actions cf">
				<ul class="dual-buttons drop-move">
					<li class="share">
						<a href="#" class="button-primary modal-trigger"><span class="icon-share"></span></a>
					</li>
					<li class="bucket">
						<a href="#" class="button-primary modal-trigger">
							<span class="icon-add-to-bucket"></span>
							<% if (buckets != null && buckets.length > 0) { %>
								<span class="bucket-total"><%= buckets.length %></span>
							<% } %>
						</a>
					</li>
				</ul>
				<span class="drop-score">
					<a href="#" class="button-white">
						<span class="icon-star"></span>
					</a>
				</span>
				<ul class="drop-status cf">
					<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
					<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a><li>
				</ul>
			</div>
		</div>
	</div>
</script>

<script type="text/template" id="metadata-template">
	<span class="toggle-filters-display">
		<span class="total"><%= metadata_count %></span>
		<span class="icon-arrow-down"></span>
		<span class="icon-arrow-up"></span>	
	</span>
	<span class="filters-type-settings">
		<a href="#" class="modal-trigger">
			<span class="icon-cog"></span>
		</a>
	</span>
	<h2><%= metadata_type %></h2>
	<div class="filters-type-details">
		<ul></ul>
	</div>
</script>

<script type="text/template" id="metadata-item-template">
	<a href="#"><%= metadata_item %></a>
</script>

<script type="text/template" id="drop-detail-template">
	<div class="center cf">
		<div class="page-action">
			<a href="#" class="button button-primary">
				<i class="icon-full-screen"></i>
				<?php echo __("View full-screen"); ?>
			</a>
			<a href="#" class="button button-white zoom-close">
				<i class="icon-cancel"></i>
				<?php echo __("Close"); ?>
			</a>
		</div>
	</div>
	<div class="center cf">
		<div class="col_9">
			<div class="base">
				<section class="drop-source">
					<p class="metadata"><%= date_published %></p>
					<a href="#" class="avatar-wrap"><img src="<%= source.avatar %>" /></a>
					<div class="byline">
						<h2><%= title %></h2>
						<p class="drop-source-channel">
							<a href="#">
								<span class="icon-<%= channel %>"></span>
								<?php echo __("via"); ?> <%= channel %>
							</a>
						</p>
					</div>
				</section>

				<div class="drop-body">
					<h1><%= title %></h1>
				</div>

				<div class="drop-actions cf">
					<ul class="dual-buttons drop-move">
						<li class="share">
							<a href="#" class="button-primary modal-trigger"><span class="icon-share"></span></a>
						</li>
						<li class="bucket">
							<a href="#" class="button-primary modal-trigger">
								<span class="icon-add-to-bucket"></span>
								<% if (buckets != null && buckets.length > 0) { %>
									<span class="bucket-total"><%= buckets.length %></span>
								<% } %>
							</a>
						</li>
					</ul>
					<span class="drop-score">
						<a href="#" class="button-white">
							<span class="icon-star"></span>
						</a>
					</span>
				</div>
				<h2 class="label attach"><?php echo __("Full Story"); ?></h2>
				<article class="drop-fullstory">
					<h1><a href="#"><%= title %></a></h1>
					<%= content %>
				</article>
			</div>
			<h2 class="label"><?php echo __("Related discussion"); ?></h2>
			<section class="drop-discussion list">
				<!-- TODO: Fetch the comments for the current drop via the API -->
			</section>
		</div>

		<div id="metadata" class="col_3"></div>
	</div>
</script>

<?php echo $droplet_js; ?>
