<script type="text/javascript">
	$(function() {
		
		var baseURL = "<?php echo $fetch_base_url; ?>"
		
		// Filters 
		var Filter = Backbone.Model.extend({

			getString: function() {
				var query = "";
				var data = this.toJSON();
				for (filter in data) {
					if (query.length) {
						query += "&";
					}
					query += filter + "=" + data[filter];
				}
				return query;
			},

			isEmpty: function() {
				return _.keys(this.attributes).length == 0;
			}
		});
		var filters = new Filter(<?php echo $filters; ?>);
		
		// Global drop lists
		var dropsList = new Drops.DropsList;
		var newDropsList = new Drops.DropsList;
		
		// Set drop list urls optionally applying filters
		function getDropListUrl(applyFilters) {
			var f = "";

			if (applyFilters) {
				f = filters.getString();
			}

			return baseURL + "/droplets" + (f.length > 0 ? '?' + f : '');
		}
		dropsList.url = newDropsList.url = getDropListUrl(true);
				
		// Filters modal window
		var FiltersView = Backbone.View.extend({
			tagName: "article",

			className: "modal",

			template: _.template($("#filters-modal-template").html()),

			events: {
				"click .save-toolbar .button-blue a": "applyFilter",
				"click .save-toolbar .button-blank a": "resetFilter"
			},

			render: function() {
				this.$el.html(this.template({filters: filters, channels: <?php echo $channels; ?>}));
				return this;
			},

			applyFilter: function() {
				if (!this.$('.save-toolbar').hasClass('visible'))
					return false;

				// Prepare a key value pair of data to send to the server
				var data = {};
				this.$("form").find("input[type=text], input[type=date], select").each( function(index, el) {
					var input = $(el);
					var value = $.trim(input.val());
					if (value.length) {
						data[input.attr("name")] = encodeURIComponent(value);
					}
				});

				// If there is a filter or a previous filter has been cleared
				if (_.keys(data).length || (!_.keys(data).length  && !filters.isEmpty())) {

					// Photos view?
					data['photos'] = photos;

					var loading_msg = window.loading_message.clone().append("<span>Applying filter, please wait...</span>");
					var save_toolbar = this.$(".save-toolbar .button-blue, .save-toolbar .button-blank").clone();

					isSyncing = isPageFetching = true
					var view = this;

					// Show a loading message if the GET request takes longer than 500ms
					var t = setTimeout(function() { this.$(".save-toolbar .button-blue, .save-toolbar .button-blank").replaceWith(loading_msg); }, 500);
					$.get(baseURL + "/droplets", data, function(response) {
						// Success, replace the drops list with the new data
						dropsList.reset(response);

						// Update the filter
						filters = new Filter(data);
						appRouter.setFilter(filters.getString(), false);
						dropsList.url = newDropsList.url = getDropListUrl(true);

						// Reset pagination
						pageNo = 1;
						isAtLastPage = false;

						modalHide();
					}, "json")
					.complete(function() {
						isSyncing = isPageFetching = false;

						clearTimeout(t);
						loading_msg.replaceWith(save_toolbar);
					});
				}

				return false;
			},

			resetFilter: function() {
				this.$("form").find("input[type=text], input[type=date], select").each( function(index, el) {
					$(el).val("");
				});	

				this.applyFilter();

				return false;
			}
		})

		// Bind to the filters button
		$("nav.page-navigation div.filter-actions a").click(function () {
			var view = new FiltersView();
			modalShow(view.render().el);
			return false;
		})
		
		// Boolean that is true when in photos view
		var photos = <?php echo $photos; ?>;
		
		// Load content while scrolling - Infinite Scrolling
		var pageNo = 1;
		var maxId = 0;
		var isAtLastPage = false;
		var isPageFetching = false;

		var loading_msg = window.loading_message.clone();

		$(window).bind("scroll", function() {
			bottomEl = $("#next_page_button");

			if (!bottomEl.length)
				return;

			if (nearBottom(bottomEl) && !isPageFetching && !isAtLastPage) {
				// Advance page and fetch it
				isPageFetching = true;
				pageNo += 1;		

				// Hide the navigation selector and show a loading message				
				loading_msg.appendTo(bottomEl).show();

				dropsList.fetch({
				    data: {
				        page: pageNo, 
				        max_id: maxId,
				        photos: photos
				    }, 
				    add: true,
				    complete: function(model, response) {
						// Reanable scrolling after a delay
						setTimeout(function(){ isPageFetching = false; }, 700);
				        loading_msg.fadeOut('normal');
				    },
				    error: function(model, response) {
				        if (response.status == 404) {
				            isAtLastPage = true;
				        }
				    }
				});
		    }
		});

		// New drop polling
		var sinceId = 0;	
		var isSyncing = false;

		// New drops via polling will queue in this list
		newDropsList.on('add', function (droplet) {
			if (parseInt(droplet.get("sort_id")) > sinceId) {
				sinceId = parseInt(droplet.get("sort_id"));
			}
		}, this);

		// Poll for new drops every 30 seconds
		setInterval(function() {
			if (!isSyncing) {
				isSyncing = true;
				newDropsList.fetch({data: {since_id: sinceId, photos: photos}, 
				    add: true, 
				    complete: function () {
				        isSyncing = false;
				    }
				});   
			}		    
		}, 30000 + Math.floor((Math.random()*30000)+1));

		// Bootstrap the droplet list
		dropsList.reset(<?php echo $droplet_list; ?>
			);

		// Set the maxId after inital rendering of droplet list
		maxId = sinceId = <?php echo $max_droplet_id ?>;

		var AppRouter = Backbone.Router.extend({
			routes: {
				"drop/:id" : "dropFullView",
				"drop/:id/zoom" : "dropZoomView",
				"*actions": "defaultRoute"
			},

			initialize: function() {
				this.route(/^drops(\?.+)?$/, "dropsView");
				this.route(/^list(\?.+)?$/, "listView");
				this.route(/^photos(\?.+)?$/, "photosView");
			},

			listingDone: false,

			// Cache for the drops view to unbinding event handlers
			// when the view changes.
			view: null,

			resetView: function() {
				var noticeContent = $("#system_notification", "#content");

				$("#content").empty();
				if (noticeContent !== null) {
					$("#content").append(noticeContent);
				}
				modalHide();
				zoomHide();
				dropsList.off(null, null, this.view);
				this.view = null;
			},

			getView: function (layout) {
				if (!this.view) {
					this.view = new Drops.DropsView({layout: layout, 
													dropsList: dropsList, 
													newDropsList: newDropsList,
													baseURL: baseURL,
													maxId: maxId,
													router: this});
					this.view.render();
					this.view.initDrops();
				}
				return this.view.el;
			},

			dropsView: function() {
				$("#drops-navigation-link").addClass("active");
				$("#list-navigation-link").removeClass("active");
				$("#photos-navigation-link").removeClass("active");
				this.resetView();		
				$("#content").append(this.getView("drops"));
				this.view.masonry();
				this.listingDone = true;

				// Apply filter parameters to the navigation if any
				if (!filters.isEmpty()) {
					this.setFilter(filters.getString(), true);
				}
			},

			listView: function() {
				$("#list-navigation-link").addClass("active");
				$("#drops-navigation-link").removeClass("active");
				$("#photos-navigation-link").removeClass("active");
				this.resetView();
				$("#content").append(this.getView("list"));
				this.listingDone = true;

				// Apply filter parameters to the navigation if any
				if (!filters.isEmpty()) {
					this.setFilter(filters.getString(), true);
				}
			},

			photosView: function() {
				$("#list-navigation-link").removeClass("active");
				$("#drops-navigation-link").removeClass("active");
				$("#photos-navigation-link").addClass("active");
				this.resetView();		
				$("#content").append(this.getView("photos"));
				this.view.masonry();
				this.listingDone = true;

				// Apply filter parameters to the navigation if any
				if (!filters.isEmpty()) {
					this.setFilter(filters.getString(), true);
				}
			},		

			dropFullView: function(id) {
				this.resetView();
				var drop = dropsList.get(id);
				if (!drop) {
					// Drop not in the local collection, request it from the server
					drop = new Drop({id: id});
					drop.urlRoot = getDropletListUrl(false);
					var context = this;
					var callback = this.dropFullView;
					drop.fetch({
						success: function() {
							// Add the drop to the collection only when successful
							dropsList.add(drop);
							// Retry the drop view now that the drop is loaded
							setTimeout(function() { callback.call(context, id); }, 0);
						}
					});
					return;
				}
				var fullView = new Drops.DropFullView({model: drop, baseURL: baseURL}).render();
				fullView.$('.settings-toolbar').remove();
				$("#content").append(fullView.el);
			},

			dropZoomView: function(id) {
				var drop = dropsList.get(id);
				var detailView = new Drops.DropDetailView({model: drop, baseURL: baseURL, router: this});
				var layout = this.view.options.layout;
				zoomShow(detailView.render().el);
				$("#zoom-container div.modal-window").bind("clickoutside", function(event){
					if(!appRouter.listingDone) {
						appRouter.navigate(layout, {trigger: true});
					} else {
						appRouter.navigate(layout);
					}
				});
				return false;
			},

			defaultRoute: function(actions) {
				switch("<?php echo $default_view; ?>") {
					case 'photos':
						this.navigate("photos", {trigger: true, replace: true});
						break;
					case 'list':
						this.navigate("list", {trigger: true, replace: true});
						break;
					case 'drops':
					default:
						this.navigate("drops", {trigger: true, replace: true});
						break;
				}
			},

			setFilter: function(query, repl) {
				var fragment = "list";
				if (this.view.options.layout !=  undefined) {
					fragment = this.view.options.layout;
				} 
				if (query) {
					fragment += '?' + query;
				}
				this.navigate(fragment, {replace: repl});
			}
		});

		// Initiate the router
		var appRouter = new AppRouter;
		// Start Backbone history
		Backbone.history.start({pushState: true, root: baseURL + "/"});

		// Onclick Handlers for Drops/List
		if ( ! photos)
		{
			$("#drops-navigation-link a").click(function() {
				appRouter.navigate('/drops', {trigger: true});
				return false;
			});
			$("#list-navigation-link a").click(function() {
				appRouter.navigate('/list', {trigger: true});
				return false;
			});
		}
	});
</script>
