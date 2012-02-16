<div class="panel-body">
	<div id="settings" class="controls">
		
		<!-- NEW: Privacy control -->
		<?php if ( ! $is_newly_created): ?>
		<div class="row cf">
			<h2><?php echo __("Access to the River"); ?></h2>
			<div class="input">
				<p class="checkbox">
					<label>
						<input type="radio" name="river_public" value="1" checked="checked">
						<?php echo __("Public (Anyone)"); ?>
					</label>
				</p>
				<p class="checkbox">
					<label>
						<input type="radio" name="river_public" value="0">
						<?php echo __("Private (Only People I specifiy)"); ?>
					</label>
				</p>
			</div>
		</div>
		<?php endif; ?>
		<!-- /NEW: Privacy control -->

		<div class="row cf">
			<h2><?php echo __("Channels"); ?></h2>
			<div class="tab-controls cf">
				<ul class="tabs"></ul>				
				<div class="tab-container"></div>
			</div>
		</div>
		
		<div class="row cf">
			<!-- collaborators -->
			<?php echo $collaborators_control; ?>
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
<script type="text/template" id="channel-template">
    <a href="#<%= channel %>">
    	<% var switchClass = (enabled == 1) ? "switch-on" : "switch-off"; %>
        <span class="switch <%= switchClass %>" id="channel-<%= channel %>"></span>
        <span class="label"><%= channel_name %></span>
    </a>
</script>

<!-- template for rendering the options for each channel -->
<script type="text/template" id="channel-option-panel-template">
    <ul class="channel-options input cf"></ul>
</script>

<script type="text/template" id="option-item-header-template">
    <h3>
		<%= label %><span>[<a href="#">&mdash;</a>]</span>
	</h3>
</script>

<script type="text/template" id="option-item-label-template">
	<h3><%= label %></h3>
</script>

<script type="text/template" id="channel-option-template">
	<span><%= data.value %></span>
</script>

<script type="text/template" id="channel-option-control-template">
    <label><%= label %></label>
</script>

<script type="text/template" id="channel-option-control-button-template">
	<button type="button" class="channel-button" disabled="disabled">
		<span><?php echo __("Add"); ?></span>
	</button>
</script>

<script type="text/template" id="channel-option-dropdown-template">
	<select class="select-list"></select>
</script>

<script type="text/template" id="channel-option-input-template">
	<div class="channel-option-input">
		<input type="<%= type %>" name="<%= key %>">
	</div>
</script>

<script type="text/template" id="channel-option-dropdown-item-template">
	<option <%= selected %>><%= value %></option>
</script>

<?php echo $settings_js; ?>
