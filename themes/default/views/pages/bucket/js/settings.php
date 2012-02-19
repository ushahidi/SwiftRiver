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

	// View for the bucket settings
	window.BucketSettingsView = Backbone.View.extend({

		el: $("div#settings"),

		initialize: function() {
			this.model = new Bucket(<?php echo $bucket_data; ?>);
			var bucket = this.model;

			this.$("input[name='bucket_publish']").each(function(){
				if ($(this).val() == bucket.get("bucket_publish")) {
					$(this).attr("checked", "checked");
				} else {
					$(this).removeAttr("checked");
				}
			});
		},

		// Events
		events: {
			// When the bucket is renamed
			"click button#rename_bucket": "renameBucket",

			// When the privacy settings are changed
			"click input[name='bucket_publish']": "toggleBucketPrivacy",

			"click .actions li.confirm > a" : "deleteBucket"
		},

		renameBucket: function(e) {
			// Get the name the bucket
			var bucketName = $("input#bucket_name").val();
			if ($.trim(bucketName).length > 0 && this.model.get("bucket_name") != bucketName) {
				// Save the new river name
				this.model.save({name_only: true, bucket_name: bucketName}, {
					wait: true,
					success: function(model, response) {
						if (response.success) {
							window.location.href = response.redirect_url;
						}
					}
				});
			}			
		},

		toggleBucketPrivacy: function(e) {
			var radio = $(e.currentTarget);
			if ($(radio).val() != this.model.get("bucket_publish")) {

				// String for the new status of the river
				var newStatus = ($(radio).val() == 1) ? 
				    "<?php echo __('public') ?>" 
				    : "<?php echo __('private'); ?>";

				// Show confirmation dialog before updating
				if (confirm("<?php echo __('Are you sure you want to make this bucket '); ?>" + newStatus + '?')) {

					// Update the privacy settings of the river
					this.model.save({privacy_only: true, bucket_publish: $(radio).val()}, {
						wait: true, 
						success: function(model, response) {
							if (response.success) {
								window.location.href = response.redirect_url;
							}
						}
					});
				} else {
					// Prevent the switch
					e.preventDefault();
				}
			}			
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