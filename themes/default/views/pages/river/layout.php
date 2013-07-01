<hgroup class="page-title cf">
	<div class="center">
		<div class="col_9">
			<h1><?php print $page_title; ?></h1>
		</div>
		<div class="page-action col_3">
			<a href="#" class="button button-primary filters-trigger"><i class="icon-filter"></i>Filters</a>

			<?php if (isset($follow_button)): ?>
			<span class="button-follow" id="follow-button">
				<?php echo $follow_button; ?>
			</span>
			<?php endif; ?>

			<?php if ($owner): ?>
				<a href="<?php echo $settings_url ?>" class="button button-white settings"><span class="icon-cog"></span></a>
			<?php endif; ?>
		</div>
	</div>
</hgroup>

<div id="content" class="river drops cf">
	<div class="center">

		<section id="filters" class="col_3">
			<div class="modal-window">
				<div class="modal">		
					<div class="modal-title cf">
						<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
						<h1>Filters</h1>
					</div>
					<ul class="filters-primary">
						<?php foreach ($nav as $item): ?>
						<li id="<?php echo $item['id']; ?>" class="<?php echo $item['active'] == $active ? 'active' : ''; ?>">
							<a href="<?php echo $river_base_url.$item['url']; ?>">
								<span class="total"></span>
								<?php echo $item['label'];?>
							</a>
						</li>
						<?php endforeach; ?>
					</ul>
					
					<!-- Restrict this channels and read/unread sections to "owners" -->
					<?php if ($owner): ?>
					<div class="filters-type" id="drops-state-filter">
						<ul>
							<li class="unread"><a href="#"><span class="total">0</span> <?php echo __("Unread"); ?></a></li>
							<li class="read"><a href="#"><span class="total">0</span> <?php echo __("Read"); ?></a></li>
						</ul>
					</div>
			
					<div class="filters-type" id="drops-channel-list">
						<span class="toggle-filters-display">
							<span class="total">0</span>
							<span class="icon-arrow-down"></span>
							<span class="icon-arrow-up"></span>
						</span>
						<span class="filters-type-settings"><a href="#"><span class="icon-cog"></span></a></span>
						<h2><?php echo __("Channels"); ?></h2>
						<div class="filters-type-details">
							<ul>
								<!-- List of channels goes here -->
							</ul>
						</div>
					</div>
					<?php endif; ?>

					<div class="filters-type" id="drop-search-filter">
						<ul id="search-filter-list">
						</ul>
						<a href="#" class="button-add modal-trigger">
							<i class="icon-search"></i>
							<?php echo __("Add search filter"); ?>
						</a>
					</div>

				</div>
			</div>
		</section>
			
		<?php echo $droplets_view; ?>
	</div>
</div>

<script type="text/javascript">
$(function() {
	var channelsConfig = new Channels.ChannelsConfig();
	channelsConfig.reset(<?php echo $channels_config; ?>);
	Channels.channelList.reset(<?php echo $channels; ?>);	
	
	$('.filters-type-settings a').live('click', function () {
		modalShow(new Channels.ChannelsModalView({
			collection: Channels.channelList, 
			config: channelsConfig,
			baseUrl: "<?php echo $channels_base_url ?>"
		}).render().el);
		return false;
	});
	
	new Channels.DroplistChannelsView({
		collection: Channels.channelList,
		dropFilters: window.dropsView.getFilters()});
});
</script>

<script type="text/template" id="channels-modal-template">
	<div class="modal-title cf">
		<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
		<h1>Channels</h1>
	</div>

	<div class="modal-body">
		<div class="view-table base">
			<ul>
				<li class="add"><a href="#">Add channel</a></li>
			</ul>
		</div>
	</div>
</script>

<script type="text/template" id="channel-drop-list-template">
	<a href="#" style="text-overflow:ellipsis;">
		<i class="icon-<%= channel %>"></i>
		<span class="total"><%= drop_count %></span>
		<%= display_name %>
	</a>
</script>

<script type="text/template" id="channel-modal-template">
	<a href="#">
	<span class="remove icon-cancel"></span>
	<i class="channel-icon icon-<%= channel %>"></i>
	<%= display_name %>
	</a>
</script>

<script type="text/template" id="channel-config-template">
	<a href="#"><span class="channel-icon icon-<%=channel%>"></span></a>
</script>

<script type="text/template" id="channel-option-config-template">
	<a href="#"><%=label%></a>
</script>

<script type="text/template" id="edit-group-channel-template">
	<div class="active">
		<span class="modal-field">
			<h4 class="label">Keywords</h3>
			<input type="text" placeholder="Enter keywords, separated by commas" />
		</span>
		<span class="modal-field">
			<h4 class="label">Users</h3>
			<input type="text" placeholder="Enter usernames, separated by commas" />
		</span>
		<span class="modal-field">
			<h4 class="label">Locations</h3>
			<input type="text" placeholder="Enter location" />
			<select style="display:block;">
				<option>within 100km</option>
				<option>within 1000km</option>
			</select>
		</span>
	</div>
</script>

<script type="text/template" id="edit-channel-template">
	<div class="active">
	</div>
</script>

<script type="text/template" id="edit-channel-text-template">
	<span class="modal-field">
		<% if (isGroup) { %>
			<h4 class="label"><%= label %></h3>
		<% } %>
		<input type="text" name="<%= key %>" placeholder="<%= placeholder %>" value="<%= val %>" />
	</span>
</script>

<script type="text/template" id="edit-channel-geo-template">
	<span class="modal-field">
		<h4 class="label"><%= label %></h3>
		<input type="text" name="<%= key %>" placeholder="<%= placeholder %>" />
		<select style="display:block;">
			<option>within 100km</option>
			<option>within 1000km</option>
		</select>
	</span>
</script>

<script type="text/template" id="edit-channel-file-template">
	<span class="modal-field has_file">
		<a class="button-primary" href="#">Select file
		<input type="file" name="file">
		</a>
	</span>
</script>

<script type="text/template" id="add-channel-modal-template">
	<div class="modal-title cf">
		<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
		<h1>Add channel</h1>
	</div>
				
	<div class="modal-body modal-tabs-container">
		<div class="base">
			<ul class="modal-tabs-menu">
			<!-- List of available channels goes here -->
			</ul>
			<div class="modal-tabs-window">
				<div id="add-twitter" class="active">
					<div class="modal-field modal-field-tabs-container">
						<ul class="modal-field-tabs-menu">
						</ul>
						<div class="modal-field-tabs-window">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-toolbar">
			<a href="#" class="button-submit button-primary"><span>Add channel</span></a>
		</div>
	</div>
</script>

<script type="text/template" id="edit-channel-modal-template">
	<div class="modal-title cf">
		<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
		<h1>Edit channel</h1>
	</div>
				
	<div class="modal-body modal-tabs-container">
		<div class="base">
			<ul class="modal-tabs-menu">
			<!-- List of available channels goes here -->
			</ul>
			<div class="modal-tabs-window">
				<div id="add-twitter" class="active">
					<div class="modal-field modal-field-tabs-container">
						<ul class="modal-field-tabs-menu">
						</ul>
						<div class="modal-field-tabs-window">
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="modal-toolbar">
			<a href="#" class="button-submit button-primary">Save channel</a>
		</div>
	</div>
</script>