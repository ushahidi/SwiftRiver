<div class="panel-body">
	<div class="controls">
		<div class="row cf">
			<h2><?php echo __("River Filters"); ?></h2>
			<ul class="channel-options input cf">
				<li>
					<div class="channel-option-input">
						<label><?php echo __("Channel"); ?></label>
						<select name="channel_name" id="channel_name">
							<option value="0"><?php echo __("All Channels"); ?></option>
						<?php foreach ($channel_filters as $channel => $data): ?>
							<option value="<?php echo $channel; ?>"><?php echo ucfirst($channel); ?></option>
						<?php endforeach; ?>
						</select>
					</div>
				</li>
				<li>
					<div class="channel-option-input">
						<label><?php echo __("Tags (People, Organizations etc)"); ?></label>
						<?php echo Form::input("tag_names", "", array('id' => "tag_names",
						    'placeholder' => 'E.g. "Mwai Kibaki", UON, KBC, Fanta'));?>
					</div>
				</li>
				<li>
					<div class="channel-option-input">
						<label><?php echo __("Place Names"); ?></label>
						<?php echo Form::input("place_names", "", array('id' => "place_names",
						    'placeholder' => 'E.g. Nairobi, "Rift Valley", Kampala')); ?>
					</div>
				</li>
			</ul>
		</div>
	</div>

	<div class="row controls-buttons cf">
		<p class="button-go"><a><?php echo __("Apply Filters"); ?></a></p>
		<p class="other"><a><?php echo __("Save and create a new filter"); ?></a></p>
	</div>
</div>


<script type="text/javascript">
// Self-invoking JS block for handling the river filters
(function() {

window.RiverFiltersView = Backbone.View.extend({
	el: $("div.panel-body"),

	initialize: function() {
		this.placeNames = this.$("input#place_names");
		this.tagNames = this.$("input#tag_names");
		this.channelName = this.$("select#channel_name");
	},

	events: {
		// When "Apply Filters" is clicked
		"click .controls-buttons p.button-go > a": "applyFilters"
	},

	applyFilters: function(e) {
		var riverFilters = {};

		// Place names
		if ($(this.placeNames).val().length > 0)
		{
			riverFilters["places"] = $(this.placeNames).val();
		}

		// Tag names
		if ($(this.tagNames).val().length > 0)
		{
			riverFilters["tags"] = $(this.tagNames).val();
		}

		// Channel
		if ($(this.channelName).val() != 0)
		{
			riverFilters["channel"] = $(this.channelName).val();
		}

		// Apply the filters the droplets
		if (_.size(riverFilters) > 0)
		{
			dropletList.filterDroplets(riverFilters);
		}

		return false;
	}
});
// Bootstrap the view
window.riverFiltersView = new RiverFiltersView;
})(); 
</script>