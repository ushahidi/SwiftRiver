/**
 * Backbone.js wiring for the droplets MVC
 */
(function() {
	// Droplet model
	window.Droplet = Backbone.Model.extend();

	// Droplet collection
	window.DropletCollection = Backbone.Collection.extend({
		model: Droplet,
		url: "<?php echo $fetch_url; ?>"
	});

	// Rendering for a list of droplets
	window.DropletListView = Backbone.View.extend({
		
		el: $("#droplet-list"),
		
		initialize: function() {
			this.model.bind("reset", this.render, this);
		},
		
		render: function(eventName) {
			_.each(this.model.models, function(droplet) {
				$(this.el).append(new DropletListItem({model: droplet}).render().el);
			}, this);
		}
	});


	// Rendering for a single droplet in the list view
	window.DropletListItem = Backbone.View.extend({
		
		tagName: "article",
		
		className: "droplet cf",
		
		template: _.template($("#droplet-list-item").html()),
		
		events: {
			"click .detail-view": "showDetail"
		},
		
		render: function(eventName) {
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		},
		
		showDetail: function() {
			// TODO: Toggle the state of the "view more" button
			// TODO: Display the droplet detail + tags
			// console.log(this.model.toJSON().droplet_content);
			return false;
		}
	});


	// // Rendering for the droplet detail
	// window.DropletDetailView = Backbone.View.extend({
	// 	el: $("#droplet-view"),
	// 	template: _.template($("#droplet-details").html()),
	// 	render: function(eventName) {
	// 		$(this.el).html(this.template(this.model.toJSON()));
	// 		return this;
	// 	}
	// });
	// 

	var AppRouter = Backbone.Router.extend({
		routes: {
			"" : "list",
			"droplet/index/:id" : "dropletDetails"
		},
	
		list: function() {
			this.dropletList = new DropletCollection();
			this.dropletListView = new DropletListView({model: this.dropletList});
			this.dropletList.fetch();
		},
	
		dropletDetails: function(id) {
			this.droplet = this.dropletList.get(id);
			this.dropletDetailView = new DropletDetailView({model: this.droplet});
			this.dropletDetailView.render();
		}
	});

	var app = new AppRouter();
	Backbone.history.start();
})();