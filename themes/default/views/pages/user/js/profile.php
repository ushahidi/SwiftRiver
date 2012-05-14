<script type="text/javascript">
$(function() {
	
	var visited_account = <?php echo $visited_account->id; ?>;
	
	var ProfileAssetView = BaseAssetView.extend({
		template: _.template($("#profile-asset-list-item-template").html())
	});
	
	var ProfileAssetListView = BaseAssetListView.extend({
		
		listSelector: '.asset-list',
		
		initialize: function(options) {
			BaseAssetListView.prototype.initialize.call(this, options);
						
			if (this.collection instanceof window.BucketList) {
				this.setElement($("#buckets"));
			} else if (this.collection instanceof window.RiverList) {
				this.setElement($("#rivers"));
			}
		},
		
		isCreator: function(asset) {
			// Override creator determination for when visited account != logged_in_account
			return asset.get("is_owner") && parseInt(asset.get("account_id")) == visited_account;
		},
		
		isCollaborator: function(asset) {
			// Override collaborator determination for when visited account != logged_in_account
			return asset.get("is_owner");
		},
		
		getView: function(asset) {
			return new ProfileAssetView({model: asset});
		},
		
		renderOwn: function(view) {
			this.$(".own-title").after(view.render().el);
			this.$(".own-title").show();
		},
		
		renderCollaborating: function(view) {
			this.$(".collaborating-title").after(view.render().el);
			this.$(".collaborating-title").show();
		},
		
		renderFollowing: function(view) {
			this.$(".following-title").after(view.render().el);
			this.$(".following-title").show();
		}		
	});
	
	var buckets = new BucketList();
	new ProfileAssetListView({collection: buckets});
	buckets.reset(<?php echo $bucket_list; ?>);
	
	var rivers = new RiverList();
	new ProfileAssetListView({collection: rivers});
	rivers.reset(<?php echo $river_list; ?>);
});
</script>