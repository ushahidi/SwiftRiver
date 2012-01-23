<script type="text/javascript">
	/**
	 * Backbone.js wiring for the droplets MVC
	 */
	(function() {
		// Tracks the current page number and is used for the infinite scroll
		var pageNo = 1;
	
		// Droplet model
		window.Droplet = Backbone.Model.extend({
			initialize: function() {
				var dropletId = this.toJSON().id;
				this.places = new DropletPlaceCollection;
				this.places.url = '/droplet/index/'+dropletId+'?semantics=places';
			
				this.tags = new DropletTagCollection;
				this.tags.url = '/droplet/index/'+dropletId+'?semantics=tags';
			
				this.links = new DropletLinkCollection;
				this.links.url = '/droplet/index/'+dropletId+'?semantics=link';
			}
		});
	
		// Models for the droplet places, tags and links 
		window.DropletPlace = Backbone.Model.extend();
		window.DropletTag = Backbone.Model.extend();
		window.DropletLink = Backbone.Model.extend();

		// Droplet collection
		window.DropletCollection = Backbone.Collection.extend({
			model: Droplet,
			url: "<?php echo $fetch_url; ?>"
		});
	
		// Collections for droplet places, tags and links
		window.DropletPlaceCollection = Backbone.Collection.extend({
			model: DropletPlace
		})
	
		window.DropletTagCollection = Backbone.Collection.extend({
			model: DropletTag
		});
	
		window.DropletLinkCollection = Backbone.Collection.extend({
			model: DropletTag
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
				return this;
			}
		});


		// Rendering for a single droplet in the list view
		window.DropletListItem = Backbone.View.extend({
		
			tagName: "article",
		
			className: "droplet cf",
		
			template: _.template($("#droplet-list-item").html()),
		
			events: {
				"click a.detail-view": "showDetail"
			},
		
			render: function(eventName) {
				$(this.el).html(this.template(this.model.toJSON()));
				return this;
			},
		
			showDetail: function(event) {
				// Toggle the state of the "view more" button
				$(event.currentTarget).toggleClass("detail-hide");
			
				var dropletId = this.model.toJSON().id;
				var _obj = $("#detail-section-"+dropletId);
			
				// Display the droplet detail
				if ($(event.currentTarget).hasClass("detail-hide")) {
					_obj.slideDown(200);
				
					var dropletView = new DropletDetailView({model: this.model});
					$("#droplet-view", this.el).html(dropletView.render().el);
				
					// Fetch and render the places
					this.model.places.fetch();
					var placeView = new SemanticsView({
						el: $("#droplet-locations-"+dropletId),
						model: this.model.places,
						itemTagName: "li",
						itemTemplate: "#droplet-place-item"
					});
					placeView.render();
				
					// Fetch and render the tags
					this.model.tags.fetch();
					var tagView = new SemanticsView({
						el: $("#droplet-tags-"+dropletId),
						model: this.model.tags,
						itemTagName: "li",
						itemTemplate: "#droplet-tag-item"
					});
					tagView.render();
				
					// Render the links
					this.model.links.fetch();
					var linksView = new SemanticsView({
						el: $("#droplet-links-"+dropletId),
						model: this.model.links,
						itemTagName: "li",
						itemTemplate: "#droplet-link-item"
					});
					linksView.render();
				
				} else {
					_obj.slideUp(50);
					_obj.hide();
				}
			
				return false;
			}
		});


		// View for the droplet detail
		window.DropletDetailView = Backbone.View.extend({

			tagName: "article",
		
			className: "fullstory",
		
			template: _.template($("#droplet-details").html()),
		
			render: function(eventName) {
				$(this.el).html(this.template(this.model.toJSON()));
				return this;
			}
		});
	
		// 
		// Views for the tags, places and link 
		// 
		window.SemanticsView = Backbone.View.extend({
		
			initialize: function() {
				this.model.bind("reset", this.render, this);
			},
		
			render: function(eventName) {
				$(this.el).html("");
				_.each(this.model.models, function(tag) {
					$(this.el).append(new SemanticItemView({
						model: tag, 
						itemTemplate: this.options.itemTemplate, 
						tagName: this.options.itemTagName
					}).render().el);
				}, this);
				return this;
			}
		});
	
		window.SemanticItemView = Backbone.View.extend({
		
			render: function(eventName) {
				this.template = _.template($(this.options.itemTemplate).html());
				$(this.el).html(this.template(this.model.toJSON()));
				return this;
			}
		});
		
		// ------------------------------------------------------------------------
	

		var AppRouter = Backbone.Router.extend({
			routes: {
				"" : "list"
			},
	
			list: function() {
				this.dropletList = new DropletCollection();
				this.dropletListView = new DropletListView({model: this.dropletList});
				this.dropletList.fetch();
			}
		});

		appRouter = new AppRouter();
		Backbone.history.start();
	
		// Load content while scrolling
		$(window).scroll(function() {
			if ($(window).scrollTop() == ($(document).height() - $(window).height())) {
				// Increase the page count
				pageNo += 1;
			
				// Update the droplet collection url with the new page no
				var droplets = new DropletCollection();
				droplets.url = droplets.url + "?page="+pageNo;
			
				// Fetch content and update the view
				listView = new DropletListView({model: droplets});
				droplets.fetch();
				listView.render();
			
			}
		});
	})();
</script>