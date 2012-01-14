<div id="droplet-list" class="trend-container cf">
</div>

<script type="text/template" id="droplet-list-item">
	<div class="summary">
		<section class="source <%= channel %>">
			<a><img src="<%= identity_avatar %>"/></a>
			<div class="actions">
				<span class="type"></span>
				<p class="button-change score"><a onclick=""><span>0</span></a></p>
				<div class="clear"></div>
			</div>
		</section>
		
		<section class="content">
			<div class="title">
				<p class="date"><%= droplet_date_pub %></p>
				<h1><%= identity_name %></h1>
			</div>
			<div class="body">
				<p><%= droplet_title %></p>
			</div>
		</section>
		
		<section class="actions">
			<p class="button-view"><a href="#" class="detail-view"><span></span><strong>detail</strong></a></p>
			<div class="button">
				<p class="button-change bucket"><a href="#" class="detail-view"><span></span><strong>buckets</strong></a></p>
			</div>
			<div class="clear"></div>
		</section>
		
		<section class="detail cf" style="display:none" id="detail-section-<%= id %>">
			<div class="arrow top"><span></span></div>
			<div class="canyon cf">
				<aside>
					<div class="item cf">
						<h2><?php echo __('Tags'); ?></h2>
						<ul class="tags cf" id="droplet-tags-<%= id %>"></ul>
					</div>

					<div class="item cf">
						<h2><?php echo __('Locations'); ?></h2>
						<ul class="tags cf" id="droplet-locations-<%= id %>"></ul>
					</div>
					<div class="item cf">
						<h2><?php echo __('Links'); ?></h2>
						<p class="edit" id="droplet-links-<%= id %>"></p>
					</div>
					<div class="item cf">
						<p class="button-change">
							<a><?php echo __('+ Add Attachment'); ?></a>
						</p>
					</div>
				</aside>

				<div id="droplet-view" class="right-column"></div>
			</div>
			<div class="arrow bottom"><a class="close" onclick=""><?php echo __('Hide Detail'); ?></a></div>
		</section>
	</div>
</script>


<script type="text/template" id="droplet-details">
	<hgroup>
		<h2><?php echo __('Full Story'); ?></h2>
		<h1 class="edit"><span class="edit-trigger"><%= droplet_title %></span></h1>
	</hgroup>
	<div class="edit">
		<span class="edit-trigger"><%= droplet_content %></span>
	</div>
</script>

<script type="text/template" id="droplet-tag-item">
	<a href="#"><%= tag %></a>
</script>

<script type="text/template" id="droplet-place-item">
	<a href="#"><%= place_name %></a>
</script>

<script type="text/template" id="droplet-link-item">
	<%= link_full %>
</script>


<script type="text/javascript">
	<?php echo $droplet_js; ?>
</script>
