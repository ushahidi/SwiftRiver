<div id="content" class="river">
</div>

<script type="text/template" id="drop-listing-template">
<div id="drops-view" class="center cf">
	<?php echo $nothing_to_display ?>
</div>
<div class="cf"></div>
<div class="meow page_buttons cf" id="next_page_button">
    <p class="button-view"></p>
</div>
</script>

<script type="text/template" id="drop-drops-view-template">
	<h1 class="drop"><a href="#" class="zoom-trigger"><%= droplet_title %></a></h1>
	<div class="drop-actions cf">
		<?php if ( ! $anonymous): ?>
			<ul class="dual-buttons move-drop">
				<li class="button-blue share"><a href="/markup/modal-share.php" class="modal-trigger"><span class="icon"></span></a></li>
				<?php if ( ! $anonymous): ?>
					<li class="button-blue bucket"><a href="#" class="modal-trigger"><span class="icon"></span></a></li>
				<?php endif; ?>
			</ul>
			<ul class="dual-buttons score-drop">
				<li class="button-white like <%= parseInt(user_score) == 1 ? 'scored' : ''  %>"><a href="#"><span class="icon"></span></a></li>
				<li class="button-white dislike <%= parseInt(user_score) == -1 ? 'scored' : ''  %>"><a href="#"><span class="icon"></span></a></li>
			</ul>
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

<script type="text/template" id="drop-list-view-template">
	<div class="drop-content">
		<div class="drop-body">
			<h1><a href="#" class="zoom-trigger"><%= droplet_title %></a></h1>
			<p class="metadata discussion"><%= new Date(droplet_date_pub).toLocaleString() %> <a href="#"><span class="icon"></span><strong><%= discussions.length %></strong> comments</a></p>
		</div>
		<section class="drop-source cf">
			<a href="#" class="avatar-wrap"><img src="<%= identity_avatar %>" /></a>
			<div class="byline">
				<h2><%= identity_name %></h2>
				<p class="drop-source-channel <%= channel %>"><a href="#"><span class="icon"></span>via <%= channel %></a></p>
			</div>
		</section>
	</div>
	<div class="drop-actions stacked cf">
		<?php if ( ! $anonymous): ?>
			<ul class="dual-buttons move-drop">
				<li class="button-blue share"><a href="/markup/modal-share.php" class="modal-trigger"><span class="icon"></span></a></li>
					<li class="button-blue bucket"><a href="#" class="modal-trigger"><span class="icon"></span></a></li>			
			</ul>		
			<ul class="dual-buttons score-drop">
				<li class="button-white like <%= parseInt(user_score) == 1 ? 'scored' : ''  %>"><a href="#"><span class="icon"></span></a></li>
				<li class="button-white dislike <%= parseInt(user_score) == -1 ? 'scored' : ''  %>"><a href="#"><span class="icon"></span></a></li>
			</ul>
		<?php endif; ?>
	</div>
</script>

<script type="text/template" id="drop-full-view-template">
	<article class="drop drop-full cf">
		<div class="center">
			<!-- DROP DETAIL WILL GO HERE -->
			
			<div class="col_3">
				<!-- META DATA HERE -->
			</div>
		</div>
	</article>
</script>

<script type="text/template" id="metadata-template">
	<h3 class="arrow">
		<span class="icon"></span>
		<?php if ( ! $anonymous): ?>
			<span class="button-blue" style="display:none;"><a href="#" class="modal-trigger">Edit</a></span>
		<?php endif; ?>
	</h3>
	<div class="meta-data-content">
		<ul class="meta-list">
		</ul>
	</div>
</script>


<script type="text/template" id="drop-detail-template">
	<div class="settings-toolbar base">
		<p class="button-white close"><a href="#"><span class="icon"></span>Close</a></p>
		<p class="button-blue button-big"><a href="#">Edit this drop</a></p>
	</div>

	<div class="base">
		<section class="drop-source cf">
			<p class="metadata"><%= new Date(droplet_date_pub).toLocaleString() %></p>
			<a href="#" class="avatar-wrap"><img src="<%= identity_avatar %>" /></a>
			<div class="byline">
				<h2><%= identity_name %></h2>
				<p class="drop-source-channel twitter"><a href="#"><span class="icon"></span>via <%= channel %></a></p>
			</div>
		</section>
		<div class="drop-body">
			<%= droplet_title %>
		</div>
		<div class="drop-actions cf">
			<?php if ( ! $anonymous): ?>
				<ul class="dual-buttons score-drop">
					<li class="button-white like <%= parseInt(user_score) == 1 ? 'scored' : ''  %>"><a href="#"><span class="icon"></span></a></li>
					<li class="button-white dislike <%= parseInt(user_score) == -1 ? 'scored' : ''  %>"><a href="#"><span class="icon"></span></a></li>
				</ul>
				<ul class="dual-buttons move-drop">
					<li class="button-blue share"><a href="/markup/modal-share.php" class="modal-trigger"><span class="icon"></span></a></li>
					<li class="button-blue bucket"><a href="#" class="modal-trigger"><span class="icon"></span></a></li>
				</ul>
			<?php endif; ?>
		</div>
		<% if (droplet_content != droplet_title) { %>
		<section class="drop-fullstory drop-sub">
			<h2>Full story</h2>
			<h3><%= droplet_title %></h3>
			<%= droplet_content %>
		</section>
		<% } %>
	</div>

	<section class="drop-discussion list">
		<% if (discussions.length) { %>
			<h3>Related discussion</h3>
		<% } %>
				
			<article class="add-comment drop base cf">
				<?php if ( ! $anonymous): ?>
					<div class="drop-content">
						<div class="drop-body">
							<textarea></textarea>
						</div>
						<section class="drop-source cf">
							<a href="#" class="avatar-wrap"><img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" /></a>
							<div class="byline">
								<h2><?php echo $user->name ?></h2>
							</div>
						</section>
					</div>
					<div class="drop-actions cf">
						<p class="button-blue"><a href="#">Publish</a></p>
					</div>
				<?php endif; ?>
			</article>
	</section>	
</script>

<script type="text/template" id="discussion-template">
	<div class="drop-content">
		<div class="drop-body">
			<h1><%= droplet_content %></h1>
			<p class="metadata discussion"><%= new Date(droplet_date_pub).toLocaleString() %></span></a></p>
		</div>
		<section class="drop-source cf">
			<a href="#" class="avatar-wrap"><img src="<%= identity_avatar %>" /></a>
			<div class="byline">
				<h2><%= identity_name %></h2>
			</div>
		</section>
	</div>
</script>

<script type="text/template" id="bucket-template">
	<input type="checkbox" <% if (containsDrop) { %> checked <% } %>/>
	<%= bucket_name %>
</script>

<script type="text/template" id="add-to-bucket-template">
<article class="modal">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1>Add to bucket</h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon"></span>
					Close
				</a>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body select-list" style="overflow: auto; max-height: 150px">
		<form>
		</form>
	</div>
	<div class="modal-body create-new">
		<form>
			<h2>Create a new bucket</h2>
			<div class="field">
				<input type="text" placeholder="Name your new bucket" class="name" name="new_bucket" />
				<p class="button-blue"><a href="#">Save and add drop</a></p>
			</div>
			<div class="system_error"></div>
		</form>
	</div>
	<section class="drop-summary cf">
		<a class="avatar-wrap"><img src="<%= identity_avatar %>" /></a>
		<div class="drop-content">
			<p><strong><%= identity_name %>:</strong> <%= droplet_title %></p>
			<p class="drop-source-channel rss"><a href="#"><span class="icon"></span>via <%= channel %></a></p>
		</div>
	</section>
</article>
</script>

<script type="text/template" id="edit-metadata-listitem">
	<a href="#"><%= label %></a><span class="remove-small"><span class="icon"></span></span>
</script>

<script type="text/template" id="add-metadata-template">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1>Edit <%= label %></h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon"></span>
					Close
				</a>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body link-list"  style="overflow: auto; max-height: 150px">
		<h2>Current <%= label %>s</h2>
		<ul>
		</ul>
	</div>

	<div class="modal-body create-new">
		<form>
			<h2>Add a new <%= label %></h2>
			<div class="field">
				<input type="text" placeholder="Name the <%= label %>" class="name" name="new_metadata" />
				<p class="button-blue"><a href="#">Add this <%= label %></a></p>
			</div>
			<div class="system_error"></div>
		</form>
	</div>
	<section class="drop-summary cf">
		<a href="#" class="avatar-wrap"><img src="<%= identity_avatar %>" /></a>
		<div class="drop-content">
			<p><strong><%= identity_name %>:</strong> <%= droplet_title %></p>
			<p class="drop-source-channel rss"><a href="#"><span class="icon"></span>via <%= channel %></a></p>
		</div>
	</section>
</script>

<?php echo $droplet_js; ?>
