<div id="droplet-list" class="trend-container cf">
</div>
<div class="page_buttons cf" id="next_page_button">
    <p class="button-view"></p>
</div>

<script type="text/template" id="droplet-template">
	<div class="summary cf">
		<section class="source <%= channel %>">
			<a><img src="<%= identity_avatar %>" /></a>
			<div class="actions">
				<span class="type"></span>
				<p class="button-change score"><a onclick=""><span>0</span></a><p>
				<div class="clear"></div>
				<ul class="dropdown left">
					<li class="confirm"><a onclick=""><?php echo  __("This is useful"); ?></a></li>
					<li class="not_useful"><a onclick=""><?php echo __("This is not useful"); ?></a></li>
				</ul>
			</div>
		</section>
		<section class="content">
			<hgroup>
				<p class="date"><%= droplet_date_pub %></p>
				<h1><%= identity_name %></h1>
			</hgroup>
			<div class="body">
				<p><%= droplet_title %></p>
			</div>
		</section>
		<section class="actions two_buttons">
			<p class="button-view"><a class="detail-view"><span class="icon"></span></a></p>
			<div class="button bucket">
				<p class="button-change checkbox-options"><a class="bucket-view"><span class="icon"></span></a></p>
				<div class="clear"></div>
				<div class="dropdown">
					<div class="container buckets-list">
						<h3><?php echo __("Add to Bucket"); ?></h3>
						<ul></ul>
					</div>
					<div class="container">
						<p class="create-new">
							<a class="plus"><?php echo __("Create new bucket"); ?></a>
							<div class="create-name">
								<input type="text" value="" name="bucket_name" placeholder="<?php echo __("Name your new bucket"); ?>">
								<div class="buttons">
									<button class="save"><?php echo __("Save"); ?></button>
									<button class="cancel"><?php echo __("Cancel"); ?></button>
								</div>
							</div>							
						</p>
					</div>
				</div>
			</div>
		</section>
	</div>

	<div class="drawer">
		<div class="detail">
			<div class="arrow top"><span></span></div>
			<div class="canyon cf">
				<section class="meta">
					<div class="item actions cf">
						<p class="button-delete cf"><a><?php echo __("Delete droplet"); ?></a></p>
						<ul class="dropdown left delete-droplet">
							<p><?php echo __("Are you sure you want to delete this droplet?"); ?></p>

							<li class="confirm"><a onclick=""><?php echo __("Yep."); ?></a></li>
							<li class="cancel"><a onclick=""><?php echo __("No, nevermind.") ?></a></li>
						</ul>
					</div>

					<div class="item cf">
						<h2>Tags</h2>
						<ul class="tags cf"></ul>
						<p class="button-change"><a><?php echo __("Add tag") ?></a></p>
					</div>

					<div class="item cf locations">
						<h2><?php echo __("Location"); ?></h2>
					</div>

					<div class="item cf links">
						<h2><?php echo __("Links"); ?></h2>
					</div>

					<div class="item cf">
						<p class="button-change"><a><?php echo __("Add attachment"); ?></a></p>
					</div>																					
				</section>

			<div class="content">
				<article class="fullstory">
					<hgroup>
						<h2><?php echo __('Full story'); ?></h2>
						<h1 class="edit"><span class="edit_trigger" title="text" onclick=""><%= droplet_title %></span></h1>
					</hgroup>
					<div class="edit">
						<span class="edit_trigger" title="textarea" onclick="">
							<%= droplet_content %>
						</span>
					</div>
				</article>
			</div>
		</div>
		<div class="arrow bottom"><a class="close"><?php echo __('Hide detail'); ?></a></div>
	</div>
	<div class="clear"></div>
</script>

<script type="text/template" id="bucket-template">
	<a class="<%= _.find(droplet_buckets, function(droplet_bucket) { return droplet_bucket['id'] == id }) ? 'selected' : '' %>"><span class="input"></span><%= bucket_name %></a>
</script>

<script type="text/template" id="tag-template">
	<a><%= tag %></a>
</script>

<script type="text/template" id="link-template">
	<span class="edit_trigger" title="text" onclick=""><%= link_full %></span>
</script>

<script type="text/template" id="place-template">
	<span class="edit_trigger" title="text" onclick=""><%= place_name %></span>
</script>

<!-- related discussion -->
<script type="text/template" id="discussion-template">
	<hgroup>
		<h2><?php echo __("Related Discussion"); ?></h2>
	</hgroup>
</script>

<script type="text/template" id="discussion-item-template">
	<div class="summary">
		<section class="source <%= channel %>">
			<a><img src="<%= identity_avatar %>"></a>
		</section>
		<section class="content">
			<div class="title">
				<p class="date"><%= droplet_date_pub %></p>
				<h1><%= identity_name %></h1>
			</div>
			<div class="body"><p><%= droplet_content %></p></body>
		</section>
	</div>
</script>
<!-- /related discussion -->

<?php echo $droplet_js; ?>