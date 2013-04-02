<script type="text/javascript">
	$(function() {
		
		var baseURL = "<?php echo $fetch_base_url; ?>";

		// Boolean that is true when in photos view
		var photos = <?php echo $photos; ?>;
		
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
			},
			
			getDropListUrl: function(applyFilters) {
				var f = "";

				if (applyFilters) {
					f = filters.getString();
				}

				return baseURL + "/droplets" + (f.length > 0 ? '?' + f : '');
			}
		});
		var filters = window.filters = new Filter(<?php echo $filters; ?>);
		
		// Global drop lists
		var dropsList = window.dropsList = new Drops.DropsList;
		var newDropsList = window.newDropsList = new Drops.DropsList;
		
		dropsList.url = newDropsList.url = filters.getDropListUrl(true);

		// Load content while scrolling - Infinite Scrolling
		var pageNo = 1;
		var maxId = 0;
		var isAtLastPage = false;
		var isPageFetching = false;

		var loading_msg = window.loading_message.clone();

		$(window).bind("scroll", function() {
			bottomEl = $("#drop_listing_bottom");

			if (!bottomEl.length)
				return;

			if (nearBottom(bottomEl) && !isPageFetching && !isAtLastPage) {
				// Advance page and fetch it
				isPageFetching = true;
				pageNo += 1;

				// Show a loading message after a delay if fetch takes long.
				var t = setTimeout(function() { $(".stream-message.waiting").fadeIn("slow"); }, 500);

				dropsList.fetch({
					url: filters.getDropListUrl(true),
				    data: {
				        page: pageNo,
				        max_id: maxId,
				        photos: photos
				    }, 
				    update: true,
					remove: false,
				    success: function(collection, response, options) {
						// Re-enable scrolling after a delay
						setTimeout(function(){ isPageFetching = false; }, 700);
						clearTimeout(t);
				        $(".stream-message.waiting").fadeOut("normal");
						$(".filters-primary li span.total").html(collection.length);
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
			if (parseInt(droplet.get("id")) > sinceId) {
				sinceId = parseInt(droplet.get("id"));
			}
		}, this);

		// Poll for new drops every 30 seconds
		setInterval(function() {
			if (!isSyncing) {
				isSyncing = true;
				newDropsList.fetch({data: {since_id: sinceId, photos: photos}, 
				    update: true,
					remove: false,
				    complete: function () {
				        isSyncing = false;
				    }
				});   
			}		    
		}, 30000 + Math.floor((Math.random()*30000)+1));

		// Bootstrap the droplet list
		dropsList.reset(<?php echo $droplet_list; ?>);

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
				filters.on("change", this.filterUpdated, this);
			},

			listingDone: false,

			// Cache for the drops view to unbinding event handlers
			// when the view changes.
			view: null,

			resetView: function() {
				var noticeContent = $("#system_notification", "#content");

				$("#stream").empty();
				if (noticeContent !== null) {
					$("#stream").append(noticeContent);
				}
				modalHide();
				zoomHide();
				dropsList.off(null, null, this.view);
				this.view = null;
			},

			getView: function (layout) {
				if (!this.view) {
					this.view = window.dropsView = new Drops.DropsView({layout: layout, 
													dropsList: dropsList, 
													newDropsList: newDropsList,
													baseURL: baseURL,
													maxId: maxId,
													router: this,
													filters: filters});
					this.view.render();
					this.view.initDrops();
				}
				return this.view.el;
			},

			dropsView: function() {
				this.activateNavigationLink("#drops-navigation-link");
				this.resetView();		
				$("#stream").append(this.getView("drops"));
				this.view.masonry();
				this.listingDone = true;

				// Apply filter parameters to the navigation if any
				if (!filters.isEmpty()) {
					this.setFilter(filters.getString(), true, false);
				}
			},

			listView: function() {
				this.activateNavigationLink("#list-navigation-link");
				this.resetView();
				$("#stream").append(this.getView("list"));
				this.listingDone = true;

				// Apply filter parameters to the navigation if any
				if (!filters.isEmpty()) {
					this.setFilter(filters.getString(), true, false);
				}
			},

			photosView: function() {
				this.activateNavigationLink("#photos-navigation-link");
				this.resetView();		
				$("#stream").append(this.getView("photos"));
				this.view.masonry();
				this.listingDone = true;

				// Apply filter parameters to the navigation if any
				if (!filters.isEmpty()) {
					this.setFilter(filters.getString(), true, false);
				}
			},
			
			activateNavigationLink: function(hash) {
				var selector = $("ul.filters-primary li");
				selector.removeClass("active");
				$("span.total", selector).hide();
				
				$(hash).addClass("active");
				$(hash + " span.total").show();
			},

			dropFullView: function(id) {
				this.resetView();
				var drop = dropsList.get(id);
				if (!drop) {
					// Drop not in the local collection, request it from the server
					drop = new Drop({id: id});
					drop.urlRoot = filters.getDropListUrl(false);
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
				$("#zoom-container a.zoom-close").bind("click", function(e) {
					appRouter.navigate(layout);
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

			setFilter: function(query, repl, trigger) {
				var fragment = "list";
				if (this.view.options.layout !=  undefined) {
					fragment = this.view.options.layout;
				} 
				if (query) {
					fragment += '?' + query;
				}
				this.navigate(fragment, {trigger: true, replace: repl});
			},
			
			filterUpdated: function(filter) {
				var router = this;
				dropsList.fetch({
					url: filters.getDropListUrl(true),
					data: {photos: photos},
					silent: true,
					success: function(collection, response, options) {
						$(".filters-primary li span.total").html(collection.length);
						router.setFilter(filter.getString(), true, true);
					}
				});
			}
		});

		// Initiate the router
		var appRouter = new AppRouter;
		// Start Backbone history
		Backbone.history.start({pushState: true, root: baseURL + "/"});
		
		// Drop state filter
		new Drops.DropsStateFilterView({
			dropFilters: filters,
			dropsList: dropsList
		});

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
