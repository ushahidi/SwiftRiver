<script type="text/javascript">
$(function() {
	
	// Container for an assets in the combined DashboardAssetList
	var DashboardAsset = Assets.Asset.extend();
	
	// Combined Asset List
	var DashboardAssetList = Assets.AssetList.extend({
		comparator: function (asset) {
			return asset.get("asset").get('name').toLowerCase();
		},
		
		selected: function() {
			return this.filter(function(asset) { 
				return asset.get("selected");
			});
		},
	});
	
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
				
				case "#form":
					view = new Assets.FormModalView({model: new Assets.Form(), collection: Assets.formList});
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
			"change .select-toggle input[type=checkBox]": "setSelected",
			"click .item-summary a": "showEditForm"
		},
		
		template: _.template($("#asset-template").html()),
		
		initialize: function(options) {
			this.model.get("asset").on("change", this.render, this);
		},
		
		render: function(eventName) {
			var data = this.model.get("asset").toJSON();
			data["type"] = this.model.get("type");
			this.$el.html(this.template(data));
			return this;
		},
		
		setSelected: function() {
			this.model.set("selected", !this.model.get("selected"));
		},
		
		showEditForm: function () {
			var asset = this.model.get("asset");
			if (asset instanceof Assets.Form) {
				var view = new Assets.FormModalView({model: asset, collection: Assets.formList});
				modalShow(view.render().el);
				return false;
			}
		}
	});
	
	
	var DashboardAssetListView = Backbone.View.extend({
		
		el: '#content',
		
		selectedType: "all",
		
		selectedRole: "all",
		
		events: {
			"click .filters-primary a": "filterByType",
			"click .filters-type a": "filterByCategory",
			"click .container-tabs-menu a": "filterByRole",
			"click .container-toolbar a.delete-asset": "confirmDelete",
			"click .create-new a.button-primary": "showCreateAsset",
			"click .container-toolbar a.uncollaborate": "confirmUnCollaborate",
		},
		
		initialize: function(options) {
			this.collection = new DashboardAssetList();
			
			// Bind to global River, Bucket and Form list events
			options.bucketList.on("reset", this.addAssets, this);
			options.riverList.on("reset", this.addAssets, this);
			options.formList.on("reset", this.addAssets, this);
			options.bucketList.on("add", this.addAsset, this);
			options.riverList.on("add", this.addAsset, this);
			options.formList.on("add", this.addAsset, this);
			
			// Render asset when added to combined list
			this.collection.on("add", this.renderAsset, this);
		},
		
		addAssets: function(assets) {
			assets.each(this.addAsset, this);
		},
		
		addAsset: function(asset) {
			var dbAsset = new DashboardAsset({
				asset: asset
			});
			
			if (asset instanceof Assets.Bucket) {
				dbAsset.set("type", "bucket");
			} else if (asset instanceof Assets.River) {
				dbAsset.set("type", "river");
			} else if (asset instanceof Assets.Form) {
				dbAsset.set("type", "form");
			}
			
			this.collection.add(dbAsset);
			dbAsset.on("destroy", this.onAssetDeleted, this);
		},
		
		
		// Display an asset sorted into the view
		renderAsset: function(asset) {
			var view = new DashboardAssetView({model: asset});
			asset.view = view;
			
			var index = this.collection.indexOf(asset);
			if (index > 0) {
				// Insert assets after those they followin in the list.
				this.collection.at(index-1).view.$el.after(view.render().el);
			} else {
				// First asset is simply appended to the view
				this.$("#asset-list").append(view.render().el);
			}

			asset.on("destroy", function() {
				view.$el.fadeOut().remove();
			});
		},
		
		// Remove a deleted asset from view
		onAssetDeleted: function(asset) {
			asset.view.$el.fadeOut("slow", function() { $(this).remove(); })
		},
		
		// Return true if the asset matched the current type filter
		matchTypeFilter: function(asset) {
			var match = true;
			
			// Apply type filter
			switch (this.selectedType) {
				case "river":
					match = asset instanceof Assets.River;
					break;
				case "bucket":
					match = asset instanceof Assets.Bucket;
					break;	
				case "form":
					match = asset instanceof Assets.Form;
					break;
			}
			
			return match;
		},
		
		// Return true if the asset matched the current role filter
		matchRoleFilter: function(asset) {
			var match = true;
			
			// Apply role filter
			switch (this.selectedRole) {
				case "managing":
					this.$(".container-toolbar a.delete-asset").show();
					this.$(".container-toolbar a.uncollaborate").hide();
					match = asset.get("is_owner");
					break;

				case "following":
					this.$(".container-toolbar a.delete-asset").hide();
					this.$(".container-toolbar a.uncollaborate").hide();
					match = asset.get("following");
					break;
				
				case "collaborating":
					this.$(".container-toolbar a.delete-asset").hide();
					this.$(".container-toolbar a.uncollaborate").show();
					match = asset.get("is_collaborator");
					break;
				
				default:
					this.$(".container-toolbar a.uncollaborate").hide();
			}
			
			return match;
		},
		
		// Show assets that match current filters otherwise hide them
		filterView: function() {
			if (!this.collection.size())
				return;
			
			this.collection.each(function(dbAsset) {
				// If true, display the asset otherwise hide it
				var isSelected = true;
				var asset = dbAsset.get("asset");
				
				if (this.matchTypeFilter(asset) && this.matchRoleFilter(asset)) {
					if (this.selectedRole == "collaborating" && asset.get("is_collaborator")) {
						$(".select-toggle", dbAsset.view.$el).html($("<input>").attr("type", "checkbox"));
					}
					dbAsset.view.$el.fadeIn("slow");
				} else {
					dbAsset.view.$el.fadeOut("slow");
				}
				
			}, this);
		},
		
		// Update the current type filter
		filterByType: function(ev) {
			var parentEl = $(ev.currentTarget).parent();
			if (parentEl.hasClass("active"))
				return false;

			$(ev.currentTarget).parents("li").siblings().removeClass("active");
			parentEl.addClass("active");

			// Get the hash
			var propHash = $(ev.currentTarget).prop('hash');
			this.selectedType = propHash.substring(1);
			
			this.filterView();

			return false;
		},
		
		// Update the current category filter
		filterByCategory: function(ev) {
			$(ev.currentTarget).parent().toggleClass("active");
			return false;
		},
		
		// Update the current role filter
		filterByRole: function(ev) {
			var parentEl = $(ev.currentTarget).parent();
			if (parentEl.hasClass("active"))
				return false;

			$(ev.currentTarget).parents("li").siblings().removeClass("active");
			parentEl.addClass("active");
			
			var role = $(ev.currentTarget).prop('hash');
			this.selectedRole = role.substring(1);
			
			this.filterView();

			return false;
		},
		
		// Show a delete confirmation window
		confirmDelete: function() {
			if (!this.collection.selected().length)
				return false;
				
			// Show confirmation window
			new ConfirmationWindow("<?php echo __("Are you sure you want to delete the selected items?"); ?>", 
				this.deleteAssets, this).show();
			
			return false;
		},
		
		// Deletes all currently selected assets
		deleteAssets: function() {
			var selection = this.collection.selected();
			var success = true;
			_.each(selection, function(dbAsset) {
				var asset = dbAsset.get("asset");
				
				asset.destroy({
					wait: true,
					async: false,
					success: function() {
						dbAsset.trigger("destroy", dbAsset);
					},
					error: function() {
						success = false;
					}
				});
			}, this);
			
			if (success) {
				var message = selection.length + " <?php echo __(" item(s) successfully deleted!"); ?>";
				showSuccessMessage(message, {flash: true});
			} else {
				showFailureMessage("Some items could not be deleted. Try again later.");
			}
		},
		
		showCreateAsset: function() {
			modalShow(new CreateAssetModal().render().el);
			return false;
		},
		
		confirmUnCollaborate: function(e) {
			if (!this.collection.selected().length)
				return false;
			
			new ConfirmationWindow(
				"<?php echo __("Are you sure you want to stop collaborating on the selected items?"); ?>",
				this.uncollaborate, this).show();
			return false;
		},
		
		uncollaborate: function() {
			var selection = this.collection.selected(),
				success = true;
			
			_.each(selection, function(dbAsset) {
				var asset = dbAsset.get("asset");
				var urlRoot = asset.get("url")+ "/collaborators";
				var AssetCollaborator = Backbone.Model.extend({urlRoot: urlRoot});
				
				var collaborator = new AssetCollaborator({
					id: <?php echo $account_id; ?>, 
				});
				
				collaborator.destroy({
					wait: true,
					async: false,
					success: function() {
						dbAsset.trigger("delete", dbAsset);
					},
					
					error: function() {
						success = false;
					}
				});
				
			}, this);
			
			if (success) {
				var message = "You have successfully stopped collaborating on " + selection.length + "item(s)";
				showSuccessMessage(message, {flash: true});
			} else {
				var message = "<?php echo __("Oops! An error occurred while removing you "
				. "from the list of collaborators of some of the selected items. Please try again later."); ?>";
				showFailureMessage(message);
			}
		}
	});
	
	<?php if ($owner): ?>
		new DashboardAssetListView({
			bucketList: Assets.bucketList,
			riverList: Assets.riverList,
			formList: Assets.formList
		});
	<?php else: ?>
		var bucketList = new Assets.BucketList();
	    var riverList = new Assets.RiverList();
		var formList = new Assets.FormList();

		new DashboardAssetListView({
			bucketList: bucketList,
			riverList: riverList,
			formList: formList
		});
	
		bucketList.reset(<?php echo $buckets; ?>);
		riverList.reset(<?php echo $rivers; ?>);
		formList.reset(<?php echo $forms; ?>);
	<?php endif; ?>
});
</script>