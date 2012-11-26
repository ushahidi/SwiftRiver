<div id="content" class="settings channels cf">
	<div class="center">
		<div class="col_12">
			<div class="settings-toolbar">
				<p class="button-blue button-small create"><a href="#" class="modal-trigger"><span class="icon"></span>Add channels</a></p>
			</div>

			<div class="alert-message blue" style="display:none;">
				<p><strong>No channels.</strong> You can flow new channels into your river by selecting the "Add channel" button above.</p>
			</div>
			
			<?php Swiftriver_Event::run('swiftriver.template.river.settings.channels', $river); ?>
			
			<!-- CHANNELS WILL GO HERE -->

		</div>
	</div>
</div>

<script type="text/template" id="channels-modal-channel-item-template">
	<input type="checkbox" name="<%= channel %>" <%= added ? "checked" : "" %>/>
	<%= name %>
</script>

<script type="text/template" id="add-channels-modal-template">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1>Add Channel</h1>
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

	<div class="modal-body select-list">
		<form class="channels">
			<!-- CHANNEL LIST WILL GO HERE -->
		</form>
	</div>
</script>

<script type="text/template" id="parameter-template">
<a href="#"><%= label %></a>
</script>

<script type="text/template" id="channel-option-template">
	<label>
		<p class="field"><%= config.label %></p>
		<% if (typeof title !== 'undefined') { %>
			<p class="title"><%= title.substring(0, 19) + (title.length > 20 ? "..." : "") %></p>
		<% } %>
		<%= input %>
		<p class="remove-small actions">
			<span class="icon"></span><span class="nodisplay">Remove</span>
		</p>
	</label>
	<div style="clear: both"></div>

</script>

<script type="text/template" id="channel-template">
	<header class="cf">
		<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay">Remove</span></a>
		<div class="property-title">
			<a href="#" class="avatar-wrap"><img onerror="showDefaultAvatar(this)" src="<?php echo URL::site('media/img'); ?>/channel-<%= channel %>.gif" /></a>
			<h1><%= name %></h1>
			<div class="popover add-parameter">
				<p class="button-white has-icon add"><a href="#" class="popover-trigger"><span class="icon"></span>Add parameter</a></p>
				<ul class="popover-window base">
				</ul>
			</div>
		</div>
	</header>
	<section class="property-parameters channel-options">
	</section>
</script>

<script type="text/javascript">

$(function() {
	
	// Base fetch url
	var baseURL = "<?php echo $base_url; ?>"
    
	// Bootstrap the channel configuration before anything else
	Channels.channelsConfig.reset(<?php echo $channels_config; ?>);
	
	// Bootstrap the channel control
    var channels = new Channels.ChannelList();
    channels.url = baseURL + "/manage"
	new Channels.ChannelsControl({collection: channels, baseURL: baseURL});
	channels.reset(<?php echo $channels; ?>);
});

</script>