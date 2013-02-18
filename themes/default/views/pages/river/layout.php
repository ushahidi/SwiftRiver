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
});

</script>

<hgroup class="page-title cf">
	<div class="center">
		<div class="col_9">
			<h1><?php print $page_title; ?></h1>
		</div>
		<div class="page-action col_3">
			<!-- IF: User manages this river -->
			<a href="settings.php" class="button button-white settings"><span class="icon-cog"></span></a>
			<a href="#" class="button button-primary filters-trigger"><i class="icon-filter"></i>Filters</a>
			<!-- ELSE IF: User follows this river
			<a href="#" class="button-follow selected button-primary"><i class="icon-checkmark"></i>Following</a>
			! ELSE
			<a href="#" class="button-follow button-primary"><i class="icon-checkmark"></i>Follow</a>
			-->				
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
								<?php echo $item['label'];?>
							</a>
						</li>
						<?php endforeach; ?>
					</ul>
					<div class="filters-type">
						<ul>
							<li><a href="#"><span class="total">39</span> Unread</a></li>
							<li><a href="#"><span class="total">165</span> Read</a></li>
						</ul>
					</div>
				
					<div class="filters-type">
						<span class="toggle-filters-display"><span class="total">5</span><span class="icon-arrow-down"></span><span class="icon-arrow-up"></span></span>				
						<span class="filters-type-settings"><a href="#"><span class="icon-cog"></span></a></span>
						<h2>Channels</h2>
						<div class="filters-type-details">
							<ul>
								<li class="active"><a href="#"><i class="icon-twitter"></i><span class="total">28</span> @Mainamshy, @rkulei...</a></li>
								<li class="active"><a href="#"><i class="icon-facebook"></i><span class="total">61</span> DailyNation, KTNKenya</a></li>
								<li class="active"><a href="#"><i class="icon-rss"></i><span class="total">83</span> The Kenyan Post</a></li>
								<li class="active"><a href="#"><i class="icon-rss"></i><span class="total">14</span> African Press</a></li>
								<li class="active"><a href="#"><i class="icon-rss"></i><span class="total">19</span> Standard Media</a></li>
							</ul>
						</div>
					</div>
							
					<div class="filters-type">
						<ul>
							<li class="active"><a href="#"><span class="remove icon-cancel"></span><i class="icon-calendar"></i>November 1, 2012 to present</a></li>
							<!--li class=""><a href="#"><span class="remove icon-cancel"></span><i class="icon-pencil"></i>hate, robbed</a></li-->
						</ul>
						<a href="/markup/_modals/add-search-filter.php" class="button-add modal-trigger"><i class="icon-search"></i>Add search filter</a>				
					</div>

					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-close">Done</a>				
					</div>
				</div>
			</div>
		</section>
			
		<?php echo $droplets_view; ?>
	</div>
</div>

<script type="text/template" id="channels-modal-template">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
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
		</div>
		<div id="modal-secondary" class="modal-view">
			<!-- START: Edit channel 1 -->				
			<div id="edit-channel-1" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Edit channel</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<h2 class="label">Twitter</h2>

						<div class="modal-field modal-field-tabs-container">
							<ul class="modal-field-tabs-menu">
								<li class="active"><a href="#input-keywords">Keywords</a></li>
								<li><a href="#input-users">Users</a></li>
								<li><a href="#input-location">Location</a></li>
							</ul>
							<div class="modal-field-tabs-window">
								<div id="input-keywords" class="active">
									<a href="#" class="add-field"><span class="icon-plus"></span></a>									
									<input type="text" placeholder="Enter keywords, separated by commas" />
								</div>
								<div id="input-users">
									<a href="#" class="add-field"><span class="icon-plus"></span></a>									
									<input type="text" placeholder="Enter usernames, separated by commas" />
								</div>
								<div id="input-location">
									<a href="#" class="add-field"><span class="icon-plus"></span></a>									
									<input type="text" placeholder="Enter location" />
									<select style="display:block;">
										<option>within 100km</option>
										<option>within 1000km</option>
									</select>
								</div>																				
							</div>
							
							<!-- IF: Parameter added -->
							<div class="modal-field-parameter">									
								<select style="display:block;">
									<option>AND</option>
									<option>OR</option>
								</select>
								
								<input type="text" value="SXSW" />
							</div>
	
							<div class="modal-field-parameter">									
								<select style="display:block;">
									<option>AND</option>
									<option>OR</option>
								</select>
								
								<input type="text" value="Austin, TX" />
								<select style="display:block;">
									<option>within 100km</option>
									<option>within 1000km</option>
								</select>
							</div>														
							<!-- -->
						</div>
					</div>			
				</div>
			</div>
			
			<!-- START: Edit channel 2 -->				
			<div id="edit-channel-2" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Edit channel</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<h2 class="label">Facebook</h2>
						<div class="modal-field">
							<h3 class="label">Facebook Page name</h3>
							<a href="#" class="add-field"><span class="icon-plus"></span></a>
							<input type="text" placeholder="Enter the name of a Facebook page" />
							<!-- IF: Parameter added -->
							<div class="modal-field-parameter">									
								<select style="display:block;">
									<option>AND</option>
									<option>OR</option>
								</select>
								
								<input type="text" value="SwiftRiver" />
							</div>
	
							<div class="modal-field-parameter">									
								<select style="display:block;">
									<option>AND</option>
									<option>OR</option>
								</select>
								
								<input type="text" value="SXSW" />
							</div>													
							<!-- -->
						</div>
					</div>			
				</div>
			</div>
			
			<!-- START: Edit channel 3 -->				
			<div id="edit-channel-3" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Edit channel</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<h2 class="label">RSS</h2>
						<div class="modal-field">
							<h3 class="label">RSS URL</h3>
							<input type="text" value="http://mashable.com/rss" />
						</div>
					</div>			
				</div>
			</div>						
		
		</div>
	</div>
</script>

<script type="text/template" id="channel-modal-template">
	<a href="#edit-channel-1" class="modal-transition">
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
		<input type="text" name="<%= key %>" placeholder="<%= placeholder %>" />
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
			<a href="#" class="button-submit button-primary modal-back">Add channel</a>				
		</div>					
	</div>
</script>