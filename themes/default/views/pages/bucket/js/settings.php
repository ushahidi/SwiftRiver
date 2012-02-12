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

	// Check if the JS is to be used in listing mode
	<?php if ($listing_mode): ?>

	window.BucketsCollection = Backbone.Collection.extend({
		model: Bucket
	});


	// List of buckets owned/accessible by the current user
	window.BucketsList = new BucketsCollection();

	// View for the buckets listing
	window.BucketsListView = Backbone.View.extend({

		el: $(".container "),

		initialize: function() {
			BucketsList.on('reset', this.addBuckets, this);
		},

		addBucket: function(bucket) {
			var view = new BucketView({model: bucket});
			$(this.el).append(view.render().el);
		},

		addBuckets: function() {
			BucketsList.each(this.addBucket, this);
		}
	});

	// View for an individual bucket list item
	window.BucketView = Backbone.View.extend({

		tagName: "article",

		className: "item cf",

		template: _.template($("#bucket-list-item-template").html()),

		events: {
			"click .dropdown li.confirm > a": "deleteBucket"
		},

		deleteBucket: function(e) {
			var view = this;

			// Delete the bucket on the server
			this.model.destroy({wait: true, success: function(model, response) {
				if (response.success) {
					// Remove item from the UI
					view.$(".dropdown").hide();
					$(view.el).hide(800);
				}
			}});
		},

		render: function(eventName) {
			$(this.el).attr("data-bucket-id", this.model.get("id"));
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		}
	});

	// Bootstrap the listing
	window.bucketsListView = new BucketsListView;
	BucketsList.reset(<?php echo $buckets_list ?>);

	<?php else: ?>

	// We're Bucket settings mode

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

	<?php endif; ?>

	
})();
</script>