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
		
		events: {
			"click .filters-primary a": "filterByType",
			"click .filters-type a": "filterByCategory",
			"click .container-tabs-menu a": "filterByRole"
		},
		
		initialize: function(options) {
			options.bucketList.on("reset", this.addBuckets, this);
			options.riverList.on("reset", this.addRivers, this);
		},
		
		addBuckets: function() {
			this.options.bucketList.each(this.addAsset, this);
		},
		
		addRivers: function() {
			this.options.riverList.each(this.addAsset, this);
		},
		
		addAsset: function(asset) {
			if (asset instanceof Assets.Bucket) {
				asset.set("asset_type", "bucket");
			} else if (asset instanceof Assets.River) {
				asset.set("asset_type", "river");
			}
			var view = new DashboardAssetView({model: asset});
			this.$("#asset-list").after(view.render().el);
		},
		
		filterByType: function(ev) {
			$(ev.currentTarget).parents("li").siblings().removeClass("active");
			$(ev.currentTarget).parent().addClass("active");
			return false;
		},
		
		filterByCategory: function(ev) {
			$(ev.currentTarget).parent().toggleClass("active");
			return false;
		},
		
		filterByRole: function(ev) {
			$(ev.currentTarget).parents("li").siblings().removeClass("active");
			$(ev.currentTarget).parent().addClass("active");
			return false;
		}
		
	});
	
	new DashboardAssetListView({
		bucketList: Assets.bucketList,
		riverList: Assets.riverList
	});
});
</script>