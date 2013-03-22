<script type="text/javascript">
$(function() {
	
	var CreateAssetModal = Backbone.View.extend({
		tagName: "article",
		
		className: "modal modal-view",
		
		template: _.template($("#create-asset-modal-template").html()),
		
		events: {
			"click a.modal-transition": "showCreateView"
		},
		
		render: function() {
			this.$el.html(this.template());
			return this;
		},
		
		showCreateView: function(e) {
			var hash = $(e.currentTarget).prop('hash');
			var view = null;
			switch(hash) {
				case "#river":
					view = new Assets.CreateRiverModalView();
				break;
				
				case "#bucket":
					view = new Assets.CreateBucketModalView();
				break;
			}
			
			if (view != null) {
				modalShow(view.render().el);
			}
			return(false);
		}
	});

	var DashboardAssetView = Backbone.View.extend({
		tagName: "tr",
		
		events: {
			"change .select-toggle input[type=checkBox]": "setSelected"
		},
		
		template: _.template($("#asset-template").html()),
		
		render: function(eventName) {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},
		
		setSelected: function() {
			this.$el.toggleClass("row-selected", "");
			if (this.$el.hasClass("row-selected")) {
				this.options.listView.addSelected(this);
			} else {
				this.options.listView.removeSelected(this);
			}

			return false;
		}
	});
	
	
	var DashboardAssetListView = Backbone.View.extend({
		
		el: '#content',
		
		// Selected asset type
		selectedType: "all",
		
		// Selected role
		selectedRole: "all",
		
		// Hashmap of selected assets
		selectedAssets: {},
		
		events: {
			"click .filters-primary a": "filterByType",
			"click .filters-type a": "filterByCategory",
			"click .container-tabs-menu a": "filterByRole",
			"click .container-toolbar a.delete-asset": "deleteAssets",
			"click .create-new a.button-primary": "showCreateAsset"
		},
		
		initialize: function(options) {
			options.bucketList.on("reset", this.addBuckets, this);
			options.riverList.on("reset", this.addRivers, this);
			options.bucketList.on("add", this.addAsset, this);
			options.riverList.on("add", this.addAsset, this);

			// When the list of assets in the nav changes
			Assets.riverList.on("add", this.addAsset, this);
			Assets.bucketList.on("add", this.addAsset, this);

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
			var view = new DashboardAssetView({model: asset, listView: this});
			this.$("#asset-list").prepend(view.render().el);

			asset.on("destroy", function() {
				view.$el.fadeOut().remove();
			});
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
		},
		
		addSelected: function(view) {
			if (this.selectedAssets[view.cid] == undefined) {
				this.selectedAssets[view.cid] = view.model;
			}
		},
		
		removeSelected: function(view) {
			if (this.selectedAssets[view.cid]) {
				delete this.selectedAssets[view.cid];
			}
		},
		
		// Deletes all currently selected assets
		deleteAssets: function() {
			if (_.size(this.selectedAssets) == 0)
				return false;

			// Show confirmation window
			new ConfirmationWindow("<?php echo __("Are you sure you want to delete the selected items?"); ?>", 
				this.confirmDelete, this).show();
			
			return false;
		},
		
		confirmDelete: function() {
			_.each(this.selectedAssets, function(asset, id) {				
				if (asset.get("asset_type") == "bucket") {
					Assets.bucketList.remove(asset);
				} else if (asset.get("asset_type") == "river"){
					Assets.riverList.remove(asset);
				}
				asset.destroy();
			}, this);
			
			var message = _.size(this.selectedAssets) + " <?php echo __("item(s) successfully deleted!"); ?>";

			// Show success message
			showSuccessMessage(message, {flash: true});
			
			// Clear the list of selected items
			this.selectedAssets = {};
		},
		
		showCreateAsset: function() {
			modalShow(new CreateAssetModal().render().el);
			return false;
		}
		
	});
	
	var bucketList = new Assets.BucketList(),
		riverList = new Assets.RiverList();

	new DashboardAssetListView({
		bucketList: bucketList,
		riverList: riverList
	});
	
	bucketList.reset(<?php echo $buckets; ?>);
	riverList.reset(<?php echo $rivers; ?>);
});
</script>