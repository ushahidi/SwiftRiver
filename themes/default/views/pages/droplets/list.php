<div id="droplet-list" class="trend-container cf">
</div>

<script type="text/template" id="droplet-list-item">
	<div class="summary cf">
		<section class="source <%= channel %>">
			<a><img src="<%= identity_avatar %>"/></a>
			<div class="actions">
				<span class="type"></span>
				<p class="button-change score"><a onclick=""><span>0</span></a></p>
				<div class="clear"></div>
			</div>
		</section>
		<div class="content">
			<hgroup>
				<p class="date"><%= droplet_date_pub %></p>
				<h1><%= identity_name %></h1>
			</hgroup>
			<div class="body">
				<p><%= droplet_title %></p>
			</div>
		</div>
		
		<section class="actions two_buttons">
			<p class="button-view"><a class="detail-view"><span class="icon"></span></a></p>
			<div class="button bucket">
				<p class="button-change checkbox-options"><a class="bucket-view"><span class="icon"></span></a></p>
				<div class="clear"></div>
				<ul class="dropdown">
					<li class="create-new">
						<a class="plus" onclick=""><span class="icon"></span><?php echo __("Create new bucket"); ?></a>
					</li>
				</ul>
			</div>
		</section>
	</div>

	<div class="drawer" id="detail-section-<%= id %>">
		<div class="detail">
			<div class="arrow top"><span></span></div>
			<div class="canyon cf">
				<section class="meta">
					<div class="item cf">
						<h2><?php echo __("Tags"); ?></h2>
						<ul class="tags cf" id="droplet-tags-<%= id %>"></ul>
					</div>
	
					<div class="item cf">
						<h2><?php echo __("Locations"); ?></h2>
						<ul class="tags cf" id="droplet-locations-<%= id %>"></ul>
					</div>
					<div class="item cf">
						<h2><?php echo __("Links"); ?></h2>
						<p class="edit" id="droplet-links-<%= id %>"></p>
					</div>
					<div class="item cf">
						<p class="button-change">
							<a><?php echo __("+ Add Attachment"); ?></a>
						</p>
					</div>
				</section>
	
				<div class="content"></div>
			</div>
			<div class="arrow bottom"><a class="close" onclick=""><?php echo __("Hide Detail"); ?></a></div>
		</div>
	</div>
</script>


<script type="text/template" id="droplet-details">
	<hgroup>
		<h2><?php echo __("Full Story"); ?></h2>
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

<script type="text/template" id="buckets-list-item">
	<a><span class="select"></span><%= bucket_name %></a>
</script>

<script type="text/template" id="create-inline-bucket">
	<span class="create-name">
		<input type="text" value="" name="bucket_name" placeholder="<?php echo __("Name your new bucket"); ?>">
		<div class="buttons">
			<button class="save"><?php echo __("Save"); ?></button>
			<button class="cancel"><?php echo __("Cancel"); ?></button>
		</div>
	</span>
</script>

<?php echo $droplet_js; ?>