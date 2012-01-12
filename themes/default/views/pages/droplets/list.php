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
			<div class="content">
				<hgroup>
					<p class="date"><?php echo $droplet['droplet_date_pub'] ?></p>
					<h1><?php echo $droplet['identity_name'] ?></h1>
				</hgroup>
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
			<section class="actions">
				<p class="button_view"><a href="/droplet/detail/<?php echo $droplet['id'];?>" class="detail_view"><span class="icon"></span></a></p>
			</section>
		</div>
	
</script>

<div id="droplet-view">
</div>

<script type="text/template" id="droplet-details">
	<div class="arrow top"><span></span></div>
	<div class="canyon cf">
		<aside>
			<div class="item cf">
				<h2>Tags</h2>
				<ul class="tags cf">
					<li><a href="#"><%= tag %></a></li>
				</ul>
			</div>
		
			<div class="item cf">
				<h2>Location</h2>
				<p class="edit"><span><%= place_name %></span></p>
			</div>
		
			<div class="item cf">
				<p class="button-change"><a><?php echo __('Add Attachment'); ?></a></p>
			</div>
		</aside>
	
		<div class="right-column">
			<article class="fullstory">
				<hgroup>
					<h2><?php echo __('Full Story'); ?></h2>
					<h1 class="edit"><span class="edit-trigger"><%= droplet_title %></span></h1>
				</hgroup>
				<div class="edit">
					<span class="edit-trigger"><%= droplet_content %></span>
				</div>
			</article>
		</div>
	</div>
	<div class="arrow bottom"><a class="close" onclick=""><?php echo __('Hide Detail'); ?></a></div>
</script>

<script type="text/javascript">
	<?php echo $droplet_js; ?>
</script>
