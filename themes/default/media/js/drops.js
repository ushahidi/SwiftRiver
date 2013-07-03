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
	
	// SearchFilter model
	var SearchFilter = Backbone.Model.extend({
		defaults: {
			"keywords": null,
			"date_from": null,
			"date_to": null,
			"locations": null,
		},
	});
	
	// SearchFilter collection
	var SearchFilters = Backbone.Collection.extend({
		model: SearchFilter
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
		setBucket: function(changeBucket, success_callback, error_callback) {
			// Clone the buckets lit - Backbone JS appears to be using
			// reference equality so when the buckets list/array is altered via
			// buckets.push, the Backbone JS reference will remain the same - hence
			// the change event won't be triggered
			buckets = _.clone(this.get("buckets"));
			if (buckets == null) {
				buckets = [];
			}

			bucketId = changeBucket.get("id");

			// The task to be performed - add/remove from bucket
			var command = "";
			var removedBucket = null;

			// Is the drop already in the bucket
			if (this.isInBucket(changeBucket)) {
				// Remove the bucket from the list
				removedBucket = _.find(buckets, function(bucket) {return bucket['id'] == bucketId; });
				buckets = _.filter(buckets, function(bucket) { return bucket["id"] != bucketId; });
				command = "remove";
			} else {
				buckets.push({id: bucketId, name: changeBucket.get("name")});
				command = "add";
			}

			// Updated model data
			var modelData = {
				buckets: buckets,
				command: command,
				bucket_id: bucketId
			};
			
			if (command == "add") {
				modelData.read = !this.get('read');
			}
			
			if (command == "remove") {
				modelData.bucket_drop_id = removedBucket["bucket_drop_id"];
			}

			// Save the changes
			this.save(modelData ,{
				patch: true,
				wait: true,
				success: success_callback,
				error: error_callback
			});
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
			return Date.parse(droplet.get('date_published'));
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
					var dropModel = dropsList.get(drop.id);
					if (!dropModel.urlRoot) {
						dropModel.urlRoot = dropsList.url;
					}
					models.push(dropModel);
				})

				// Custom event that unlike the add event, will contain an
				// array of all the models that were added at once.
				this.trigger("drops", models);
			}
		},
		
		getUnread: function() {
			return this.filter(function(drop) { return !drop.get("read"); })
		},
		
		getRead: function() {
			return this.filter(function(drop) { return drop.get("read"); })
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
			el.toggleClass('selected');
			if (el.hasClass("selected")) {
				el.siblings("li").removeClass("selected");
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
				this.showDropScore("ul.score-drop > li.star");
			} else if (new_score == -1 || (new_score == 0 && old_score == -1)) {
				this.showDropScore("ul.score-drop > li.remove");
			}
		},

		shareDrop: function() {
			if (!window.public_registration_enabled) {
				// Public registration not allowed, show message
				showConfirmationMessage("Drop sharing is not allowed in this deployment");
			} else {
				shareView = new ShareDropView({model: this.model, baseURL: this.options.baseURL});
				modalShow(shareView.render().el);
				
				// Mark the drop as read; Assumption: If a user is sharing a drop, they have read it
				if (!this.model.get('read')) {
					this.markAsRead();
				}
			}
		    return false;
		},
		
		updateBucketCount: function() {
			var bucketCount = this.model.get("buckets").length;
			var selector = this.$(".bucket .bucket-total");
			selector.html(bucketCount);
			if (bucketCount > 0) {
				selector.show();
			} else {
				selector.hide();
			}
		},
		removeDrop: function() {
			var view = this;
			this.model.destroy({
				wait: true,
				success: function() {
					view.$el.fadeOut();
					showSuccessMessage("The drop has been successfully deleted.", {flash: true});
				},
				error: function(model, response) {
					showFailureMessage("Unable to delete the drop");
				}
			});
			return false;
		},

		hideReadStatusButton: function() {
			this.$(".drop-status > li.drop-status-read").fadeOut('fast').remove();
		},

		markAsRead: function() {
			if (this.model.get('read') == true) {
				this.hideReadStatusButton();
				return;
			}

			var view = this;
			this.model.save({read: true}, {
				patch: true,
				wait: true,
				success: function(model, response) {
					view.hideReadStatusButton();
				},
				error: function(model, response) {
					showFailureMessage("Oops! Something just went wrong on our server while loading this drop. We'll fix it shortly.");
				}
			});
			return false;
		}
	})
	
	
	// Single drop in the drops/list view
	var DropView = DropBaseView.extend({
				
		events: {
			"click a.zoom-trigger": "showDetail",
			"click p.discussion a": "showDetail",
			"click li.bucket a.modal-trigger": "showAddToBucketModal",
			"click ul.score-drop > li.star a": "likeDrop",
			"click ul.score-drop > li.remove a": "dislikeDrop",
			"click li.share > a": "shareDrop",
			"click li.drop-status-remove > a": "removeDrop",
			"mouseenter": function() {this.$('.drop-status').fadeIn('fast');},
			"mouseleave": function() {this.$('.drop-status').fadeOut('fast');},
			"click li.drop-status-read > a": "markAsRead"
		},
		
		initialize: function(options) {
			var el = null;
			if (options.layout == "list") {
				el = this.make("article", {"class": "drop base cf"});
				this.template = _.template($("#drop-list-view-template").html());
			} else 	if (options.layout == "photos") {
				el = this.make("article", {"class": "drop col_3 base"})
				this.template = _.template($("#drop-photos-view-template").html());	
			} else {
				el = this.make("article", {"class": "drop base masonry-brick"})
				this.template = _.template($("#drop-drops-view-template").html());
			}
			this.setElement(el);
			
			this.model.on("change:user_score", this.updateDropScore, this);
			this.model.on("change:comment_count", this.render, this);
			this.model.on("change:buckets", this.updateBucketCount, this);
		},

		make: function(tagName, attributes) {
			var el = document.createElement(tagName);
			if (attributes) $(el).attr(attributes);
			return el;
		},

		render: function(eventName) {
			// Content for the view
			var viewContent = this.template(this.model.toJSON());

			if (this.options.layout == "drops") {
				// Show the image before the drop content
				if (this.model.get("image") != null) {
					var imageTemplate = _.template($("#drop-photos-view-template").html());
					var imageContent  = imageTemplate({image: this.model.get("image")});

					this.$el.html(imageContent);
				}
			} else if (this.options.layout == "photos") {
				// Show the drop content after the image
				var dropTemplate = _.template($("#drop-drops-view-template").html());
				dropContent = dropTemplate(this.model.toJSON());
				
				if (this.model.get("image") != null) {
					this.$el.html(viewContent);
				}
				viewContent = dropContent;
			}
			
			// Set the content for the view
			this.$el.append(viewContent);
			
			return this;
		},
		
		// Show the drop in zoom view
		showDetail: function() {
			if (!this.model.get('read')) {
				this.markAsRead();
			}
			this.options.router.navigate("/drop/" + this.model.get("id")  + "/zoom", {trigger: true});
			return false;
		},

	});
	
	// Drop detail in zoom view
	var DropDetailView = Drops.DropDetailView = DropBaseView.extend({
	
		tagName: "article",
	
		className: "modal modal-view drop drop-full",
		
		isFetching: false,
		
		lastId: Math.pow(2,32) - 1,
		
		isAtLastPage: false,
		
		maxId: 0,
		
		renderDiscussionCollection: false,
		
		isPollingStarted: false,
		
		isSyncing: false,
		
		events: {
			"click #add-comment a.button-primary": "addComment",
			"click li.bucket a.modal-trigger": "showAddToBucketModal",
			// "click .page-action a.button-primary": "showFullDrop",
			"click ul.score-drop > li.star a": "likeDrop",
			"click ul.score-drop > li.remove a": "dislikeDrop",
			"click li.share > a": "shareDrop",
			"click #discussions_next_page": "loadComments",
			"click #new_comments_alert a": "showNewComments",
			"click a.button-prev": "showPrevDrop",
			"click a.button-next": "showNextDrop",
			"click #add-custom-form": "showAddForm",
		},
		
		initialize: function(options) {
			this.template = _.template($("#drop-detail-template").html());
			// Create a single discussion collection per drop
			if (this.model.has("discussion_collection")) {
				this.discussions = this.model.get("discussion_collection");
				this.renderDiscussionCollection = true;
			} else {
				this.discussions = new Discussions();
				this.discussions.url = options.baseURL+"/comments/"+this.model.get("id");
				this.model.set("discussion_collection", this.discussions);
				this.loadComments();
			}
			this.discussions.on('add', this.addDiscussion, this);			
			this.model.on("change:user_score", this.updateDropScore, this);
			
			this.newComments = new Discussions();
			this.newComments.url = options.baseURL + "/comments/" + this.model.get("id");
			this.newComments.on('add', this.alertNewComments, this);
			this.newComments.on('reset', this.resetNewCommentsAlert, this);
			
			var forms = this.forms = new DropForms;
			forms.url = this.options.baseURL+"/forms/"+this.model.get("id");
			forms.on("add", this.addDropForm, this);
			forms.on("reset", this.addDropForms, this);
			
		},
						
		render: function(eventName) {
			this.$el.html(this.template(this.model.toJSON()));
			if (this.renderDiscussionCollection) {
				// Pre-existing so no add event therefore
				// we need to render the list manually
				this.discussions.each(this.addDiscussion, this);	
			}

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
			
			this.forms.reset(this.model.get("forms"));

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
				this.$(".drop-discussion").parent().show();
			}
			

			var view = new DiscussionView({model: discussion});
			discussion.view = view;
			var index = this.discussions.indexOf(discussion);
			if (index > 0) {
				// Newer comments added before coments they follow in the collection
				this.discussions.at(index-1).view.$el.before(view.render().el);
			} else {
				// First comment is simply appended to the view
				this.$(".drop-discussion").prepend(view.render().el);
			}
		},

		addComment: function(e) {
			var textarea = this.$("#add-comment textarea");
			
			if (!$(textarea).val().length)
				return false;

			var publishButton = this.$("#add-comment .drop-actions a").clone();            
			var loading_msg = window.loading_message.clone();
			this.$("#add-comment .drop-actions a").replaceWith(loading_msg);
            
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
					showFailureMessage("Unable to add comment. Try again later.");
				}
			});
			
			return false;
		},

		addMetadataBlock: function (collection, metadataType, propertyName) {
			var view = new MetadataView({collection: collection, model: this.model});
			this.$("#metadata").prepend(view.render().el);
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
				update: true,
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
		},
		
		addDropForm: function(dropForm) {
			var form = Assets.formList.get(dropForm.get("id"));
			
			var view = new DropFormView({
				model: dropForm,
				form: form
			});
			this.$("#metadata #add-custom-form").before(view.render().el);
		},
		
		addDropForms: function() {
			this.forms.each(this.addDropForm, this);
		},
		
		showAddForm: function() {
			var view = new AddCustomFormModal({
				dropForms: this.forms,
				baseUrl: this.options.baseURL + "/forms/" + this.model.get("id"),
			});
			modalShow(view.render().el);
			
			return false;
		}
	});
	
	// Bucket in modal view
	var BucketView = Drops.BucketView = Backbone.View.extend({
	
		tagName: "li",
		
		className: "static cf",
		
		events: {
			"click span.select": "toggleBucket"
		},
		
		initialize: function() {
			this.template = _.template($("#bucket-template").html());
		},
		
		render: function() {
			// Determine if this bucket contains the selected drop
			var bucket = this.model
			var droplet_buckets = this.options.drop.get('buckets');
			var containsDrop = typeof _.find(droplet_buckets, function(droplet_bucket) { return droplet_bucket['id'] == bucket.get('id') }) !== 'undefined';
			if (containsDrop) {
				this.$el.addClass("selected");
			}
			
			// Render the bucket
			this.$el.html(this.template(bucket.toJSON()));
			
			return this;
		},
		
		setSelected: function() {
			this.$el.addClass("selected");
		},
		
		toggleBucket: function() {
			var bucketName = this.model.get("name");
			var view = this;

			this.options.drop.setBucket(this.model, function() {
				if (!view.$el.hasClass("selected")) {
					view.$el.addClass('selected');
				} else {
					view.$el.removeClass('selected');
				}
				
			}, function(){
				var message = "The drop could not be added to the \"" + bucketName + "\" bucket";
				showFailureMessage(message);
			});
		}
		
	});
	
	
	// Buckets modal
	var AddToBucketView = Drops.AddToBucketView = Assets.BaseModalAssetListView.extend({
	
		tagName: "article",
	
		className: "modal modal-view",
		
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
			return bucket.get("is_owner") || bucket.get("is_collaborator");
		},
		
		onSaveNewBucket: function(bucket) {
			// if the drop is not already in the bucket
			if (!this.model.isInBucket(bucket)) {
				this.model.setBucket(bucket);
			}
		}

	});
	
	// New drops alert message
	var AlertNewDropsView = Backbone.View.extend({
		
		tagName: "article",
	
		className: "stream-message",
		
		events: {
			"click a": "onClick"
		},
		
		initialize: function(options) {
			this.template = _.template($("#new-drops-template").html());
		},
		
		render: function() {
			var data = {count: this.collection.size()};
			this.$el.html(this.template(data)).show();
			return this;
		},
		
		onClick: function() {
			this.trigger("click");
			return false;
		}
		
	});
		
	// VIEW: Listing of drops
	var DropsView = Drops.DropsView = Backbone.View.extend({
		
		noContentElHidden: false,
		
		initialize: function(options) {
			this.template = _.template($("#drop-listing-template").html());
			
			options.dropsList.on('reset', this.initDrops, this); 
			options.dropsList.on('destroy', this.checkEmpty, this);
			
			if (options.layout == "list") {
				// For list layout we can add drops directly, no masonry required
				options.dropsList.on('add', this.addDrop, this);
				this.setElement(this.make("div", {"class": "river list"}))
			} else {
				// Masonry requires all new drops to be added at once for a smooth
				// animation
				options.dropsList.on('drops', this.addDrops, this);
				options.dropsList.on('remove', this.masonry, this);
				this.setElement(this.make("div", {"class": "river drops", "style": "position:relative;"}));
			}
			
			options.newDropsList.on('add', this.alertNewDrops, this);
			options.newDropsList.on('reset', this.resetNewDropsAlert, this);
			options.filters.on('change', this.filtersUpdated, this);
		},
		
		make: function(tagName, attributes, text) {
			var el = document.createElement(tagName);
			if (attributes) $(el).attr(attributes);
			if (text) $(el).html(text);

			return el;
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
				id = Math.max(id, drop.get("id"));
				views.push(new DropView({model: drop, 
										layout: this.options.layout, 
										baseURL: context.options.baseURL,
										router: context.options.router,
										dropsList: context.options.dropsList}).render().el);

				if (context.options.layout != 'list') {
					drop.on("destroy", context.masonry, context);
				}

			}, this)
			
			// Hide the new drops while they are loading
			var $newElems = $(views).css({ opacity: 0 });
			var $container = this.$("#drops-view");
			
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
					$container
						.masonry()
						.masonry('reload');
				} else {
					// Pagination, append the drops
					$container
						.masonry()
						.masonry('appended', $(views), true);
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

			if (this.options.layout != 'list') {
				drop.on("destroy", this.masonry, this);
			}

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
			
			if (this.checkEmpty())
				return;
			
			// When a reset is done after initialization, redo masonry.
			var doMasonry = false;
			if (this.$("#drops-view").hasClass("masonry-brick")) {
				this.$("#drops-view").masonry('destroy');
				doMasonry = true;
			}
			
			this.hideNoContentEl();
			this.options.dropsList.each(this.addDrop, this);

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
								return containerWidth / 2;
							}
						});
					}
					else if (window.innerWidth >= 960) {
						$container.masonry({
							itemSelector: 'article.drop',
							isAnimated: !Modernizr.csstransitions,
							columnWidth: function( containerWidth ) {
								return containerWidth / 3;
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
				if (this.alertView == undefined) {
					this.alertView = new AlertNewDropsView({collection: this.options.newDropsList});
					this.alertView.on("click", this.showNewDrops, this);
				}
				this.alertView.render().$el.prependTo(this.$el).fadeIn("slow");
			}
		},
		
		showNewDrops: function() {
			var view = this;
			var newDropsList = this.options.newDropsList;
			view.alertView.$el.fadeOut("slow", function() {
				view.alertView.$el.remove();
				delete view.alertView;
				
				view.options.dropsList.add(newDropsList.models);
				newDropsList.reset();
			})
		},
		
		resetNewDropsAlert: function() {
			this.$("article.alert-message").fadeOut("slow").remove();
		},
		
		getFilters: function() {
			return this.options.filters;
		},
		
	});

	
	// VIEW: Edit Metadata List Item
	var EditMetadataListItemView = Backbone.View.extend({
	
		tagName: "li",
		
		events: {
			"click span.remove": "removeMeta",
		},
		
		initialize: function() {
			this.template = _.template($("#edit-metadata-item-template").html());
		},
						
		render: function() {
			var label = '';
			metadata = this.model;
			if (metadata instanceof Tag) {
				label = metadata.get("tag");
			} else if (metadata instanceof Link) {
				label = metadata.get("url");
			} else if (metadata instanceof Place) {
				label = metadata.get("name");
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
			return false;
		}
	});
	
	var AddMetadataModalView = Backbone.View.extend({

		tagName: "article",
		
		className: "modal modal-view modal-segment",

		events: {
			"click .modal-toolbar a.button-submit": "saveNewMetadata",
			"keyup .modal-field input": "keypressSave"
		},

		initialize: function(options) {
			this.template = options.templateView;
		},
		
		render: function() {
			this.$el.html(this.template());
			return this;
		},
		
		saveNewMetadata: function(e) {
			var name = $.trim(this.$(".modal-field input[name=new_metadata]").val());
			var tagType = this.$(".modal-field select[name=tag_type]").val();
			if (name && name.length > 0) {
				this.trigger("add", {name: name, tagType: tagType});
			}
			return false;
		},

		keypressSave: function(e) {
			if (e.which == 13) {
				this.saveNewMetadata();
				return false;
			}
		},
		
	});

	// VIEW: Edit Metadata modal dialog
	var EditMetadataView = Backbone.View.extend({
	
		tagName: "article",
	
		className: "modal modal-view",
		
		events: {
			"click li.add a": "showAddMetadata",
		},
		
		initialize: function() {
			this.template = _.template($("#edit-metadata-template").html());
			this.collection.on("add", this.addMetadata, this);

			this.data = this.model.toJSON();
			this.templateView = null;
			
			if(this.collection instanceof Tags) {
				this.data.label = 'Tags';
				this.templateView = _.template($("#add-tag-modal-template").html());
			} else if(this.collection instanceof Links) {
				this.data.label = 'Links';
				this.templateView = _.template($("#add-link-modal-template").html());
			} else if(this.collection instanceof Places) {
				this.data.label = 'Places';
			}
		},
		
		addMetadata: function(metadata) {
			var editMetadataView = new EditMetadataListItemView({model: metadata});
			this.$(".view-table ul").prepend(editMetadataView.render().el);
			
			// Store this view in the model so that it is accessible when
			// the model is available
			metadata.view = editMetadataView;
			return false;
		},
		
		showAddMetadata: function() {
			var view = new AddMetadataModalView({templateView: this.templateView});
			view.on("add", this.save, this);
			modalShow(view.render().el);
			return false;
		},
		
		render: function() {
			this.$el.html(this.template(this.data));
			
			// Display current meta in the list
			this.collection.each(this.addMetadata, this);
			
			return this;
		},
		
		isPageFetching: false,
		
		save: function(data) {
			if (this.isPageFetching)
				return false;
			
			// First check if the metadata already exists in the drop
			var metadata = this.collection.find(function(metadata) { 
				if(metadata instanceof Tag) {
					return (metadata.get('tag').toLowerCase() == data.name.toLowerCase()) 
						&& (metadata.get('type').toLowerCase() == data.tagType.toLowerCase());
				} else if(metadata instanceof Link) {
					return metadata.get('url').toLowerCase() == data.name;
				} else if(metadata instanceof Place) {
					return metadata.get('name').toLowerCase() == data.name.toLowerCase();
				}
			});
			
			if (metadata) {
				// Scroll to the bucket in the list
				var scrollOffset = metadata.view.$el.offset().top - this.$(".view-table ul").offset().top;
				
				// Scroll only if the bucket is outside the view
				if (scrollOffset < 0 || scrollOffset > this.$(".view-table ul").height()) {
					this.$(".view-table ul").animate({
						scrollTop: this.$(".view-table ul").scrollTop() + scrollOffset
						}, 600);
				}
				
				modalBack();
				metadata.view.setSelected();
				this.$(".modal-field input[name=new_metadata]").val("");
				this.isPageFetching = false;
				return false;
			}
				
			var loading_msg = window.loading_message.clone();
			var create_el = this.$(".create-new .field").clone();
			this.$(".create-new .field").replaceWith(loading_msg);
						
			if (this.collection instanceof Tags) {
				metadata = new Tag({tag: data.name, tag_type: data.tagType});
			} else if (this.collection instanceof Links) {
				metadata = new Link({url: data.name});
			} else if (this.collection instanceof Places) {
				metadata = new Place({name: data.name});
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
					view.$(".view-table ul").animate({
						scrollTop: view.$(".view-table ul").scrollTop() + (view.$(".view-table ul li").last().offset().top - view.$(".view-table ul").offset().top)
					}, 600);

					modalBack();
					view.$(".view-table ul li").removeClass("selected");
					metadata.view.setSelected();
				}
			});
			return false;
		}
		
	});
	
		
	// VIEW: Metadata block in Drop detail view
	var MetadataView = Backbone.View.extend({
		
		tagName: "div",
	
		className: "filters-type",
		
		isInitialRender: true,
		
		events: {			
			"click .filters-type-settings a": "showEditMetadata"
		},
		
		initialize: function() {
			this.template = _.template($("#metadata-template").html());
			this.collection.on("add", this.addMetadata, this);
			this.collection.on("remove", this.removeMetadata, this);

			// For each model in this collection, remove it from the collection
			// when the model is deleted i.e. on model.destroy()
			var context = this;
			this.collection.each(function(model) {
				model.on("destroy", function() { context.collection.remove(model); });
			}, this);
		},
		
		render: function() {
			var typeLabel = "";
			if (this.collection instanceof Places) {
				typeLabel = "Places"
			} else if (this.collection instanceof Tags) {
				typeLabel = "Tags";
			} else if (this.collection instanceof Links) {
				typeLabel = "Links";
			}

			var templateData = {
				label: typeLabel,
				count: this.collection.length
			};

			this.$el.html(this.template(templateData));
			this.collection.each(this.addMetadata, this);
			this.initialRender = false;
			return this;
		},
		
		addMetadata: function(metadata) {
			var itemView = new MetadataItemView({model: metadata});			
			this.$(".filters-type-details ul").append(itemView.render().el);
			metadata.itemView = itemView;
			if (!this.initialRender) {
				this.updateMetadataCount();
			}
		},
		
		showEditMetadata: function() {
			var editView = new EditMetadataView({model: this.model, collection: this.collection});
			
			modalShow(editView.render().el);
			return false;
		},
		
		updateMetadataCount: function() {
			this.$("span.total").html(this.collection.length);
		},
		
		// When a model is removed from the collection associated
		// with this view
		removeMetadata: function(metadata) {
			metadata.itemView.$el.fadeOut("slow");
			// Update the count
			this.updateMetadataCount();
		}
	});
	
	// VIEW: Single metadata item
	var MetadataItemView = Backbone.View.extend({

		tagName: "li",
		
		initialize: function(options) {
			this.template = _.template($("#metadata-item-template").html());
			var view = this;
			this.model.on("destroy", function() {
				view.$el.fadeOut("slow");
			});
		},
		
		render: function() {
			var metadataText = "";
			if (this.model instanceof Tag) {
				metadataText = this.model.get("tag");
			} else if (this.model instanceof Place) {
				metadataText = this.model.get("name");
			} else if (this.model instanceof Link) {
				metadataText = this.model.get("url");
			}
			this.$el.html(this.template({metadataText: metadataText}));
			return this;
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
			this.$el.attr("id", "content");
			
			// Render the droplet detail
			var detailView = new DropDetailView({model: this.model, baseURL: this.options.baseURL, router: this.options.router});
			this.$el.append(detailView.render().el);
			
			window.fullView = this;
			return this;
		}		
	});

	// VIEW: Share drop
	var ShareDropView = Backbone.View.extend({
		tagName: "article",

		className: "modal modal-view",

		events: {
			"click a#share-email": "showEmailForm",
		},
		
		initialize: function(options) {
			this.template = _.template($("#share-drop-template").html());
		},

		showEmailForm: function() {
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

		className: "modal modal-view modal-segment",

		events: {
			"click .modal-toolbar a.button-submit": "sendEmail"
		},
		
		initialize: function(options) {
			this.template = _.template($("#email-share-template").html());
			this.dropURL = site_url.substring(0, site_url.length-1) + this.options.baseURL + '/drop/' + this.model.get("id");
			this.hasSubmitted = false;
		},

		sendEmail: function() {
			var postData = {
				drop_title: this.model.get("title"),
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
				var submitButton = this.$(".modal-toolbar a.button-submit");
				this.$(".modal-toolbar a.button-submit").replaceWith(loading_message);

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

						setTimeout(function() { modalBack(); }, 1800);
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
	
	// List of channels in the drop listing
	var DropsStateFilterView = Drops.DropsStateFilterView = Backbone.View.extend({
		
		el: "#drops-state-filter",
		
		events: {
			"click a": "onClick"
		},
		
		initialize: function(options) {
			options.dropsList.on("all", this.renderTotals, this);
			options.dropFilters.on("all", this.renderTotals, this);
			
			this.render();
		},
		
		onClick: function(ev) {
			var el = $(ev.target);
			
			if (!el.parents("li").hasClass("active")) {
				// Currently no state selected
				
				el.parents("li").addClass("active");
				el.parents("li").siblings("li").removeClass("active");
			
				// Update the filter
				if (el.parents("li").hasClass("read")) {
					filters.set("state", "read");
				} else {
					filters.set("state", "unread");
				}
			} else {
				// Deselecting a read state
				
				el.parents("li").removeClass("active");
				filters.unset("state");
			}
			
			return false;
		},
		
		render: function() {
			var dropFilters = this.options.dropFilters;
			
			// Reset read/unread selection
			this.$(".read,.unread").removeClass("active");
			
			if (dropFilters.has("state")) {
				var state = dropFilters.get("state")
				this.$("." + state).addClass("active");
			}
			
			this.renderTotals();
			
			return this;
		},
		
		renderTotals: function() {
			$(".filters-primary li span.total").html(this.options.dropsList.length)
			this.$("li.read .total").html(this.options.dropsList.getRead().length);
			this.$("li.unread .total").html(this.options.dropsList.getUnread().length);
		}
	});
	
	
	// Model form form values
	var DropForm = Backbone.Model.extend({
		
		validate: function(attrs, options) {
			for (var i = 0; i < attrs.values.length; i++) {
				var fieldValue = attrs.values[i];
				var field = options.form.getField(fieldValue.id);
				if (fieldValue.value.length == 0 && field.required)
					return "The field '" + field.title + "' is required";
			}
		}
	});
	
	// DropForm collection
	var DropForms = Backbone.Collection.extend({
		model: DropForm
	});
	
	// Modal view for adding a custom form to a drop
	var AddCustomFormModal = Backbone.View.extend({
		tagName: "article",

		className: "modal modal-view",
		
		initialize: function(options) {
			this.template = _.template($("#add-drop-form-modal-template").html());
		},
		
		addForm: function(form) {
			var value = this.options.dropForms.get(form.get("id"));
			
			var view = new FormView({
				model: form,
				collection: this.options.dropForms,
				value: value,
				baseUrl: this.options.baseUrl
			});
			this.$(".view-table").append(view.render().el);
		},
		
		addForms: function() {
			Assets.formList.each(this.addForm, this);
		},
		
		render: function() {
			this.$el.html(this.template());
			this.addForms();
			return this;
		},
	});
	
	// Single form in the AddCustomFormModal listing
	var FormView = Backbone.View.extend({
		
		tagName: "li",
		
		events: {
			"click a": "onClick"
		},
		
		initialize: function(options) {
			this.template = _.template($("#drop-form-template").html());
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},
		
		onClick: function() {
			var model = null;
			
			// Are there existing values for this form or is this a 
			// new entry?
			if (this.options.value != undefined) {
				model = this.options.value;
			} else {
				model = new DropForm();
			}
			
			var view = new EditFormModalView({
				baseUrl: this.options.baseUrl,
				collection: this.collection,
				model: model,
				form: this.model
			});
			modalShow(view.render().el);
			return false;
		}
	});
	
	// Modal view for adding field values for a form
	var EditFormModalView = Backbone.View.extend({
		
		tagName: "article",

		className: "modal modal-view",
		
		events: {
			"click .modal-toolbar a.button-submit": "save",
		},
		
		initialize: function(options) {
			this.template = _.template($("#edit-drop-form-modal-template").html());
			this.form = options.form;
			this.model.urlRoot = options.baseUrl;
			this.model.on("invalid", this.onFormInvalid, this);
			
			this.fields = new Assets.Fields();
			this.fields.on('add', this.addField, this);
			this.fields.on('reset', this.addFields, this);
			
			this.values = new Backbone.Collection();
			this.values.reset(this.model.get("values"));
		},
		
		addFields: function() {
			this.fields.each(this.addField, this);
		},
		
		addField: function(field) {
			var view = field.view = new Assets.FieldView({
				model: field, 
				isLive: true
			});
			field.view = view;
			view.render();
			
			var value = this.values.get(field.get("id"));
			if (value != undefined) {
				view.setValue(value.get("value"));
			}
			
			
			this.$("ul.view-table").append(view.el);
			view.on("change", this.onFieldChanged, this);
		},
		
		render: function(eventName) {
			this.$el.html(this.template({
				name: this.form.get("name"),
				isNew: this.model.isNew()
			}));
			
			// Render the field list
			this.fields.reset(this.form.get("fields"));
			
			return this;
		},
		
		onFormInvalid: function(model, error) {
			showFailureMessage(error);
		},
		
		save: function() {
			var view = this;
			
			if (view.isFetching)
				return false;
			
			view.isFetching = true;
			var button = this.$(".button-submit");
			var originalButton = button.clone();
			var t = setTimeout(function() {
				button.children("span").html("<em>Saving form</em>").append(loading_image_squares);
			}, 500);
				
			// Disable form elements	
			this.$("input,select").attr("disabled", "disabled");
			
			// Get field values
			var values = [];
			this.fields.each(function(field) {
				values.push({
					id: field.get("id"),
					value: field.view.getValue()
				});
			}, this);
			
			var saveStatus = this.model.save({
				id: this.form.get("id"),
				values: values
			},{
				form: this.form,
				wait: true,
				success: function() {
					if (view.collection != undefined) {
						view.collection.add(view.model);
					}
					modalHide();
					showSuccessMessage("Form saved successfully", {flash: true});
				},
				error: function(model, response) {
					if (response.status == 400) {
						showFailureMessage("The drop already has the '" + view.form.get("name") + "' form fields.");
					} else {
						showFailureMessage("There was a problem creating the form. Try again later.");
					}
					
					view.$("input,select").removeAttr("disabled");
				},
				complete: function() {
					view.isFetching = false;
					button.replaceWith(originalButton);
					clearTimeout(t);
				},
			});
			
			if (!saveStatus) {
				this.isFetching = false;
				this.$("input,select").removeAttr("disabled");
				button.replaceWith(originalButton);
				clearTimeout(t);
			}
			
			return false;
		}
	});
	
	// Single form in the drop detail metadata listing
	var DropFormView = Backbone.View.extend({
		
		tagName: "div",
		
		className: "filters-type",
		
		events: {
			"click .icon-pencil": "showEditForm",
			"click .icon-cancel": "deleteForm",
		},
		
		initialize: function(options) {
			this.template = _.template($("#drop-dropform-template").html());
		},
		
		render: function() {
			this.$el.html(this.template({
				name: this.options.form.get("name")
			}));
			return this;
		},
		
		showEditForm: function() {
			if (this.isFetching)
				return;	
			
			
			var view = new EditFormModalView({
				baseUrl: this.options.baseUrl,
				model: this.model,
				form: this.options.form
			});
			modalShow(view.render().el);
			return false;
		},
		
		deleteForm: function() {
			var view = this;
			
			if (view.isFetching)
				return;		
				
			view.isFetching = true;
			
			var button = this.$("span.icon-cancel");
			
			// Show loading icon if there is a delay
			var t = setTimeout(function() { button.removeClass("icon-cancel").html(loading_image); }, 500);
			
			this.model.destroy({
				wait: true,
				complete: function() {
					clearTimeout(t);
					view.isFetching = false;
				},
				error: function() {
					showFailureMessage("Unable to remove form. Try again later.");
					button.html("");
					button.addClass("icon-cancel");
				},
				success: function() {
					view.$el.fadeOut("slow")
				}
			});
			return false;
		}
	});

	// Search Filter modal
	var SearchFilterModalView = Backbone.View.extend({
		
		tagName: "article",
		
		className: "modal modal-view",
		
		events:{
			"click .modal-toolbar a.button-submit": "addSearchFilters",
			"click #add-date-filter a": "activateDatePicker",
			"click #add-location-filter a": "activateMap"
		},
		
		map: null,
		
		drawnItems: null,
		
		boundingBox: null,
		
		initialize: function(options){
			this.template = _.template($("#search-filter-modal-template").html());
		},

		activateDatePicker: function(e) {
			this.toggleTabs(e);

			var dateFrom = this.$("input[name=date_from]"),
				dateTo = this.$("input[name=date_to]");
			
			// Short names for the months
			var months = {1: "Jan", 2: "Feb", 3: "Mar", 4:"Apr", 5:"May", 6:"Jun", 7:"Jul", 8:"Aug", 9:"Sep", 10:"Oct", 11:"Nov", 12:"Dec"};
			
			// Callback function to be executed when a date is selected
			var onChangeDate = function(ev) {
				var date = ev.date;
				var dateVal = new String(date.getDate()).length == 1 ? "0" + date.getDate() : date.getDate();
				dateVal += "-" + months[date.getMonth() + 1] + "-" + new String(date.getFullYear()).substring(2);
				$(ev.currentTarget).val(dateVal);
			};
			
			// When the date picker is shown - adjust position
			var onShow = function(ev) {
				var el = $(ev.currentTarget);
				var offset = {left: el.offset().left, top: el.offset().top + el.outerHeight()},
					datePickerEl = $("div.datepicker.dropdown-menu:visible");

				datePickerEl.offset(offset);
				ev.preventDefault();
			};

			var nowTemp = new Date();
			var now = new Date(nowTemp.getFullYear(), nowTemp.getMonth(), nowTemp.getDate(), 0, 0, 0, 0);

			dateFrom.datepicker({format: 'dd-mm-yy'})
				.on('changeDate', onChangeDate)
				.on('show', onShow);
			
			// Disable selecting a date after the current date (today)
			dateTo.datepicker({
				onRender: function(date) {
					return date.valueOf() <= now.valueOf() ? '' : 'disabled';
				}
			}).on('changeDate', onChangeDate)
			.on('show', onShow);
			
			// Display the datepicker dialog within the modal
			$("#modal-container .modal-window").append($("div.datepicker"));
			$("body > div.datepicker").remove();
			
			return false;
		},
		
		toggleTabs: function(e) {
			var el = $(e.currentTarget);
			var modalTabHash = el.prop('hash');

			if (this.$(".modal-tabs-window " + modalTabHash).is(":visible")) {
				return false;
			}

			var view = this;
			this.$('.modal-tabs-window > div.active')
				.removeClass('active')
				.fadeOut(100, function() {
					view.$('.modal-tabs-window ' + modalTabHash)
						.fadeIn('fast')
						.addClass('active');
				});

			this.$('.modal-tabs-menu li').removeClass('active');
			$(el).parent().addClass('active');
			
		},
		
		activateMap: function(e) {
			this.toggleTabs(e);

			// Initialize the map
			if (!this.map) {
				this.map = L.map('location-map').setView([-1.28333, 36.8167], 13);

				L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
					attribution: '&copy; <a href="http://osm.org/copyright">OpenStreetMap</a> contributors'
				}).addTo(this.map);
				
				// Initialze the FeatureGroup to store the editable layers
				this.drawnItems = new L.FeatureGroup();
				this.map.addLayer(this.drawnItems);

				// Draw control
				var drawControl = new L.Control.Draw({
					allowIntersection: false,
					draw: {
						polygon: false,
						polyline: false,
						circle: false,
						marker: false
					},
					edit: {
						featureGroup: this.drawnItems
					}
				});
				
				this.map.addControl(drawControl);
			}

			var context = this;
			var bounds = null;
			// when a new vector has been created
			this.map.on('draw:created', function(e){
				var layer = e.layer;
				context.drawnItems.addLayer(layer);
				bounds = layer.getBounds();
			});
			
			// When drawing is complete
			this.map.on('draw:drawstop', function(e){
				var southWest = bounds.getSouthWest(),
					northEast =  bounds.getNorthEast();

					// Generate the bounding box string
					context.boundingBox = southWest.lat + "," + southWest.lng + ", " + northEast.lat + "," + northEast.lng;
			})

			return false;
		},

		render: function() {
			this.$el.html(this.template());

			return this;
		},
		
		addSearchFilters: function() {
			var searchFilters = {
				keywords: this.$("input[name=keywords]").val(),
				date_from: this.$("input[name=date_from]").val(),
				date_to: this.$("input[name=date_to]").val(),
				locations: this.boundingBox,
			};
			
			var validCount = 0;
			for (key in searchFilters) {
				if (searchFilters[key] != null && $.trim(searchFilters[key]).length > 0) {
					validCount++;
				} else {
					delete searchFilters[key];
				}
			}
			
			if (validCount === 0) {
				showFailureMessage('You have not specified a search filter');
				return;
			}
		
			// Deactivate any active search filters
			// this.options.filtersView.deactivateFilters();
			
			// Check for keywords search filter
			if (searchFilters.keywords) {
				var searchFilterModel = new SearchFilter({
					keywords: searchFilters.keywords
				});
				
				this.options.filtersView.addSearchFilter(searchFilterModel);
			};
			
			// Check for date search filter
			if (searchFilters.date_from || searchFilters.date_to) {
				var searchFilterModel = new SearchFilter();
				if (searchFilters.date_from && searchFilters.date_to) {
					searchFilterModel.set({
						date_from: searchFilters.date_from,
						date_to: searchFilters.date_to
					});
				}
			
				if (searchFilters.date_from && !searchFilters.date_to) {
					searchFilterModel.set({
						date_from: searchFilters.date_from
					});
				}
			
				if (searchFilters.date_to && !searchFilters.date_from) {
					searchFilterModel.set({
						date_to: searchFilters.date_to
					});
				}
				
				this.options.filtersView.addSearchFilter(searchFilterModel);
			}
			
			// Location search filter
			if (searchFilters.locations) {
				var locationSearchFilter = new SearchFilter({locations: searchFilters.locations});
				this.options.filtersView.addSearchFilter(locationSearchFilter);
			}
			
			this.options.filtersView.updateDropFilters();

			modalHide();
			return false;
		}
	});
	
	// View for individual search filters
	var SearchFilterItemView = Backbone.View.extend({
		tagName: "li",
		
		className: "active",

		events: {
			"click a span.remove": "removeSearchFilter",
			"click a": "toggleSearchFilter",
		},
		
		initialize: function(options) {
			this.template = _.template($("#search-filter-item-template").html());
			
			//
			// Set the labels
			// 

			// Keywords
			if (this.model.get('keywords')) {
				this.model.set({
					iconClass: "icon-pencil",
					label: this.model.get('keywords')
				});
			}
			
			// Locations
			if (this.model.get('locations')) {
				this.model.set({
					iconClass: "icon-cog",
					label: this.model.get('locations')
				});
			}

			// Date from and date to
			if (this.model.get('date_from') && this.model.get('date_to')) {
				this.model.set({
					iconClass: "icon-calendar",
					label: this.model.get('date_from') + " to " + this.model.get('date_to'),
					dateRange: true,
				});
			}

			// Date from only
			if (this.model.get('date_from') && !this.model.get('date_to')) {
				this.model.set({
					iconClass: "icon-calendar",
					label: "From " + this.model.get('date_from')
				});
			}
			
			// Date to only
			if (!this.model.get('date_from') && this.model.get('date_to')) {
				this.model.set({
					iconClass: "icon-calendar",
					label: "Until " + this.model.get('date_to')
				});
				
			}
		},
		
		removeSearchFilter: function() {
			this.options.filtersView.removeSearchFilter(this);
			this.$el.fadeOut('slow').remove();
			return false;
		},
		
		toggleSearchFilter: function() {
			if (this.$el.hasClass("active")) {
				this.options.filtersView.removeSearchFilter(this);
				this.$el.removeClass("active");
			} else {
				this.options.filtersView.activateSearchFilter(this);
				this.$el.addClass("active");
			}

			return false;
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}
	})

	// Search filters view
	var DropSearchFiltersView = Drops.DropSearchFiltersView = Backbone.View.extend({
		
		el: "#drop-search-filter",
		
		events: {
			"click a.button-add": "showSearchFilterModal"
		},
		
		initialize: function(options) {
			this.render();
			this.collection = new SearchFilters();

			// Hashtable of active search filters
			this.activeSearchFilters = {};
			
			if (options.dropFilters.get('keywords')) {
				this.collection.add(new SearchFilter({keywords: options.dropFilters.get('keywords')}));
			}

			if (options.dropFilters.get('locations')) {
				this.collection.add(new SearchFilter({locations: options.dropFilters.get('locations')}));
			}
			
			if (options.dropFilters.get('date_from') && options.dropFilters.get('date_to')) {
				this.collection.add(new SearchFilter({
					date_from: options.dropFilters.get('date_from'),
					date_to: options.dropFilters.get('date_to')
				}));
			}
			
			if (options.dropFilters.get('date_from') && !options.dropFilters.get('date_to')) {
				this.collection.add(new SearchFilter({
					date_from: options.dropFilters.get('date_from')
				}));
			}

			if (!options.dropFilters.get('date_from') && options.dropFilters.get('date_to')) {
				this.collection.add(new SearchFilter({
					date_to: options.dropFilters.get('date_to')
				}));
			}

			this.collection.each(this.addSearchFilter, this);
			if (_.size(this.collection)) {
				this.updateDropFilters();
			}
		},
		
		showSearchFilterModal: function() {
			var view = new SearchFilterModalView({filtersView: this});
			modalShow(view.render().el);

			return false;
		},
		
		addSearchFilter: function(searchFilter) {
			var view = new SearchFilterItemView({model: searchFilter, filtersView: this});
			this.$("#search-filter-list").append(view.render().el);
			
			// Store the search model against its view
			this.checkActiveSingletonFilters(view);
			this.activeSearchFilters[view.cid] = view;
		},
		
		activateSearchFilter: function(view) {
			this.checkActiveSingletonFilters(view);
			this.activeSearchFilters[view.cid] = view;
			this.updateDropFilters();
		},
		
		removeSearchFilter: function(view) {
			delete this.activeSearchFilters[view.cid];
			this.updateDropFilters();
		},
		
		// Checks for active date and location filters
		checkActiveSingletonFilters: function(view) {
			var model = view.model.toJSON();
			if (model.date_from || model.date_to || model.locations) {
				for (cid in this.activeSearchFilters) {
					var filterView = this.activeSearchFilters[cid],
						filterModel = filterView.model.toJSON(),
						found = false;
					if (filterModel.dateRange || filterModel.locations) {
						filterView.$el.removeClass("active");
						found = true;
					} else {
						if (filterModel.date_from && model.date_from) {
							filterView.$el.removeClass("active");
							found = true;
						}
						
						if (filterModel.date_to && model.date_to) {
							filterView.$el.removeClass("active");
							found = true;
						}
					}
					if (found) {
						delete this.activeSearchFilters[cid];
					}
					
				}
			}
		},
		
		updateDropFilters: function() {
			var searchFilters = {keywords: [], date_from: null, date_to: null, locations: null};
			for (cid in this.activeSearchFilters) {
				var model = this.activeSearchFilters[cid].model.toJSON();
				if (model.keywords) {
					if (!searchFilters.keywords) {
						searchFilters.keywords = [];
					}
					searchFilters.keywords.push(model.keywords);
				}
				
				if (model.locations) searchFilters.locations = model.locations;
				if (model.date_from) searchFilters.date_from = model.date_from;
				if (model.date_to) searchFilters.date_to = model.date_to;
			}
			this.options.dropFilters.set(searchFilters);
		},
		
		deactivateFilters: function() {
			this.$("#search-filter-list li").removeClass("active");
		},

		render: function() {
			return this;
		}
	});

}(this));