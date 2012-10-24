<div id="content" class="river">
<?php echo $river_notice; ?>
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
	<% if (typeof(droplet_image_url) != "undefined") { %>
		<a href="#" class="drop-image-wrap zoom-trigger"><img src="<%= droplet_image_url %>" class="drop-image" /></a>
	<% } %>
	<h1 class="drop"><a href="#" class="zoom-trigger"><%= droplet_title %></a></h1>
	<div class="drop-actions cf">
		<?php if ( ! $anonymous): ?>
			<ul class="dual-buttons move-drop">
				<li class="button-blue share">
					<a href="#" class="modal-trigger" title="<?php echo __("Share this drop"); ?>">
					    <span class="icon"></span>
					</a>
				</li>
				<?php if ( ! $anonymous): ?>
					<li class="button-blue bucket">
						<a href="#" class="modal-trigger" title="<?php echo __("Add drop to bucket"); ?>">
						    <span class="icon"></span>
						</a>
					</li>
				<?php endif; ?>
			</ul>
			<ul class="dual-buttons score-drop">
				<li class="button-white like <%= parseInt(user_score) == 1 ? 'scored' : ''  %>">
					<a href="#"><span class="icon"></span></a>
				</li>
				<li class="button-white dislike <%= parseInt(user_score) == -1 ? 'scored' : ''  %>">
					<a href="#"><span class="icon"></span></a>
				</li>
			</ul>
		<?php endif; ?>
	</div>
	<section class="drop-source cf">
		<% if ( identity_avatar) { %>
			<a href="#" class="avatar-wrap"><img src="<%= identity_avatar %>" /></a>
		<% } %>
		<div class="byline">
			<h2><%= identity_name %></h2>
			<p class="drop-source-channel <%= channel %>">
				<a href="#"><span class="icon"></span>via <%= channel %></a>
			</p>
		</div>
	</section>
</script>

<script type="text/template" id="drop-list-view-template">
	<div class="drop-content">
		<div class="drop-body">
			<% if (typeof(droplet_image_url) != "undefined") { %>
				<a href="#" class="drop-image-wrap zoom-trigger"><img src="<%= droplet_image_url %>" class="drop-image" /></a>
			<% } %>		
			<h1><a href="#" class="zoom-trigger"><%= droplet_title %></a></h1>
			<p class="metadata discussion"><%= new Date(droplet_date_pub).toLocaleString() %> 
				<a href="#">
					<span class="icon"></span>
					<strong><%= comment_count ? comment_count : 0 %></strong> 
					<?php echo __("comments"); ?>
				</a>
			</p>
		</div>
		<section class="drop-source cf">
			<% if ( identity_avatar) { %>
				<a href="#" class="avatar-wrap"><img src="<%= identity_avatar %>" /></a>
			<% } %>
			<div class="byline">
				<h2><%= identity_name %></h2>
				<p class="drop-source-channel <%= channel %>">
					<a href="#"><span class="icon"></span>via <%= channel %></a>
				</p>
			</div>
		</section>
	</div>
	<div class="drop-actions stacked cf">
		<?php if ( ! $anonymous): ?>
			<ul class="dual-buttons move-drop">
				<li class="button-blue share">
					<a href="#" class="modal-trigger" title="<?php echo __("Share this drop"); ?>">
					    <span class="icon"></span>
					</a>
				</li>
				<li class="button-blue bucket">
					<a href="#" class="modal-trigger" title="<?php echo __("Add drop to bucket"); ?>">
					    <span class="icon"></span>
					</a>
				</li>
			</ul>		
			<ul class="dual-buttons score-drop">
				<li class="button-white like <%= parseInt(user_score) == 1 ? 'scored' : ''  %>">
					<a href="#"><span class="icon"></span></a>
				</li>
				<li class="button-white dislike <%= parseInt(user_score) == -1 ? 'scored' : ''  %>">
					<a href="#"><span class="icon"></span></a>
				</li>
			</ul>
		<?php endif; ?>
	</div>
</script>

<script type="text/template" id="drop-photos-view-template">
	<% if (typeof(droplet_image_url) != "undefined") { %>
		<div class="drop-content">
			<div class="drop-body">
				<a href="#" class="drop-image-wrap zoom-trigger"><img src="<%= droplet_image_url %>" class="drop-image" /></a>
				<h1 class="drop"><a href="#" class="zoom-trigger"><%= droplet_title %></a></h1>
				<div class="drop-actions cf">
					<?php if ( ! $anonymous): ?>
						<ul class="dual-buttons move-drop">
							<li class="button-blue share">
								<a href="#" class="modal-trigger" title="<?php echo __("Share this drop"); ?>">
									<span class="icon"></span>
								</a>
							</li>
							<li class="button-blue bucket">
								<a href="#" class="modal-trigger" title="<?php echo __("Add drop to bucket"); ?>">
									<span class="icon"></span>
								</a>
							</li>
						</ul>
						<ul class="dual-buttons score-drop">
							<li class="button-white like <%= parseInt(user_score) == 1 ? 'scored' : ''  %>" >
								<a href="#"><span class="icon"></span></a>
							</li>
							<li class="button-white dislike <%= parseInt(user_score) == -1 ? 'scored' : ''  %>">
								<a href="#"><span class="icon"></span></a>
							</li>
						</ul>
					<?php endif; ?>
				</div>
			</div>
			<section class="drop-source cf">
				<% if ( identity_avatar) { %>
					<a href="#" class="avatar-wrap"><img src="<%= identity_avatar %>" /></a>
				<% } %>
				<div class="byline">
					<h2><%= identity_name %></h2>
					<p class="drop-source-channel <%= channel %>">
						<a href="#"><span class="icon"></span>via <%= channel %></a>
					</p>
				</div>
			</section>
		</div>
	<% } %>
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
			<span class="button-blue" style="display:none;">
				<a href="#" class="modal-trigger"><?php echo __("Edit"); ?></a>
			</span>
		<?php endif; ?>
	</h3>
	<div class="meta-data-content">
		<ul class="meta-list">
		</ul>
	</div>
</script>


<script type="text/template" id="drop-detail-template">
	<div class="settings-toolbar base">
		<p class="button-white close">
			<a href="#"><span class="icon"></span><?php echo __("Close"); ?></a>
		</p>
		<p class="button-blue button-big">
			<a href="#"><?php echo __("Edit this drop"); ?></a>
		</p>
	</div>

	<div class="base">
		<section class="drop-source cf">
			<p class="metadata"><%= new Date(droplet_date_pub).toLocaleString() %></p>
			<a href="#" class="avatar-wrap"><img src="<%= identity_avatar %>" /></a>
			<div class="byline">
				<h2><%= identity_name %></h2>
				<p class="drop-source-channel twitter">
					<a href="#"><span class="icon"></span>via <%= channel %></a>
				</p>
			</div>
		</section>
		<div class="drop-body">
			<%= droplet_title %>
		</div>
		<div class="drop-actions cf">
			<% if ($('#content > .river').length > 0) { %>
			<a href="#" class="button-prev"><div></div></a>
			<% } %>
			<?php if ( ! $anonymous): ?>
				<ul class="dual-buttons score-drop">
					<li class="button-white like <%= parseInt(user_score) == 1 ? 'scored' : ''  %>">
						<a href="#"><span class="icon"></span></a>
					</li>
					<li class="button-white dislike <%= parseInt(user_score) == -1 ? 'scored' : ''  %>">
						<a href="#"><span class="icon"></span></a>
					</li>
				</ul>
			<?php endif; ?>
				<% if ($('#content > .river').length > 0) { %>
				<a href="#" class="button-next"><div></div></a>
				<% } %>
			<?php if ( ! $anonymous): ?>
				<ul class="dual-buttons move-drop">
					<li class="button-blue share">
						<a href="#" class="modal-trigger"><span class="icon"></span></a>
					</li>
					<li class="button-blue bucket">
						<a href="#" class="modal-trigger"><span class="icon"></span></a>
					</li>
				</ul>
			<?php endif; ?>
		</div>
		<% if (droplet_content != droplet_title) { %>
		<section class="drop-fullstory drop-sub">
			<h2><?php echo __("Full story"); ?></h2>
			<% if (original_url) { %>
				<h3><a href="<%= original_url %>" target="_blank"><%= droplet_title %></a></h3>
			<% } else { %>
				<h3><%= droplet_title %></h3>
			<% } %>
			<%= droplet_content %>
		</section>
		<% } %>
		<% if (media.length > 0 && channel == "twitter") { %>
		<section class="drop-media drop-sub">
			<h2><?php echo __("Media"); ?></h2>
			<div>
			<% for (v in media) { %>
				<a href="<%= media[v].url %>" target="_blank">
					<img src="<%= media[v].url %>" />
				</a>
			<% } %>
			</div>
		</section>
		<% } %>
	</div>

	<section class="drop-discussion list">
			<h3>Related discussion</h3>
				
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
			
			<article class="alert-message blue col_12 drop nodisplay" id="new_comments_alert">
				<p style="text-align: center;"><a href="#">Click here to display <span class="message"></span>.</a></p>
			</article>
			
			<article class="drop cf nodisplay">
				<p class="button-white" id="discussions_next_page"><a href="#">Show more comments</a></p>
			</article>
			
			<article class="alert-message blue col_12 drop nodisplay" id="no_comments_alert">
				<p style="text-align: center;">No more comments</p>
			</article>
	</section>	
</script>

<script type="text/template" id="discussion-template">
	<div class="drop-content">
		<div class="drop-body">
			<% if (!deleted && (identity_user_id == logged_in_user)) { %>
				<p class="remove-small actions" title="Delete this comment">
					<span class="icon"></span><span class="nodisplay">Remove</span>
				</p>
			<% } %>
			<h1><%= comment_text %></h1>
			<p class="metadata discussion"><%= new Date(date_added).toLocaleString() %></span></a></p>
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
	<%= display_name %>
</script>

<script type="text/template" id="add-to-bucket-template">
<article class="modal">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1><?php echo __("Add to bucket"); ?></h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon"></span>
					<?php echo __("Close"); ?>
				</a>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body select-list">
		<p class="category own-title" style="display:none">Your buckets</p>
		<form class="own">
		</form>

		<p class="category collaborating-title" style="display:none">Buckets you collaborate on</p>
		<form class="collaborating">
		</form>

		<p class="category following-title" style="display:none">Buckets you follow</p>
		<form class="following">
		</form>
	</div>
	<div class="modal-body create-new" id="show-create-new">
		<form>
			<div class="field">
				<p class="button-blue" id="show-create-bucket-button"><a href="#" title="<?php echo __("Click here to create a new bucke"); ?>">Create a new bucket</a></p>
			</div>
			<div class="system_error"></div>
		</form>
	</div>
	<div class="modal-body create-new nodisplay" id="create-bucket-form">
		<form>
			<h2><?php echo __("Create a new bucket"); ?></h2>
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
			<h1><?php echo __("Edit"); ?> <%= label %></h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon"></span>
					<?php echo __("Close"); ?>
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
				<p class="button-blue"><a href="#"><?php echo __("Add this"); ?> <%= label %></a></p>
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

<script type="text/template" id="share-drop-template">
<article class="modal">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1><?php echo __("Share this drop"); ?></h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon"></span>
					<?php echo __("Close"); ?>
				</a>
			</h2>
		</div>
	</hgroup>
	<div class="modal-body link-list">
		<ul>
			<li class="twitter">
				<a href="https://twitter.com/share?url=<%= encodeURIComponent(drop_url) %>&text=<%= encodeURIComponent(droplet_title) %>" 
				    onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;" 
				    target="_blank">
					<span class="icon"></span><?php echo __("Twitter"); ?>
				</a>
			</li>
			<li class="facebook">
				<% var FBShareURL = encodeURIComponent(drop_url) + '&t' + encodeURIComponent(droplet_title); %>
				<a href="http://www.facebook.com/share.php?u=<%= FBShareURL %>" 
				    onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=600,width=600');return false;"
				    target="_blank">
				    <span class="icon"></span><?php echo __("Facebook"); ?>
			    <a/>
			</li>
			<li class="email"><a href="#"><span class="icon"></span><?php echo __("Email"); ?></a></li>
		</ul>
	</div>
	<section class="drop-summary cf">
		<a class="avatar-wrap"><img src="<%= identity_avatar %>" /></a>
		<div class="drop-content">
			<p><strong><%= identity_name %>:</strong> <%= droplet_title %></p>
			<p class="drop-source-channel rss">
				<a href="#"><span class="icon"></span>via <%= channel %></a>
			</p>
		</div>
	</section>
</article>
</script>

<script type="text/template" id="email-dialog-template">
<article class="modal">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1><?php echo __("Share via Email"); ?></h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon"></span>
					<?php echo __("Close"); ?>
				</a>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body">
		<!-- notifications -->
		<div class="alert-message red" id="error" style="display: none;">
			<p>
				<strong><?php echo __("Error"); ?></strong>
				<?php echo __("You have entererd an invalid email address or security code"); ?>
			</p>
		</div>
		<div class="alert-message blue" id="success" style="display: none;">
			<p>
				<?php echo __("The drop has been successfully shared via email!"); ?>
			</p>
		</div>
		<!-- /notitications -->

		<?php echo Form::open(); ?>
		<article class="container base">
			<section class="property-parameters">
				<div class="parameter">
					<label>
						<p class="field"><?php echo __("Send To:"); ?></p>
						<?php echo Form::input('recipient', ''); ?>
					</label>
				</div>
			</section>
		</article>
		<article class="container base">
			<section class="property-parameters">
				<div class="parameter image-field">
				    <label>
					    <p class="field"><?php echo __("Security Image:"); ?></p>
						<?php echo Captcha::instance()->render(); ?>
					</label>
				</div>
				<div class="parameter">
					<label>
						<p class="field"><?php echo __("Security Code:"); ?></p>
						<input type="text" name="security_code" placeholder="<?php echo __("Enter the text in the image above"); ?>" />
					</label>
				</div>
			</section>
		</article>
		<p class="button-blue">
			<a href="#"><?php echo __("Send"); ?></a>
		</p>
		<?php echo Form::close(); ?>
	</div>
	<section class="drop-summary cf">
		<a class="avatar-wrap"><img src="<%= identity_avatar %>" /></a>
		<div class="drop-content">
			<p><strong><%= identity_name %>:</strong> <%= droplet_title %></p>
			<p class="drop-source-channel rss">
				<a href="#"><span class="icon"></span>via <%= channel %></a>
			</p>
		</div>
	</section>
</article>
</script>

<script type="text/template" id="filters-modal-template">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1>Filters</h1>
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

	<div class="modal-body modal-containers link-list">
		<?php echo Form::open(); ?>
		<article class="container base">
			<section class="property-parameters">
				<div class="parameter">
					<label for="channel">
						<p class="field">Channel</p>
						<select name="channel">
							<option value="">Any</option>
							<% for (channel in channels) { %>
								<% var selected =  channels[channel]['channel'] == filters.get('channel') ? 'selected' : '' %>
								<option value="<%= channels[channel]['channel'] %>" <%= selected %>><%= channels[channel]['name'] %></option>
							<% } %>
						</select>
					</label>
				</div>
			</section>
			
			<section class="property-parameters">
				<div class="parameter">
					<label for="tags">
						<p class="field">Tags</p>
						<input type="text" name="tags" value="<%= filters.get('tags') ? decodeURIComponent(filters.get('tags')).replace(',', ', ') : '' %>"/>
					</label>
				</div>
			</section>
			
			<section class="property-parameters">
				<div class="parameter">
					<label for="date">
						<p class="field">Date</p>
						<input type="date" name="start_date" placeholder="DD-MM-YYYYY" value="<%= filters.get('start_date') %>" />
						<span class="combine">to</span>
						<input type="date" name="end_date" placeholder="DD-MM-YYYYY" value="<%= filters.get('end_date') %>" />
					</label>
					</label>
				</div>
			</section>
		</article>
		
		<div class="save-toolbar">
			<p class="button-blue"><a href="#"><?php echo __("Apply Filter"); ?></a></p>
			<p class="button-blank"><a href="#"><?php echo __("Remove Filter"); ?></a></p>
		</div>
		<?php echo Form::close(); ?>
	</div>
</script>

<?php echo $droplet_js; ?>