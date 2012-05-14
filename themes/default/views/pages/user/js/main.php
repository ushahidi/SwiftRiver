<script type="text/javascript">
$(function() {
	
	var DashboardAssetView = BaseAssetView.extend({
		template: _.template($("#dashboard-asset-list-item-template").html())
	});
	
	var DashboardAssetListView = BaseAssetListView.extend({
		
		listSelector: '.asset-list',
		
		initialize: function(options) {
			BaseAssetListView.prototype.initialize.call(this, options);
						
			if (this.collection instanceof window.BucketList) {
				this.setElement($("#buckets"));
			} else if (this.collection instanceof window.RiverList) {
				this.setElement($("#rivers"));
			}
		},
		
		getView: function(asset) {
			return new DashboardAssetView({model: asset});
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
		},
				
		subscriptionChanged: function(model, subscribed) {
			BaseAssetListView.prototype.subscriptionChanged.call(this, model, subscribed);

			if (model.previous('subscribed') && model.previous("collaborator")) {
				model.getView(this).$el.fadeOut("slow");
				if (!this.globalCollection.collaborating().length) {
					// Last collaborating item was removed from the view
					this.$(".collaborating-title").fadeOut("slow");
				}
			}
		},
	});
	
	new DashboardAssetListView({collection: window.bucketList});
	new DashboardAssetListView({collection: window.riverList});
});
</script>