<div class="panel-body">
	<div id="settings" class="controls">
		<div class="row cf">
			<h2><?php echo __("Channels"); ?></h2>
			<div class="tab-controls cf">
				<ul class="tabs"></ul>
				
				<div class="tab-container"></div>
			</div>
		</div>
		
		<div class="row controls cf">
			<!-- collaborators -->
			<h2><?php echo __("Collaborators"); ?></h2>
			<div class="input">
				<h3><?php echo __("Add people to collaborate on this river"); ?></h3>
				<input type="text" placeholder="<?php echo __("+ Type name..."); ?>">
			</div>
			<div class="list-stream"></div>
			<!-- /collaborators -->

			<!-- buttons -->
			<div class="row controls-buttons cf">
				<p class="button-go"><a href="#"><?php echo __("Apply changes"); ?></a></p>
				<p class="other"><a class="close"><?php echo __("Cancel"); ?></a></p>
				<div class="item actions">
					<p class="button-delete button-delete-subtle"><a><?php echo __("Delete River"); ?></a></p>
					<div class="clear"></div>
					<ul class="dropdown">
						<p><?php echo __("Are you sure you want to delete this River?"); ?></p>
						<li class="confirm"><a><?php echo __("Yep"); ?></a></li>
						<li class="cancel"><a><?php echo __("No, never mind."); ?></a></li>
					</ul>
				</div>
			</div>
			<!-- /buttons -->
		</div>
	</div>
</div>


<!-- template for the channel listing -->
<script type="text/template" id="channel-list-item">
    <a href="#<%= channel %>">
        <span class="switch <%= switch_class %>" id="channel-<%= channel %>"></span>
        <span class="label"><%= channel_name %></span>
    </a>
</script>

<!-- template for rendering the options for each channel -->
<script type="text/template" id="channel-panel-view">
    <article id="<%= channel %>" class="tab-content" style="display:none">
        <ul class="channel-options cf"></ul>
    </article>
</script>


<script type="text/template" id="channel-option-item">
    <h3>
        <%= label %>
        <span>[<a href="#">&mdash;</a>]</span>
	</h3>
    <input type="text" value="<%= value %>" />
</script>

<script type="text/template" id="channel-option-listing">
    <a href="#"><span></span><%= label %></a>
</script>

<!--Backbone JS for the UI -->
<?php echo $settings_js; ?>