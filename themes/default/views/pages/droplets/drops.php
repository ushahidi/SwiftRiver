<!-- ALTERNATE MESSAGE TO VIEW NEW DROPS //
<article class="alert-message blue col_12 drop">
	<p><a href="#">13 new drops</a></p>
</article>
// END MESSAGE -->

<div id="drops-view-drops" class="center">
	<?php echo $nothing_to_display ?>
</div>

<div class="page_buttons cf" id="next_page_button">
    <p class="button-view"></p>
</div>

<script type="text/template" id="droplet-template">
	<h1><a href="#" class="zoom-trigger"><%= droplet_title %></a></h1>
	<div class="drop-actions cf">
		<ul class="dual-buttons move-drop">
			<li class="button-blue share"><a href="/modal-share.php" class="modal-trigger"><span class="icon"></span></a></li>
			<li class="button-blue bucket"><a href="/modal-bucket.php" class="modal-trigger"><span class="icon"></span></a></li>
		</ul>
		<?php if ( ! $anonymous): ?>
		<ul class="dual-buttons score-drop">
			<li class="button-white like <%=scores ? 'scored' : '' %>" style="<%= parseInt(user_score) == 1 ? 'display:none;' : ''  %>"><a href="#"><span class="icon"></span></a></li>
			<li class="button-white dislike <%=scores ? 'scored' : '' %>" style="<%= parseInt(user_score) == -1 ? 'display:none;' : ''  %>"><a href="#"><span class="icon"></span></a></li>
		</ul>
		<?php else: ?>
			<?php echo __("You need to :login to score droplets", array(':login' => HTML::anchor(URL::site('login'), __('log in')))); ?>
		<?php endif; ?>
	</div>
	<section class="drop-source cf">
		<a href="#" class="avatar-wrap"><img src="<%= identity_avatar %>" /></a>
		<div class="byline">
			<h2><%= identity_name %></h2>
			<p class="drop-source-channel <%= channel %>"><a href="#"><span class="icon"></span>via <%= channel %></a></p>
		</div>
	</section>
</script>

<script type="text/template" id="bucket-template">
	<a class="<%= _.find(droplet_buckets, function(droplet_bucket) { return droplet_bucket['id'] == id }) ? 'selected' : '' %>"><span class="input"></span><%= bucket_name %></a>
</script>

<script type="text/template" id="tag-template">
	<?php if ($owner): ?>
		<span class="actions">
			<a href="#" class="button-delete cross"></a>
			<ul class="dropdown left">
				<p><?php echo __("Are you sure you want to remove this tag?"); ?></p>
		
				<li class="confirm"><a onclick=""><?php echo __("Yep."); ?></a></li>
				<li class="cancel"><a onclick=""><?php echo __("No, nevermind.") ?></a></li>
			</ul>
		</span>
	<?php endif; ?>
	<a class="tag-name"><%= tag %></a>
</script>

<script type="text/template" id="link-template">
    <% if (url) { %>
		<% var link_short = url.substr(0, 27) + "..."; %>
		<span title="<%= url %>"><a href="<%= url %>"><%= link_short %></a></span>
	<% } %>
</script>

<script type="text/template" id="place-template">
	<a class="place-name"><%= place_name %></a>
</script>

<!-- related discussion -->
<script type="text/template" id="discussion-item-template">
	<div class="summary cf">
		<section class="source <%= channel %>">
			<a><img src="<%= identity_avatar %>"></a>
		</section>
		<section class="content">
			<hgroup>
				<p class="date"><%= droplet_date_pub %></p>
				<string><%= droplet_content %></strong>
			</hgroup>
			<div class="body"><p><%= identity_name %></p></div>
		</section>
	</div>
</script>
<!-- /related discussion -->

<?php echo $droplet_js; ?>
