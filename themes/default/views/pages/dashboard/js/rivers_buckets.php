<script type="text/javascript">
(function() {
	// Check if the JS is to be used in listing mode

	window.ListItem = Backbone.Model.extend({
		urlRoot: "<?php echo $list_item_url_root; ?>"
	});

	window.ListItemsCollection = Backbone.Collection.extend({
		model: ListItem
	});


	// List of buckets owned/accessible by the current user
	window.ListItems = new ListItemsCollection();

	// View for the buckets listing
	window.ListItemsView = Backbone.View.extend({

		el: $(".container "),

		initialize: function() {
			ListItems.on('reset', this.addListItems, this);
		},

		addListItem: function(listItem) {
			var view = new ListItemView({model: listItem});
			$(this.el).append(view.render().el);
		},

		addListItems: function() {
			ListItems.each(this.addListItem, this);
		}
	});

	// View for an individual bucket list item
	window.ListItemView = Backbone.View.extend({

		tagName: "article",

		className: "item cf",

		template: _.template($("#list-item-template").html()),

		events: {
			"click .dropdown li.confirm > a": "deleteListItem"
		},

		deleteListItem: function(e) {
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
			$(this.el).attr("list-item-id", this.model.get("id"));
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		}
	});

	// Bootstrap the listing
	window.listItemsView = new ListItemsView;
	ListItems.reset(<?php echo $list_items ?>);

})();
</script>