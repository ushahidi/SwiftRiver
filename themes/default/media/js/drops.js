/**
 * Drops module
 */
(function (root) {
	
	// Init the module
	Drops = root.Drops = {};
	
	// Discussion model
	var Discussion = Backbone.Model.extend();
	
	// Discussion list
	var Discussions = Backbone.Collection.extend({
		model: Discussion,
		
		comparator: function (discussion) {
			return parseInt(discussion.get('id'));
		},
	});
	
	// Discussion view
	var DiscussionView = Backbone.View.extend({
		tagName: "article",
	
		className: "drop base cf",
		
		events: {
			// Show/Hide the edit button
			"mouseover": function() { this.$(".drop-body p.remove-small").show(); },
			"mouseout": function() { this.$(".drop-body p.remove-small").hide(); },
			"click p.remove-small": "delete",
		},
		
		initialize: function(options) {
			this.template = _.template($("#discussion-template").html());
		},
				
		render: function(eventName) {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},
		
		delete: function() {
			new ConfirmationWindow("Delete this comment?", function() {
				model = this.model;
				view = this;
				model.save({
					comment_text: "This comment has been removed",
					deleted: true
				},
				{
					wait: true,
					success: function() {
						view.render();
					},
					error: function() {
						showConfirmationMessage("Unable to delete comment. Try again later.");
					}
				});
			}, this).show();
			return false;
		}		
	});
	
	// Tag model
	var Tag = Backbone.Model.extend();
	
	// Tag collection
	var Tags = Backbone.Collection.extend({
		model: Tag
	})		

	// Link model
	var Link = Backbone.Model.extend();
	
	// Link collection
	var Links = Backbone.Collection.extend({
		model: Link
	})
	
	// Link model
	var Place = Backbone.Model.extend();
	
	// Link collection
	var Places = Backbone.Collection.extend({
		model: Place
	})		


	// Droplet model
	var Drop = Drops.Drop = Backbone.Model.extend({
		
		initialize: function() {
			if (this.get('droplet_image') != undefined) {
				if (this.get('droplet_image').thumbnails != undefined) {
					if (this.get('droplet_image').thumbnails['200'] != undefined) {
						this.set('droplet_image_url', this.get('droplet_image').thumbnails['200']);
					}
				} 
				
				if (!this.has('droplet_image_url')) {
					this.set('droplet_image_url', site_url + "media/thumb/?src=" + encodeURIComponent(this.get('droplet_image').url) + "&w=200");
				}
			}
		},
		
		// Add/Remove a droplet from a bucket
		setBucket: function(changeBucket) {
			// Is this droplet already in the bucket?
			buckets = this.get("buckets");
			if (this.isInBucket(changeBucket)) {
				// Remove the bucket from the list
				buckets = _.filter(buckets, function(bucket) { return bucket["id"] != changeBucket.get("id"); });
				this.set('buckets', buckets);
			} else {
				buckets.push({id: changeBucket.get("id"), bucket_name: changeBucket.get("bucket_name")});
			}
			
			this.save({buckets: buckets}, {wait: true});
		},
		
		// Return boolean of whether this droplet is in the provided bucket
		isInBucket: function(checkBucket) {
			return _.any(this.get("buckets"), function(bucket) { return bucket["id"] == checkBucket.get("id"); })
		},
		
		// Score the droplet
		score: function(val) {
			var score = (val - this.get('user_score'));
			
			// Normalize the score to either -ve or +ve 1 only
			if (score > 1) {
				score = 1;
			} else if (score < -1) {
				score = -1;
			}
			
			this.save({user_score: score, droplet_score: {user_score: score, user_id: window.logged_in_user}});
		}
	});
		
	
	// Droplet & Bucket collection
	var DropsList = Drops.DropsList = Backbone.Collection.extend({
		model: Drop,
				
		comparator: function (droplet) {
			return Date.parse(droplet.get('droplet_date_pub'));
		},
		
		add: function (model, options) {
			
			var isReset = this.length == 0;
			
			// Do the default add from parent class
 			Backbone.Collection.prototype.add.call(this, model, options);
			
			// Our custom event raised when not a reset
			if (!isReset) {
				// Get the Backbone model objects that have just been added
				var models = [];
				dropsList = this;
				_.each(model, function(drop) {
					models.push(dropsList.get(drop.id));
				})

				// Custom event that unlike the add event, will contain an
				// array of all the models that were added at once.
				this.trigger("drops", models);
			}
		}
	});
	
	
	// Common between drop list and full view.
	var DropBaseView = Backbone.View.extend({	
		showAddToBucketModal: function() {
			var addToBucketView = new AddToBucketView({collection: Assets.bucketList, model: this.model});
			modalShow(addToBucketView.render().el);
			return false;
		},
					
		showDropScore: function(selector) {
			var el = this.$(selector);
			el.toggleClass('scored');
			if (el.hasClass("scored")) {
				el.siblings("li").removeClass("scored");
			}
		},
		
		likeDrop: function() {
			this.model.score(1);
			return false;
		},
		
		dislikeDrop: function() {
			this.model.score(-1);
			return false;
		},
		
		updateDropScore: function() {
			new_score = this.model.get('user_score');
			old_score = this.model.previous('user_score');
			
			if (new_score == 1 || (new_score == 0 && old_score == 1)) {
				this.showDropScore("ul.score-drop > li.like");
			} else if (new_score == -1 || (new_score == 0 && old_score == -1)) {
				this.showDropScore("ul.score-drop > li.dislike");
			}
		},

		shareDrop: function() {
			if (!window.public_registration_enabled) {
				// Public registration not allowed, show message
				showConfirmationMessage("Drop sharing is not allowed in this deployment");
			} else {
				shareView = new ShareDropView({model: this.model, baseURL: this.options.baseURL});
				modalShow(shareView.render().el);
			}
		    return false;
		}
	})
	
	
	// Single drop in the drops/list view
	var DropView = DropBaseView.extend({
	
		tagName: "article",
			
		events: {
			"click a.zoom-trigger": "showDetail",
			"click p.discussion a": "showDetail",
			"click li.bucket a.modal-trigger": "showAddToBucketModal",
			"click ul.score-drop > li.like a": "likeDrop",
			"click ul.score-drop > li.dislike a": "dislikeDrop",
			"click li.share > a": "shareDrop"
		},
		
		initialize: function(options) {
			var el = null;
			if (options.layout == "list") {
				el = this.make("article", {"class": "drop base cf"})
				this.template = _.template($("#drop-list-view-template").html());
			} else 	if (options.layout == "photos") {
				el = this.make("article", {"class": "drop col_3 base"})
				this.template = _.template($("#drop-photos-view-template").html());	
			} else {
				el = this.make("article", {"class": "drop col_3 base"})
				this.template = _.template($("#drop-drops-view-template").html());
			}
			this.setElement(el);
			
			this.model.on("change:user_score", this.updateDropScore, this)
			this.model.on("change:comment_count", this.render, this)
		},
				
		render: function(eventName) {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},
		
		// Show the drop in zoom view
		showDetail: function() {
			this.options.router.navigate("/drop/" + this.model.get("id")  + "/zoom", {trigger: true});
			return false;
		}
	});
	
	// Drop detail in zoom view
	var DropDetailView = Drops.DropDetailView = DropBaseView.extend({
	
		tagName: "div",
	
		className: "modal drop drop-full col_9",
		
		isFetching: false,
		
		lastId: Math.pow(2,32) - 1,
		
		isAtLastPage: false,
		
		maxId: 0,
		
		renderDiscussionCollection: false,
		
		isPollingStarted: false,
		
		isSyncing: false,
		
		events: {
			"click .add-comment .drop-actions a": "addReply",
			"click li.bucket a.modal-trigger": "showAddToBucketModal",
			"click .settings-toolbar .button-big a": "showFullDrop",
			"click ul.score-drop > li.like a": "likeDrop",
			"click ul.score-drop > li.dislike a": "dislikeDrop",
			"click li.share > a": "shareDrop",
			"click #discussions_next_page": "loadComments",
			"click #new_comments_alert a": "showNewComments",
			"click a.button-prev": "showPrevDrop",
			"click a.button-next": "showNextDrop"
		},
		
		initialize: function(options) {
			this.template = _.template($("#drop-detail-template").html());
			
			// Create a single discussion collection per drop
			if (this.model.has("discussion_collection")) {
				this.discussions = this.model.get("discussion_collection");
				this.renderDiscussionCollection = true;
			} else {
				this.discussions = new Discussions();
				this.discussions.url = options.baseURL+"/reply/"+this.model.get("id");
				this.model.set("discussion_collection", this.discussions);
				this.loadComments();
			}
			this.discussions.on('add', this.addDiscussion, this);			
			this.model.on("change:user_score", this.updateDropScore, this);
			
			this.newComments = new Discussions();
			this.newComments.url = options.baseURL + "/reply/" + this.model.get("id");
			this.newComments.on('add', this.alertNewComments, this);
			this.newComments.on('reset', this.resetNewCommentsAlert, this);
		},
						
		render: function(eventName) {
			this.$el.html(this.template(this.model.toJSON()));
			
			if (this.renderDiscussionCollection) {
				// Pre-existing so no add event therefore
				// we need to render the list manually
				this.discussions.each(this.addDiscussion, this);	
			}
			return this;
		},
		
		addDiscussion: function(discussion) {
			if (parseInt(discussion.get("id")) < this.lastId || !this.lastId) {
				this.lastId = parseInt(discussion.get("id"));
			}
			
			if (parseInt(discussion.get("id")) > this.maxId) {
				this.maxId = parseInt(discussion.get("id"));
			}
			
			if (this.discussions.length >= 20) {
				this.$("section.drop-discussion p.button-white").parent().show();
			}
			
			var view = new DiscussionView({model: discussion});
			discussion.view = view;
			var index = this.discussions.indexOf(discussion);
			if (index > 0) {
				// Newer comments added before coments they follow in the collection
				this.discussions.at(index-1).view.$el.before(view.render().el);
			} else {
				// First comment is simply appended to the view
				this.$("section.drop-discussion p.button-white").parent().before(view.render().el);
			}
		},
				
		// When add reply is clicked
		addReply: function(e) {
			var textarea = this.$(".add-comment textarea");
			
			if (!$(textarea).val().length)
				return false;

			var publishButton = this.$(".add-comment .drop-actions p").clone();            
			var loading_msg = window.loading_message.clone();
			this.$(".add-comment .drop-actions p").replaceWith(loading_msg);
            
			//var error_el = this.$("section.discussion div.system_error");
			var drop = this.model;
			this.discussions.create({comment_text: $(textarea).val()}, {
				wait: true,
				complete: function() {
					loading_msg.replaceWith(publishButton);
				},
				success: function(model, response) {
					textarea.val("");
					drop.set("comment_count", parseInt(drop.get("comment_count")) + 1);
				},
				error: function(model, response) {
					showConfirmationMessage("Unable to add comment. Try again later.");
				}
			});
			
			return false;
		},
		
		doPollComments: function() {
			this.isPollingStarted = true;
			
			view = this;
			var t = setTimeout(function() {
				
				if (view.isSyncing) {
					// Sync in progress, try again later
					view.doPollComments();
				} else {
					// Request newer comments
					view.newComments.fetch({
						data: {
							since_id: view.maxId
						}, 
						add: true,
						complete: function() {
							if (view.$el.is(":visible")) {
								// Only keep polling if the detail is in view
								view.doPollComments();
							}
						}
					});
				}
			}, 60000 + Math.floor((Math.random()*60000)+1));

		},
		
		alertNewComments: function(comment) {			
			if (parseInt(comment.get("id")) > this.maxId) {
				this.maxId = parseInt(comment.get("id"));
			}
			
			var message = this.newComments.length + " new comment" + (this.newComments.length > 1 ? "s" : "");
			this.$("#new_comments_alert span.message").html(message);
			this.$("#new_comments_alert").show();
		},
		
		resetNewCommentsAlert: function() {
			this.$("#new_comments_alert").fadeOut("slow", function() {
				$(this).find("span.message").html("");
			});
		},
		
		showNewComments: function() {
			if (this.isSyncing)
				return;
			
			// Prevent further updates while in here
			this.isSyncing = true;
			
			this.discussions.add(this.newComments.models);
			this.newComments.reset();
			
			// Proceed
			this.isSyncing = false;
			
			return false;
		},
		
		loadComments: function() {
			
			if (this.isFetching || this.isAtLastPage)
				return false;
			
			this.isFetching = true;
			
			view = this;
			this.discussions.fetch({
				data: {
					last_id: view.lastId
				}, 
				add: true,
				complete: function(model, response) {
					// Re-enable scrolling after a delay
					setTimeout(function(){ view.isFetching = false; }, 700);
					//loading_msg.fadeOut('normal');
					
					// Start polling after initial load
					if (!view.isPollingStarted) {
						view.doPollComments()
					}
				},
				error: function(model, response) {
					if (response.status == 404) {
						view.isAtLastPage = true;
						var message = view.$("#no_comments_alert");
						if (view.discussions.length) {
							view.$("section.drop-discussion p.button-white").parent().replaceWith(message.show());
						}
					}
				}
			});
			return false;
		},
		
		showFullDrop: function() {
			this.options.router.navigate("/drop/" + this.model.get("id"), {trigger: true});
			return false;
		},

		showPrevDrop: function() {
			if (this.model.prev) {
				this.options.router.navigate("/drop/" + this.model.prev.get("id")  + "/zoom", {trigger: true});
			}
			return false;
		},

		showNextDrop: function() {
			if (this.model.next) {
				this.options.router.navigate("/drop/" + this.model.next.get("id")  + "/zoom", {trigger: true});
			}
			return false;
		}
	});
	
	// Bucket in modal view
	var BucketView = Drops.BucketView = Backbone.View.extend({
	
		tagName: "label",
		
		events: {
			"click input": "toggleBucket"
		},
		
		initialize: function() {
			this.template = _.template($("#bucket-template").html());
		},
		
		render: function() {
			// Determine if this bucket contains the selected drop
			var bucket = this.model
			var droplet_buckets = this.options.drop.get('buckets');
			var containsDrop = typeof _.find(droplet_buckets, function(droplet_bucket) { return droplet_bucket['id'] == bucket.get('id') }) !== 'undefined';
			bucket.set('containsDrop', containsDrop);
			
			// Render the bucket
			this.$el.html(this.template(bucket.toJSON()));
			
			return this;
		},
		
		setSelected: function() {
			this.$el.addClass("selected");
			this.$("input[type=checkbox]").prop("checked", true);
		},
		
		toggleBucket: function() {
			this.options.drop.setBucket(this.model);
			if (this.$("input[type=checkbox]").is(':checked')) {
				this.$el.addClass('selected');
			} else {
				this.$el.removeClass('selected');
			}
		}
	});
	
	
	// Buckets modal
	var AddToBucketView = Drops.AddToBucketView = Assets.BaseModalAssetListView.extend({
	
		tagName: "article",
	
		className: "modal",
		
		listSelector: '.select-list',

		listItemSelector: '.select-list label',
		
		initialize: function(options) {
			this.template =  _.template($("#add-to-bucket-template").html());
			this.$el.html(this.template(this.model.toJSON()));
				
			Assets.BaseAssetListView.prototype.initialize.call(this, options);
		},
				
		getView: function(bucket) {
			return new BucketView({model: bucket, drop: this.model});
		},
		
		// Override default determination for assets to be rendered
		renderAsset: function(bucket) {
			return bucket.get("is_owner");
		},
						
		onSaveNewBucket: function(bucket) {
			// if the drop is not already in the bucket
			if (!this.model.isInBucket(bucket)) {
				this.model.setBucket(bucket);
			}
		},
		
		saveNewBucket: function() {
			
			if (this.$("#create-bucket-form").hasClass("nodisplay")) {
				this.$("#show-create-new").remove();
				this.$("#create-bucket-form").removeClass("nodisplay")
				return false;
			}
			
			return Assets.BaseModalAssetListView.prototype.saveNewBucket.call(this);
		}	
	});
		
	// VIEW: Listing of drops
	var DropsView = Drops.DropsView = Backbone.View.extend({
		
		noContentElHidden: false,
		
		events: {
			"click article.alert-message a": "showNewDrops"
		},
				
		initialize: function(options) {
			this.template = _.template($("#drop-listing-template").html());
			
			// Set this view element programatically
			if (options.layout == "list") {
				this.setElement(this.make("article", {"class": "river list"}));
			} else {
				this.setElement(this.make("article", {"class": "river drops cf", "style": "position: relative;"}));
			}

			options.dropsList.on('reset', this.initDrops, this); 
			options.dropsList.on('destroy', this.checkEmpty, this);
			
			if (options.layout == "list") {
				// For list layout we can add drops directly, no masonry required
				options.dropsList.on('add', this.addDrop, this);
			} else {
				// Masonry requires all new drops to be added at once for a smooth
				// animation
				options.dropsList.on('drops', this.addDrops, this);
			}
			
			options.newDropsList.on('add', this.alertNewDrops, this);
			options.newDropsList.on('reset', this.resetNewDropsAlert, this);
		},
		
		render: function() {
			this.$el.html(this.template());
			
			// Show new drops alert if any
			this.alertNewDrops();
			
			return this;
		},
		
		// In drops view, add a batch of drops to the view.
		addDrops: function(drops) {
			// Get the views for each drop
			var context = this;
			
			var views = [];
			var id = this.options.maxId;
			_.each(drops, function(drop) {
				id = Math.max(id, drop.get("sort_id"));
				views.push(new DropView({model: drop, 
										layout: this.options.layout, 
										baseURL: context.options.baseURL,
										router: context.options.router}).render().el);
			}, this)
			
			// Hide the new drops while they are loading
			var $newElems = $(views).css({ opacity: 0 });
			var $container = this.$('#drops-view');
			
			// Add the drops to the view all at once and do masonry
			if (id > this.options.maxId) {
				// New drops, prepend them
				$container.prepend($newElems);
			} else {
				// Pagination, append the drops
				$container.append($newElems);
			}
			
			$newElems.imagesLoaded(function(){
				// show elems now they're ready
				$newElems.animate({ opacity: 1 });
				if (id > context.options.maxId) {
					// New drops, prepend them
					 $container.masonry('reload');
				} else {
					// Pagination, append the drops
					$container.masonry('appended', $(views), true);
				}
			});
		},
		
		// Add a single drop to the view either in initialization
		// or list view.
		addDrop: function(drop) {
			var view = new DropView({model: drop, 
									layout: this.options.layout, 
									baseURL: this.options.baseURL,
									router: this.options.router});
			drop.view = view;
			
			// Create a circular linked list of drops
			var i = this.options.dropsList.indexOf(drop);
			// Last index
			var l = this.options.dropsList.length - 1;
			
			if (l > 0) {
				drop.prev = i > 0 ? this.options.dropsList.at(i-1) : this.options.dropsList.at(l);
				drop.next = i == l ? this.options.dropsList.at(0) : this.options.dropsList.at(i+1);
				drop.prev.next = drop;
				drop.next.prev = drop;
			} else {
				drop.prev = null;
				drop.next = null;
			}
			
			if (this.options.layout == "list") {
				// Ordering is significant for list view
				// and drops will be inserted in the view according to
				// their dates and not simply appended/prepended.
				var index = this.options.dropsList.indexOf(drop);
				if (index > 0) {
					// Newer drops are added in the view before drops
					// they follow in the list i.e. newer drops are added
					// on top
					this.options.dropsList.at(index-1).view.$el.before(view.render().el);
				} else {
					// First drop is simply appended to the view
					this.$("#drops-view").append(view.render().el);
				}
			} else {
				// Keep drops hidden until masonry is done
				view.render().$el.css({ opacity: 0 });
				this.$("#drops-view").prepend(view.el);
			}
			
			if (!this.noContentElHidden) {
				this.hideNoContentEl();
			}
			
		},
		
		// Initialize the view
		initDrops: function() {
			// Remove drops if any from the view
			this.$("article.drop").remove();
			
			// When a reset is done after initialization, redo masonry.
			var doMasonry = false;
			if (this.$("#drops-view").hasClass("masonry")) {
				this.$("#drops-view").masonry('destroy');
				doMasonry = true;
			}
			
			if (!this.checkEmpty()) {
				this.hideNoContentEl();
				this.options.dropsList.each(this.addDrop, this);
			}
			
			if (doMasonry) {
				this.masonry();
			}
		},
		
		masonry: function() {
			// Do masonry
			var $container = this.$('#drops-view');
			var view = this;
			$container.imagesLoaded( function(){
				view.$("article.drop").animate({ opacity: 1 });
				if (view.options.layout == "drops" || view.options.layout == "photos") {
					// MASONRY: DROPS
					if ((window.innerWidth >= 615) && (window.innerWidth <= 960)) {
						$container.masonry({
							itemSelector: 'article.drop',
							isAnimated: !Modernizr.csstransitions,
							columnWidth: function( containerWidth ) {
								return containerWidth / 3;
							}
						});
					}
					else if (window.innerWidth >= 960) {
						$container.masonry({
							itemSelector: 'article.drop',
							isAnimated: !Modernizr.csstransitions,
							columnWidth: function( containerWidth ) {
								return containerWidth / 4;
							}
						});
					}
				}
			});
		},
		
		hideNoContentEl: function() {
			this.$(".no-content").hide();
			this.noContentElHidden = true;
		},
		
		checkEmpty: function() {
			if (!this.options.dropsList.length) {
				var noContentEl = this.$(".no-content");
				this.$el.remove();
				$("#content").removeClass("river").addClass("cf").append(noContentEl);
				noContentEl.show();
				this.noContentElHidden = false;
				return true;
			}
			return false;
		},
		
		alertNewDrops: function() {
			var count = this.options.newDropsList.size();
			if (count > 0) {
				// Alert message to show
				var message = "<p style=\"text-align:center\">" +
				    "<a href=\"#\">" + count + " new drop" + (count == 1 ? "" : "s") +". "+
				    "Click here to refresh the view</a></p>";

				if (this.$("article.alert-message").length == 0) {

					// Construct the HTML for the DOM containing the alert message
					var alertContainer = "<div class=\"center cf\">" +
					    "<article class=\"container base alert-message\">" + message +
					    "</article>" +
					    "</div>";
					
					// Attach to the drops view
					this.$el.prepend(alertContainer).fadeIn(350);
				} else {
					this.$("article.alert-message").html(message);
				}
			}
		},
		
		showNewDrops: function() {
			this.options.dropsList.add(this.options.newDropsList.models);
			this.options.newDropsList.reset();
		},
		
		resetNewDropsAlert: function() {
			this.$("article.alert-message").fadeOut("slow").remove();
		}
	});

	
	// Edit Metadata List Item
	var EditMetadataListItemView = Backbone.View.extend({
	
		tagName: "li",
		
		events: {
			"click span.remove-small": "removeMeta",
		},
		
		initialize: function() {
			this.template = _.template($("#edit-metadata-listitem").html());
		},
						
		render: function() {
			var label = '';
			metadata = this.model;
			if (metadata instanceof Tag) {					
				label = metadata.get("tag");
			} else if (metadata instanceof Link) {
				var url = metadata.get("url");
				url = url.length > 30 ? url.substr(0, 30) + "..." : url;
				label = url;
			} else if (metadata instanceof Place) {					
				label = metadata.get("place_name");
			}
			this.$el.html(this.template({label: label}));
			return this;
		},
		
		setSelected: function() {
			this.$el.addClass("selected");
		},
		
		removeMeta: function() {
			// Delete on the server
			this.model.destroy();
			this.$el.fadeOut("slow");
		}
	});
	
	// Edit Metadata modal
	var EditMetadataView = Backbone.View.extend({
	
		tagName: "article",
	
		className: "modal",
		
		events: {
			"click .create-new a": "saveNewMetadata",
			"submit": "saveNewMetadata"
		},
		
		initialize: function() {
			this.template = _.template($("#add-metadata-template").html());
			this.collection.on("add", this.addMetadata, this);
		},
		
		addMetadata: function(metadata) {
			var editMetadataView = new EditMetadataListItemView({model: metadata});
			this.$(".link-list ul").append(editMetadataView.render().el);
			
			// Store this view in the model to facilitate finding its view 
			// when only the model is available.
			metadata.view = editMetadataView;
			
			return false;
		},
		
		render: function() {
			var data = this.model.toJSON();
			if(this.collection instanceof Tags) {
				data.label = 'tag';
			} else if(this.collection instanceof Links) {
				data.label = 'link';
			} else if(this.collection instanceof Places) {
				data.label = 'location';
			}
			this.$el.html(this.template(data));
			
			// Display current meta in the list
			this.collection.each(this.addMetadata, this);
			
			return this;
		},
		
		isPageFetching: false,
		
		saveNewMetadata: function() {
			var name = $.trim(this.$(".create-new input[name=new_metadata]").val());
			
			if (!name.length || this.isPageFetching)
				return false;
				
			// First check if the metadata already exists in the drop
			var metadata = this.collection.find(function(metadata) { 
				if(metadata instanceof Tag) {
					return metadata.get('tag_canonical') == name.toLowerCase();
				} else if(metadata instanceof Link) {
					return metadata.get('url') == name;
				} else if(metadata instanceof Place) {
					return metadata.get('place_name_canonical') == name.toLowerCase();
				}
			});
			
			if (metadata) {
				// Scroll to the bucket in the list
				var scrollOffset = metadata.view.$el.offset().top - this.$(".link-list").offset().top;
				
				// Scroll only if the bucket is outside the view
				if (scrollOffset < 0 || scrollOffset > this.$(".link-list").height()) {
					this.$(".link-list").animate({
						scrollTop: this.$(".link-list").scrollTop() + scrollOffset
						}, 600);
				}

				metadata.view.setSelected();
				this.$(".create-new input[name=new_metadata]").val("");				
				this.isPageFetching = false;				
				return false;
			}
				
			var loading_msg = window.loading_message.clone();
			var create_el = this.$(".create-new .field").clone();
			this.$(".create-new .field").replaceWith(loading_msg);
						
			if(this.collection instanceof Tags) {
				metadata = new Tag({tag: name});
			} else if(this.collection instanceof Links) {
				metadata = new Link({url: name});
			} else if(this.collection instanceof Places) {
				metadata = new Place({place_name: name});
			}
			
			var view = this;
			this.collection.create(metadata, {
				wait: true,
				complete: function() {
					view.isPageFetching = false;
					loading_msg.replaceWith(create_el);
				},
				error: function(model, response) {
					var message = "";
					if (response.status == 400) {
						errors = JSON.parse(response.responseText)["errors"];
						_.each(errors, function(error) { message += "<li>" + error + "</li>"; });
					} else {
						message = "Oops something went wrong. Try again later.";
					}
					flashMessage(view.$(".system_error"), message);
				},
				success: function() {
					// Scroll to the new item in the list
					view.$(".link-list").animate({
						scrollTop: view.$(".link-list").scrollTop() + (view.$(".link-list li").last().offset().top - view.$(".link-list").offset().top)
						}, 600);
					metadata.view.setSelected();
					view.$(".create-new input[name=new_metadata]").val("");
				}
			});
			return false;
		}
	});
	
		
	// VIEW: Metadata block in Drop detail view
	var MetadataView = Backbone.View.extend({
		
		tagName: "section",
	
		className: "meta-data",
		
		events: {
			// Show/Hide the edit buttong
			"mouseover h3": function() { this.$("h3 .button-blue").show(); },
			"mouseout h3": function() { this.$("h3 .button-blue").hide(); },
			
			"click h3 a": "showEditMetadata"
		},
		
		initialize: function() {
			this.template = _.template($("#metadata-template").html());
			this.collection.on("add", this.addMetadata, this);
		},
		
		render: function() {
			this.$el.html(this.template());
			
			if(this.collection instanceof Tags) {
				this.$("h3 .icon").after("Tags");
			} else if(this.collection instanceof Links) {
				this.$("h3 .icon").after("Links");
			} else if(this.collection instanceof Places) {
				this.$("h3 .icon").after("Places");
			} 
			
			this.collection.each(this.addMetadata, this)
			return this;
		},
		
		addMetadata: function(metadata) {
			var item = "<li>";
			
			if (metadata instanceof Tag) {					
				item += "<a href=\"#\">" + metadata.get("tag") + "</a>";
			} else if (metadata instanceof Link) {
				var url = metadata.get("url");
				url = url.length > 20 ? url.substr(0, 20) + "..." : url;
				item += "<a href=\"" + metadata.get("url") + "\" target=\"_blank\" title=\"" + metadata.get("url") + "\">" + url + "</a>";
			} else if (metadata instanceof Place) {					
				item += "<a href=\"#\">" + metadata.get("place_name") + "</a>";
			}
			
			item += "</li>";
			var el = $(item);
			
			// Add the item to the list
			this.$(".meta-data-content .meta-list").append(el);
			
			// Remove the item when the model is deleted
			metadata.on("destroy", function() {
				el.fadeOut("slow");
			});
		},
		
		showEditMetadata: function() {
			var editMetadataView = new EditMetadataView({model: this.model, collection: this.collection});
			modalShow(editMetadataView.render().el);
			return false;
		}
	});
	
	// VIEW: Full Drop
	var DropFullView = Drops.DropFullView = Backbone.View.extend({
	
		tagName: "article",
	
		className: "drop drop-full cf",
		
		initialize: function(options) {
			this.template = _.template($("#drop-full-view-template").html());
		},
		
		render: function() {
			// Render the layout
			this.$el.html(this.template());
			
			// Render the droplet detail
			var detailView = new DropDetailView({model: this.model, baseURL: this.options.baseURL, router: this.options.router});
			this.$("article .center .col_3").before(detailView.render().el);
			
			// Render metadata
			var places = new Places;
			places.url = this.options.baseURL+"/places/"+this.model.get("id");
			places.reset(this.model.get("places"));
			this.addMetadataBlock(places);
			
			var links = new Links;
			links.url = this.options.baseURL+"/links/"+this.model.get("id");
			links.reset(this.model.get("links"));
			this.addMetadataBlock(links);
			
			var tags = new Tags;
			tags.url = this.options.baseURL+"/tags/"+this.model.get("id");
			tags.reset(this.model.get("tags"));
			this.addMetadataBlock(tags);
			
			
			window.fullView = this;
			return this;
		},
		
		addMetadataBlock: function (collection) {
			var view = new MetadataView({collection: collection, model: this.model});
			this.$("article .center .col_3").append(view.render().el);
		}
	});

	// VIEW: Share drop
	var ShareDropView = Backbone.View.extend({
		tagName: "article",

		className: "modal",

		events: {
			"click li.email > a": "showEmailDialog",
		},
		
		initialize: function(options) {
			this.template = _.template($("#share-drop-template").html());
		},

		showEmailDialog: function() {
			var emailView = new EmailDropView({model: this.model, baseURL: this.options.baseURL});
			modalShow(emailView.render().el);
			return false;
		},

		render: function() {
			var data = this.model.toJSON();
			data['drop_url'] = site_url.substring(0, site_url.length-1) + this.options.baseURL + '/drop/' + data["id"];
			this.$el.html(this.template(data));
			return this;
		}
	});

	var EmailDropView = Backbone.View.extend({
		tagName: "article",

		className: "modal",

		events: {
			"click p.button-blue a": "sendEmail"
		},
		
		initialize: function(options) {
			this.template = _.template($("#email-dialog-template").html());
			this.dropURL = site_url.substring(0, site_url.length-1) + this.options.baseURL + '/drop/' + this.model.get("id");
			this.hasSubmitted = false;
		},

		sendEmail: function() {
			var postData = {
				drop_title: this.model.get("droplet_title"),
				drop_url: this.dropURL
			};
			$(":input", this.$("form")).each(function(index){
				postData[$(this).attr("name")] = $(this).val();
			});

			if (!this.hasSubmitted) {
				this.hasSubmitted = true;

				// Hide pre-existing error messsages
				this.$("#error").hide();

				// Show the loading icon
				var loading_msg = window.loading_message.clone();
				var submitButton = this.$("p.button-blue");
				this.$("p.button-blue").replaceWith(loading_message);

				var context = this;

				// Submit for sharing
				$.ajax({
					url: this.options.baseURL + "/share",
					
					type: "POST",
					
					data: postData,

					success: function(response) {
						// Show success message
						context.$("#success").show();
						context.$(loading_message).replaceWith(submitButton);
						context.hasSubmitted = false;

						// Close the dialog
						setTimeout(function(){context.$("h2.close a").trigger("click");}, 1800);
					},

					error: function() {
						// Show error message
						context.$("#error").show();
						context.hasSubmitted = false;
						context.$(loading_message).replaceWith(submitButton);
					},

					dataType: "json"
				});
			}
			return false;
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}

	});
	
}(this));