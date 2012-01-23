<div id="channels">
    <div id="messages"></div>
    <div class="controls">
        <div class="row cf">
            <h2><?php echo __('Channels'); ?></h2>
            <div class="tab-controls cf">
                <ul class="tabs"></ul>
                
                <div class="tab-container">
                </div>
            </div>
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