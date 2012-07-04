<script type="text/javascript">
/**
 * Backbone.js wiring for the droplets MVC
 */
$(function() {
	var base_url = "<?php echo $fetch_base_url; ?>";
	var default_view = "<?php echo $default_view; ?>";
	var photos = <?php echo $photos; ?>;
	var sharingEnabled = <?php echo Model_Setting::get_setting('public_registration_enabled'); ?>
	
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
	
	
	// Discussion model
	var Discussion = Backbone.Model.extend();
	
	// Discussion list
	var Discussions = Backbone.Collection.extend({
		model: Discussion
	});
	
	// Discussion view
	var DiscussionView = Backbone.View.extend({
		tagName: "article",
	
		className: "drop base cf",
	
		template: _.template($("#discussion-template").html()),
		
		render: function(eventName) {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},
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
	var Drop = Backbone.Model.extend({
		
		initialize: function() {
			this.set("drop_url", site_url.substring(0, site_url.length-1) + base_url + '/drop/' + this.get("id"));
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
		
	function getDropletListUrl(applyFilters) {
		var f = "";
		
		if (applyFilters) {
			f = filters.getString();
		}
		
		return base_url + "/droplets" + (f.length > 0 ? '?' + f : '');
	}
	
	// Droplet & Bucket collection
	var DropsList = Backbone.Collection.extend({
		model: Drop,
		
		url: getDropletListUrl(true),
		
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
				_.each(model, function(drop) {
					models.push(dropsList.get(drop.id));
				})

				// Custom event that unlike the add event, will contain an
				// array of all the models that were added at once.
				this.trigger("drops", models);
			}
		}
	});
	
	// Create a global drop list to be shared by all views
	var dropsList = new DropsList;
	// New drops via polling will queue in this list
	var newDropsList = new DropsList;
	
	// Common between drop list and full view.
	var DropBaseView = Backbone.View.extend({	
		showAddToBucketModal: function() {
			var addToBucketView = new AddToBucketView({collection: window.bucketList, model: this.model});
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
			if (!sharingEnabled) {
				// Public registration not allowed, show message
				showConfirmationMessage("Drop sharing is not allowed in this deployment");
			} else {
				shareView = new ShareDropView({model: this.model});
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
		},
				
		render: function(eventName) {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},
		
		// Show the drop in zoom view
		showDetail: function() {
			appRouter.navigate("/drop/" + this.model.get("id")  + "/zoom", {trigger: true});
			return false;
		}
	});
	
	// Drop detail in zoom view
	var DropDetailView = DropBaseView.extend({
	
		tagName: "div",
	
		className: "modal drop drop-full col_9",
	
		template: _.template($("#drop-detail-template").html()),
		
		events: {
			"click .add-comment .drop-actions a": "addReply",
			"click li.bucket a.modal-trigger": "showAddToBucketModal",
			"click .settings-toolbar .button-big a": "showFullDrop",
			"click ul.score-drop > li.like a": "likeDrop",
			"click ul.score-drop > li.dislike a": "dislikeDrop",
			"click li.share > a": "shareDrop",
			"click a.button-prev": "showPrevDrop",
			"click a.button-next": "showNextDrop"
		},
		
		initialize: function() {
			this.discussions = new Discussions();
			this.discussions.url = base_url+"/reply/"+this.model.get("id");
			this.discussions.on('reset', this.addDiscussions, this);
			this.discussions.on('add', this.addDiscussion, this);
			
			this.model.on("change:user_score", this.updateDropScore, this);
		},
						
		render: function(eventName) {
			this.$el.html(this.template(this.model.toJSON()));
			this.discussions.reset(this.model.get('discussions'));
			return this;
		},
		
		addDiscussion: function(discussion) {
			var view = new DiscussionView({model: discussion});
			this.$("section.drop-discussion article.add-comment").before(view.render().el);
		},
		
		addDiscussions: function() {
			this.discussions.each(this.addDiscussion, this);
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
			this.discussions.create({droplet_content: $(textarea).val()}, {
				wait: true,
				complete: function() {
					loading_msg.replaceWith(publishButton);
				},
				success: function(model, response) {
					textarea.val("");
					dropDiscussions = drop.get('discussions');
					dropDiscussions.push(model.toJSON());
					drop.set('discussions', dropDiscussions);
				},
				error: function(model, response) {
					var message = "<?php echo __('Uh oh. An error occurred while adding the comment.'); ?>";
					//error_el.html(message).fadeIn("fast").fadeOut(4000).html();
				}
			});
			
			return false;
		},
		
		showFullDrop: function() {
			appRouter.navigate("/drop/" + this.model.get("id"), {trigger: true});
			return false;
		},

		showPrevDrop: function() {
			var index = dropsList.indexOf(this.model)
			if (index === dropsList.length - 1) {
				return false;
			}
			var m = dropsList.at(index + 1);
			appRouter.navigate("/drop/" + m.get("id")  + "/zoom", {trigger: true});
			return false;
		},

		showNextDrop: function() {
			var index = dropsList.indexOf(this.model)
			if (index === 0) {
				return false;
			}
			var m = dropsList.at(index - 1);
			appRouter.navigate("/drop/" + m.get("id")  + "/zoom", {trigger: true});
			return false;
		}
	});
	
	// Bucket in modal view
	var BucketView = Backbone.View.extend({
	
		tagName: "label",
			
		template: _.template($("#bucket-template").html()),
		
		events: {
			"click input": "toggleBucket"
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
	var AddToBucketView = BaseModalAssetListView.extend({
	
		tagName: "article",
	
		className: "modal",
	
		template: _.template($("#add-to-bucket-template").html()),
		
		listSelector: '.select-list',

		listItemSelector: '.select-list label',
		
		initialize: function(options) {
			this.$el.html(this.template(this.model.toJSON()));
			BaseAssetListView.prototype.initialize.call(this, options);
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
		}		
	});
		
	// VIEW: Listing of drops
	var DropsView = Backbone.View.extend({
	
		template: _.template($("#drop-listing-template").html()),
		
		noContentElHidden: false,
		
		events: {
			"click article.alert-message a": "showNewDrops"
		},
				
		initialize: function(options) {
			// Set this view element programatically
			if (options.layout == "list") {
				this.setElement(this.make("article", {"class": "river list"}));
			} else {
				this.setElement(this.make("article", {"class": "river drops cf", "style": "position: relative;"}));
			}

			dropsList.on('reset', this.initDrops, this); 
			dropsList.on('destroy', this.checkEmpty, this);
			
			if (options.layout == "list") {
				// For list layout we can add drops directly, no masonry required
				dropsList.on('add', this.addDrop, this);
			} else {
				// Masonry requires all new drops to be added at once for a smooth
				// animation
				dropsList.on('drops', this.addDrops, this);
			}
			
			newDropsList.on('add', this.alertNewDrops, this);
			newDropsList.on('reset', this.resetNewDropsAlert, this);
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
			var views = [];
			var id = maxId;
			_.each(drops, function(drop) {
				id = Math.max(id, drop.get("sort_id"));
				views.push(new DropView({model: drop, layout: this.options.layout}).render().el);
			}, this)
			
			// Hide the new drops while they are loading
			var $newElems = $(views).css({ opacity: 0 });
			var $container = this.$('#drops-view');
			
			// Add the drops to the view all at once and do masonry
			if (id > maxId) {
				// New drops, prepend them
				$container.prepend($newElems);
			} else {
				// Pagination, append the drops
				$container.append($newElems);
			}
			$newElems.imagesLoaded(function(){
				// show elems now they're ready
				$newElems.animate({ opacity: 1 });
				if (id > maxId) {
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
			var view = new DropView({model: drop, layout: this.options.layout});
			drop.view = view;
			if (this.options.layout == "list") {
				// Ordering is significant for list view
				// and drops will be inserted in the view according to
				// their dates and not simply appended/prepended.
				var index = dropsList.indexOf(drop);
				if (index > 0) {
					// Newer drops are added in the view before drops
					// they follow in the list i.e. newer drops are added
					// on top
					dropsList.at(index-1).view.$el.before(view.render().el);
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
				dropsList.each(this.addDrop, this);
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
			if (!dropsList.length) {
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
			var count = newDropsList.size();
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
			if (isSyncing)
				return;
			
			// Prevent further updates while in here
			isSyncing = true;
			
			dropsList.add(newDropsList.models);
			newDropsList.reset();
			
			// Proceed
			isSyncing = false;
			
			return false;
		},
		
		resetNewDropsAlert: function() {
			this.$("article.alert-message").fadeOut("slow").remove();
		}
	});

	
	// Edit Metadata List Item
	var EditMetadataListItemView = Backbone.View.extend({
	
		tagName: "li",
	
		template: _.template($("#edit-metadata-listitem").html()),
		
		events: {
			"click span.remove-small": "removeMeta",
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
	
		template: _.template($("#add-metadata-template").html()),
		
		events: {
			"click .create-new a": "saveNewMetadata",
			"submit": "saveNewMetadata"
		},
		
		initialize: function() {
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
						message = "<?php echo __('Oops something went wrong. Try again later.'); ?>";
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
		
		template: _.template($("#metadata-template").html()),
		
		events: {
			// Show/Hide the edit buttong
			"mouseover h3": function() { this.$("h3 .button-blue").show(); },
			"mouseout h3": function() { this.$("h3 .button-blue").hide(); },
			
			"click h3 a": "showEditMetadata"
		},
		
		initialize: function() {
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
	var DropFullView = Backbone.View.extend({
	
		tagName: "article",
	
		className: "drop drop-full cf",
		
		template: _.template($("#drop-full-view-template").html()),
		
		render: function() {
			// Render the layout
			this.$el.html(this.template());
			
			// Render the droplet detail
			var detailView = new DropDetailView({model: this.model});
			this.$("article .center .col_3").before(detailView.render().el);
			
			// Render metadata
			var places = new Places;
			places.url = base_url+"/places/"+this.model.get("id");
			places.reset(this.model.get("places"));
			this.addMetadataBlock(places);
			
			var links = new Links;
			links.url = base_url+"/links/"+this.model.get("id");
			links.reset(this.model.get("links"));
			this.addMetadataBlock(links);
			
			var tags = new Tags;
			tags.url = base_url+"/tags/"+this.model.get("id");
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

		template: _.template($("#share-drop-template").html()),

		events: {
			"click li.email > a": "showEmailDialog",
		},

		showEmailDialog: function() {
			var emailView = new EmailDropView({model: this.model});
			modalShow(emailView.render().el);
			return false;
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}
	});

	var EmailDropView = Backbone.View.extend({
		tagName: "article",

		className: "modal",

		template: _.template($("#email-dialog-template").html()),

		events: {
			"click #send_sharing_email > a": "sendEmail"
		},

		sendEmail: function() {
			var data = {};
			$(":input", this.$("form")).each(function(field){
				data[$(this).attr("name")] = $(this).val();
			});
			var context = this;

			// Submit for sharing
			$.ajax({
				url: "<?php echo URL::site().$user->account->account_path.'/share'; ?>",
				
				type: "POST",
				
				data: data,

				success: function(response) {
					// Show success message
					context.$("#success").show();

					// Close the dialog
					setTimeout(function(){context.$("h2.close a").trigger("click");}, 1500);
				},

				error: function() {
					// Show error message
					context.$("#error").show();
				},

				dataType: "json"
			});
			return false;
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}

	});
	
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
				$.get(base_url + "/droplets", data, function(response) {
					// Success, replace the drops list with the new data
					dropsList.reset(response);
					
					// Update the filter
					filters = new Filter(data);
					appRouter.setFilter(filters.getString(), false);
					dropsList.url = newDropsList.url = getDropletListUrl(true);

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
	
	// Bind to the filters button which is our of any of our views.
	$("nav.page-navigation div.filter-actions a").click(function () {
		var view = new FiltersView();
		modalShow(view.render().el);
		return false;
	})
	
	
	// Load content while scrolling - Infinite Scrolling
	var pageNo = 1;
	var maxId = 0;
	var isAtLastPage = false;
	var isPageFetching = false;
	
	function nearBottom(bottomEl) {
		var bufferPixels = 40;
		return $(document).height() - $(window).scrollTop() - $(window).height() - bufferPixels < $(document).height() - bottomEl.offset().top;
	}
	
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
	
	// Update since id whenever a droplet is added to the collection
	function updateSinceId (droplet) {
		if (parseInt(droplet.get("sort_id")) > sinceId) {
			sinceId = parseInt(droplet.get("sort_id"));
		}
	}
	newDropsList.on('add', updateSinceId, this);
	
	// Check if polling is enabled
	<?php if ($polling_enabled): ?>
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
	}, 30000);
	<?php endif; ?>
			
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
				this.view = new DropsView({layout: layout});
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
			var fullView = new DropFullView({model: drop}).render();
			fullView.$('.settings-toolbar').remove();
			$("#content").append(fullView.el);
		},
		
		dropZoomView: function(id) {
			var drop = dropsList.get(id);
			var detailView = new DropDetailView({model: drop});
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
				
		defaultRoute: function(actions){
			if (default_view == 'drops') {
				this.navigate("drops", {trigger: true, replace: true});
			} else {
				this.navigate("list", {trigger: true, replace: true});
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
	window.appRouter = new AppRouter;
	// Start Backbone history
	Backbone.history.start({pushState: true, root: base_url + "/"});

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
