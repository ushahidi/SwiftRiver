<div id="stream" class="col_9">
</div>

<script type="text/template" id="drop-listing-template">
<div id="drops-view"></div>
</script>

<script type="text/template" id="drop-drops-view-template">
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
		<span class="total"><%= count %></span>
		<span class="icon-arrow-down"></span>
		<span class="icon-arrow-up"></span>	
	</span>
	<span class="filters-type-settings">
		<a href="#" class="modal-trigger">
			<span class="icon-cog"></span>
		</a>
	</span>
	<h2><%= label %></h2>
	<div class="filters-type-details">
		<ul></ul>
	</div>
</script>

<script type="text/template" id="metadata-item-template">
	<a href="#" title="<%= metadataText %>">
	<% if (metadataText.length > 20) { %>
		<%= metadataText.substring(0, 20) + "..." %>
	<% } else { %>
		<%= metadataText %>
	<% } %>
	</a>
</script>

<script type="text/template" id="drop-full-view-template">
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
	<div id="drop-content-container" class="center cf">
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

<script type="text/template" id="edit-metadata-template">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white">
					<i class="icon-cancel"></i><?php echo __("Close"); ?>
				</a>
				<h1>
					<a href="#group-name" class="modal-transition">
						<?php echo __("Edit"); ?> <%= label %>
					</a>
				</h1>
			</div>
			<div class="modal-body">
				<div class="view-table base">
					<ul>
						<li class="add">
							<a href="#" class="modal-transition"><?php echo __("Add"); ?> <%= label %></a>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<div id="modal-secondary" class="modal-view">
			<div class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1><?php echo __("Add"); ?> <%= label.substring(0, label.length-1) %></h1>
				</div>
				<div class="modal-body modal-tabs-container">
					<!-- Input fields for adding metadata go here-->
					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-close"><?php echo __("Done"); ?></a>
					</div>
				</div>
			</div>
		</div>

	</div>
</script>

<script type="text/template" id="edit-metadata-item-template">
	<a href="#" class="modal-transition" title="<%= label %>">
		<span class="remove icon-cancel"></span>
		<% if (label.length > 50) { %>
			<%= label.substring(0, 50) %>
		<% }  else { %>
			<%= label %>
		<% } %>
	</a>
</script>

<script type="text/template" id="edit-metadata-item-field-template">
	<div class="base">
		<div class="modal-field">
			<a href="#" class="add-field">
				<span class="icon-plus"></span>
			</a>
			<input type="text" name="new_metadata" placeholder="<%= placeholder %>"/>
		</div>
	</div>
</script>

<?php echo $droplet_js; ?>
