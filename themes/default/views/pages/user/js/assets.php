<script type="text/javascript">
$(function() {
	
	var visited_account = <?php echo $visited_account->id; ?>;
	
	var AssetView = Assets.BaseAssetView.extend({
		template: _.template($("#profile-asset-list-item-template").html())
	});
	
	var AssetListView = Assets.BaseAssetListView.extend({
		
		el: $("#assets"),
		
		listSelector: '.asset-list',
			
		isCreator: function(asset) {
			// Override creator determination for when visited account != logged_in_account
			return asset.get("is_owner") && parseInt(asset.get("account_id")) == visited_account;
		},
		
		isCollaborator: function(asset) {
			// Override collaborator determination for when visited account != logged_in_account
			return asset.get("is_owner");
		},
		
		getView: function(asset) {
			return new AssetView({model: asset});
		},
		
		renderOwn: function(view) {
			this.$(".own section.property-parameters").append(view.render().el);
			this.$(".own").show();
		},
		
		renderCollaborating: function(view) {
			this.$(".collaborating section.property-parameters").append(view.render().el);
			this.$(".collaborating").show();
		},
		
		renderFollowing: function(view) {
			this.$(".following section.property-parameters").append(view.render().el);
			this.$(".following").show();
		},
		
		subscriptionChanged: function(model, subscribed) {
			Assets.BaseAssetListView.prototype.subscriptionChanged.call(this, model, subscribed);
			
			if (model.previous('subscribed') && model.previous("collaborator") &&
				logged_in_account == visited_account &&
				!this.collection.collaborating().length) {
				// Last collaborating item was removed from the view
				$(".collaborating").fadeOut("slow");
			}
			
			// Only remove from view if on logged_in_accounts profile or if the
			// item is not public
			if ((model.previous('subscribed') && model.previous("collaborator") &&
				logged_in_account == visited_account) || ! model.get("public"))
			{
				model.getView(this).$el.fadeOut("slow");
			}
		},
		
		assetDeleted: function(model) {
			if (!this.collection.length) {
				// All items removed
				this.$(".own, .collaborating, .following").hide();
				this.$(".empty-message").show();
			}
			if (!this.collection.own().length) {
				// All owned items removed
				this.$(".own").fadeOut("slow");
			}
		}
	});
	
	<?php if ($owner): ?>
		<?php if ($asset == 'river'): ?>
			new AssetListView({collection: Assets.riverList});
		<?php else: ?>
			new AssetListView({collection: Assets.bucketList});
		<?php endif; ?>
	<?php else: ?>
		<?php if ($asset == 'river'): ?>
			var rivers = new Assets.RiverList();
			new AssetListView({collection: rivers});
			rivers.reset(<?php echo $asset_list; ?>);
		<?php else: ?>
			var buckets = new Assets.BucketList();
			new AssetListView({collection: buckets});
			buckets.reset(<?php echo $asset_list; ?>);
		<?php endif; ?>
	<?php endif; ?>
});
</script>