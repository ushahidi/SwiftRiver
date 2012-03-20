$(document).ready(function() {
	// Global Bucket list
	window.Bucket = Backbone.Model.extend({
		defaults: {
			account_id: logged_in_account
		},
		initialize: function() {
						
			// Namespace bucket name if the logged in user is not the owner
			if (parseInt(this.get("account_id")) != logged_in_account) {
				this.set('bucket_name', this.get("account_path") + " / " + this.get("bucket_name"));
			}
		}
	});

	// Collection for all the buckets accessible to the current user
	window.BucketList = Backbone.Collection.extend({
		model: Bucket,
		
		url: buckets_url
		
	});
	
	// View for individual bucket item in a droplet list dropdown
	window.HeaderBucketView = Backbone.View.extend({
		tagName: "li",
				
		template: _.template($("#header-bucket-template").html()),
		
		render: function() {
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		}
	});
	
	window.bucketList = new BucketList();
	
	var HeaderBucketList = Backbone.View.extend({
	
		el: $("#header_dropdown_buckets"),
		
		initialize: function() {
			bucketList.on('add', this.addBucket, this);
			bucketList.on('reset', this.addBuckets, this); 
		},
		
		
		addBucket: function(bucket) {
			var bucketView = new HeaderBucketView({model: bucket});
			this.$(".create-new").before(bucketView.render().el);
		},
		
		addBuckets: function() {
			bucketList.each(this.addBucket, this);
		}
	});
	
	var headerBucketList = new HeaderBucketList();
});