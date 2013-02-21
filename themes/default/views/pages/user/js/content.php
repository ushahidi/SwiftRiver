<script type="text/javascript">
$(function() {
	
	var DashboardAssetView = Backbone.View.extend({
		tagName: "tr",
		
		template: _.template($("#asset-template").html()),
		
		render: function(eventName) {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}
	});
	
	
	var DashboardAssetListView = Backbone.View.extend({
		
		el: '#content',
		
		selectedType: "all",
		
		selectedRole: "all",
		
		events: {
			"click .filters-primary a": "filterByType",
			"click .filters-type a": "filterByCategory",
			"click .container-tabs-menu a": "filterByRole"
		},
		
		initialize: function(options) {
			options.bucketList.on("reset", this.addBuckets, this);
			options.riverList.on("reset", this.addRivers, this);
			
			this.typeMap = {"#all":"all", "#river":"river", "#bucket":"bucket"};
			this.roleMap = {"#all":"all", "#managing":"managing", "#following":"following"};
		},
		
		addBuckets: function() {
			this.applyRolesFilter(this.options.bucketList);
		},
		
		addRivers: function() {
			this.applyRolesFilter(this.options.riverList);
		},

		// Filters the asset list by the selected role
		applyRolesFilter: function(assetList) {
			var filteredList = null;
			switch (this.selectedRole) {
				case "managing":
				filteredList = assetList.own();
				break;
				
				case "following":
				filteredList = assetList.following();
				break;
				
				default:
				filteredList = assetList.models;
			}
			_.each(filteredList, this.addAsset, this);
		},

		addAsset: function(asset) {
			if (asset instanceof Assets.Bucket) {
				asset.set("asset_type", "bucket");
			} else if (asset instanceof Assets.River) {
				asset.set("asset_type", "river");
			}
			var view = new DashboardAssetView({model: asset});
			this.$("#asset-list").prepend(view.render().el);
		},
		
		filterByType: function(ev) {
			var parentEl = $(ev.currentTarget).parent();
			if (parentEl.hasClass("active"))
				return false;

			$(ev.currentTarget).parents("li").siblings().removeClass("active");
			parentEl.addClass("active");

			// Get the hash
			var propHash = $(ev.currentTarget).prop('hash');
			this.selectedType = this.typeMap[propHash];
			
			this.updateAssetList();

			return false;
		},
		
		filterByCategory: function(ev) {
			$(ev.currentTarget).parent().toggleClass("active");
			return false;
		},
		
		filterByRole: function(ev) {
			var parentEl = $(ev.currentTarget).parent();
			if (parentEl.hasClass("active"))
				return false;

			$(ev.currentTarget).parents("li").siblings().removeClass("active");
			parentEl.addClass("active");
			
			var role = $(ev.currentTarget).prop('hash');
			this.selectedRole = this.roleMap[role];
			
			this.updateAssetList();

			return false;
		},
		
		// Applies a first pass filter on the asset list based on type
		// before moving on to the other filters
		updateAssetList: function() {
			// Clone the collections
			var riverList = this.options.riverList.clone(),
				bucketList = this.options.bucketList.clone();
			
			// Empty the asset list
			this.$("#asset-list").empty().hide();
			
			switch (this.selectedType) {
				case "river":
					this.options.riverList.reset(riverList.models);
				break;
				
				case "bucket":
					this.options.bucketList.reset(bucketList.models);
				break;
				
				default:
					this.options.bucketList.reset(bucketList.models);
					this.options.riverList.reset(riverList.models);
			}
			
			this.$("#asset-list").fadeIn('slow');
		}
		
	});
	
	new DashboardAssetListView({
		bucketList: Assets.bucketList,
		riverList: Assets.riverList
	});
});
</script>