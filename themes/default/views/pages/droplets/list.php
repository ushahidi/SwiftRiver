<div id="droplet-list" class="trend-container cf">
	<?php echo $nothing_to_display ?>
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
					<p class="button-change score"><a class="<%=scores ? 'scored' : '' %>"><span><%= scores ? scores : 0 %></span></a><p>
					<div class="clear"></div>
					<?php if ( ! $anonymous): ?>
						<ul class="dropdown left">
						   <!-- Show whether droplet has been scored before -->								
						   <p style="<%= user_score ? '': 'display:none;' %>"><?php echo __("You have scored this droplet before. Change your score below?"); ?></p>						
							<li class="confirm" style="<%= parseInt(user_score) == 1 ? 'display:none;' : ''  %>"><a><?php echo  __("This is useful"); ?></a></li>
							<li class="not_useful" style="<%= parseInt(user_score) == -1 ? 'display:none;' : ''  %>"><a><?php echo __("This is not useful"); ?></a></li>
						</ul>
					<?php else: ?>
						<ul class="dropdown left">
						   <p><?php echo __("You need to :login to score droplets", array(':login' => HTML::anchor(URL::site('login'), __('log in')))); ?></p>
						</ul>
					<?php endif; ?>
				</div>
		</section>
		<section class="content">
			<hgroup>
				<p class="date"><%= new Date(droplet_date_pub).toLocaleString() %></p>
				<strong><a><%= droplet_title %></a></strong>
			</hgroup>
			<div class="body">
				<p><%= identity_name %></p>
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
					<?php if ( ! $anonymous): ?>
						<div class="container">
							<p class="create-new">
								<a class="plus"><?php echo __("Create new bucket"); ?></a>
								<div class="loading"></div>
								<div class="system_error" style="display:none"></div>
								<div class="create-name">
									<input id="new-bucket-name" maxlength="25" type="text" value="" name="bucket_name" placeholder="<?php echo __("Name your new bucket"); ?>">
									<div class="buttons">
										<button class="save"><?php echo __("Save"); ?></button>
										<button class="cancel"><?php echo __("Cancel"); ?></button>
									</div>
								</div>
							</p>
						</div>
					<?php else: ?>
						<div class="container">
							<p><?php echo __("You need to :login to add a drop to a bucket", array(':login' => HTML::anchor(URL::site('login'), __('log in')))); ?></p>
						</div>
					<?php endif; ?>
				</div>
			</div>
		</section>
	</div>

	<div class="drawer">
		<div class="detail">
			<div class="arrow top"><span></span></div>
			<div class="canyon cf">
				<section class="meta">
					<?php if ($owner): ?>
						<div class="item actions cf">
							<p class="button-delete cf"><a><?php echo __("Delete Drop"); ?></a></p>
							<ul class="dropdown left delete-droplet">
								<p><?php echo __("Are you sure you want to delete this droplet?"); ?></p>
                    	
								<li class="confirm"><a onclick=""><?php echo __("Yep."); ?></a></li>
								<li class="cancel"><a onclick=""><?php echo __("No, nevermind.") ?></a></li>
							</ul>
						</div>
					<?php endif; ?>

					<div class="item cf">
						<% if(tags.length > 0) { %>
							<h2><?php echo __("Tags") ?></h2>
						<% } %>
						<ul class="tags cf"></ul>
						<div class="loading"></div>
						<div class="system_error" style="display:none"></div>
						<?php if ($owner): ?>
							<div class="container" id="add-tag">
								<p class="create-new">
									<p class="button-change"><a><?php echo __("Add tag") ?></a></p>
									<div class="create-name">
										<input id="new-tag-name" type="text" value="" name="bucket_name" placeholder="<?php echo __("Name your new tag"); ?>">
										<div class="buttons">
											<button class="save"><?php echo __("Save"); ?></button>
											<button class="cancel"><?php echo __("Cancel"); ?></button>
										</div>
									</div>
								</p>
							</div>
						<?php endif; ?>
						
					</div>

					<div class="item cf">
						<% if (places.length > 0) { %>
							<h2><?php echo __("Location"); ?></h2>
						<% } %>
						<ul class="places"></ul>
					</div>

					<div class="item cf links">
						<% if (links.length > 0) { %>
							<h2><?php echo __("Links"); ?></h2>
						<% } %>
					</div>
                
					<?php if ($owner): ?>
					   <!-- Hide this till we figure it out
						<div class="item cf">
							<p class="button-change"><a><?php echo __("Add attachment"); ?></a></p>
						</div>
						-->
					<?php endif; ?>
				</section>

			<div class="content">
				<article class="fullstory">
					<hgroup>
						<h2><?php echo __('Full story'); ?></h2>
						<h1><span title="text" onclick=""><%= droplet_title %></span></h1>
					</hgroup>
					<div>
						<span>
						</span>
					</div>
				</article>
				
				<section class="discussion">
					<hgroup>
						<h2><?php echo __("Comments"); ?></h2>
					</hgroup>
					<div class="loading"></div>
					<div class="system_error" style="display:none"></div>
					<?php if ($owner): ?>
						<article class="item add-reply">
							<div class="summary cf">
								<section class="source">
									<a><img src="<?php echo Swiftriver_Users::gravatar($user->email, 45); ?>"></a>
								</section>
									<section class="content">
										<textarea rows="8" cols="60"></textarea>
										<p class="button-go"><a><?php echo __("Add Comment"); ?></a></p>
									</section>
							</div>
						</article>
					<?php endif; ?>
				</section>
				
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