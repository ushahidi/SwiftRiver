<script type="text/javascript">
/**
 * JavaScript for the bucket listing and settings views
 *
 * @author    Ushahidi Dev Team
 * @package   Swiftriver - https://github.com/ushahidi/Swiftriver_v2
 * @copyright Ushahidi Inc - 2008-2012
 */

(function() {
	
	// Bucket model
	window.Bucket = Backbone.Model.extend({
		urlRoot: "<?php echo $bucket_url_root; ?>"

	});

	// Bucket attached to the settings view
	window.settingsBucket = new Bucket();

	// View for the bucket settings
	window.BucketSettingsView = Backbone.View.extend({

		el: $("div#settings"),

		initialize: function() {
			this.model = settingsBucket;

			// Set the id
			var bucketId = this.$el.data("settings-bucket-id");
			if (typeof bucketId != "undefined") {
				this.model.set({id: bucketId});
			}

		},

		// Events
		events: {
			"click .actions li.confirm > a" : "deleteBucket"
		},

		deleteBucket: function(e) {
			// Delete the bucket form the server
			this.model.destroy({wait: true, success: function(model, response) {
				if (response.success) {
					window.location.href = response.redirect_url;
				}
			}});
		}
	});

	// Boostrap the settings view
	window.settingsView = new BucketSettingsView;
	
})();
</script>