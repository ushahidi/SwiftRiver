<script type="text/javascript">
/**
 * Backbone.js wiring for the droplets MVC
 */
$(function() {
	var base_url = "<?php echo $fetch_base_url; ?>";
	var filters = "<?php echo $filters; ?>";
	
	// Models for the droplet places, tags and links 
	window.DropletPlace = Backbone.Model.extend();
	window.DropletTag = Backbone.Model.extend();
	window.DropletLink = Backbone.Model.extend();
	window.Discussion = Backbone.Model.extend();
	window.DropletScore = Backbone.Model.extend();
			
	// Collections for droplet places, tags and links
	window.DropletPlaceCollection = Backbone.Collection.extend({
		model: DropletPlace
	})

	window.DropletTagCollection = Backbone.Collection.extend({
		model: DropletTag,
	});

	window.DropletLinkCollection = Backbone.Collection.extend({
		model: DropletLink
	});
	
	// Collection for the buckets a droplet a droplet belongs to
	window.DropletBucketsCollection = Backbone.Collection.extend({
		model: Bucket
	});
	
	// Collection for a droplet's discussions
	window.DropletDiscussionsCollection = Backbone.Collection.extend({
		model: Discussion,
	});
	

	// Droplet model
	window.Droplet = Backbone.Model.extend({
		
		// Add/Remove a droplet from a bucket
		setBucket: function(changeBucket) {
			// Is this droplet already in the bucket?
			change_buckets = this.get("buckets");
			if (_.any(change_buckets, function(bucket) { return bucket["id"] == changeBucket.get("id"); })) {
				// Remove the bucket from the list
				change_buckets = _.filter(change_buckets, function(bucket) { return bucket["id"] != changeBucket.get("id"); });
				this.set('buckets', change_buckets);
			} else {
				change_buckets.push({id: changeBucket.get("id"), bucket_name: changeBucket.get("bucket_name")});
			}
			
			this.save({buckets: change_buckets}, {wait: true});
		},
		
		// Score the droplet
		score: function(val) {
			var change = val > 0 ? 1 : -1
			var dropletScore = new DropletScore({
				droplet_id: this.get("id"),
				user_id: <?php echo $user->id ?>,
				score: change
			});
			var newScore = parseInt(this.get("scores") ? this.get("scores") : 0) + change;
			var currentUserScore = parseInt(this.get("user_score") ? this.get("user_score") : 0);
			
			if (currentUserScore != change) {
				// Only update 'scores' when user_score is different
				this.save({droplet_score: dropletScore, scores: newScore, user_score: change});
			}
		}
	});
	
	function getDropletListUrl(filters) {
		return base_url + "/droplets" + (filters.length > 0 ? '?' + filters : '');
	}
	
	// Droplet & Bucket collection
	window.DropletList = Backbone.Collection.extend({
		model: Droplet,
		
		url: getDropletListUrl(filters),
		
		comparator: function (droplet) {
			return Date.parse(droplet.get('droplet_date_pub'));
		}
	});
	
	
	// Rendering for a single droplet in the list view
	window.DropletView = Backbone.View.extend({
	
		tagName: "article",
	
		className: "drop base cf",
	
		template: _.template($("#droplet-template").html()),
		
		events: {
			// Show the droplet detail
			"click .button-view a.detail-view": "showDetail",
			"click div.bottom a.close": "showDetail",
			"click div.summary section.content hgroup a": "showDetail",
			
			// Show the list of buckets available to the current user
			"click .bucket a.bucket-view": "showBuckets",				
			"click .bucket .dropdown p.create-new a": "showCreateBucket",
			"click div.create-name button.cancel": "cancelCreateBucket",
			"click div.create-name button.save": "saveBucket",
			"keypress div.create-name #new-bucket-name": "checkSaveBucket",
			
			// Droplet deletion
			"click ul.delete-droplet li.confirm a": "deleteDroplet",
			
			// Droplet comments
			"click .discussion .add-reply .button-go > a": "addReply",
			
			// Handle tag creation
			"click .detail section.meta #add-tag a": "showCreateTag",
			"click .detail section.meta #add-tag button.cancel": "cancelCreateTag",
			"click .detail section.meta #add-tag button.save": "saveTag",
			
			//Droplet scoring
			"click div.summary section.source div.actions .dropdown .confirm": "saveUseful",
			"click div.summary section.source div.actions .dropdown .not_useful": "saveNotUseful"
		},
					
		initialize: function() {
			bucketList.on('add', this.addBucket, this); 
			
			// List of general tags for the droplet
			this.tagList = new DropletTagCollection();
			this.tagList.url = base_url+"/tags/"+this.model.get("id");
			this.tagList.on('reset', this.addTags, this);
			this.tagList.on('add', this.addTag, this);
			
			// List of links in a droplet
			this.linkList = new DropletLinkCollection();
			this.linkList.on('reset', this.addLinks, this); 
			 
			// List of links in a droplet
			this.placeList = new DropletPlaceCollection();
			this.placeList.on('reset', this.addPlaces, this);
			
			// List of discussions
			this.discussionsList = new DropletDiscussionsCollection();
			this.discussionsList.url = base_url+"/reply/"+this.model.get("id");
			this.discussionsList.on('reset', this.addDiscussions, this);
			this.discussionsList.on('add', this.showDiscussion, this);
			
			// Listen for score total update
			this.model.on("change:scores", this.updateScoreCount, this)

		},
		
		render: function(eventName) {
			$(this.el).attr("data-droplet-id", this.model.get("id"));
			$(this.el).html(this.template(this.model.toJSON()));
			this.tagList.reset(this.model.get('tags'));
			this.linkList.reset(this.model.get('links'));
			this.placeList.reset(this.model.get('places'));
			this.discussionsList.reset(this.model.get('discussions'));
			return this;
		},
		
		addTag: function(tag) {
			var view = new TagView({model: tag});
			this.$("ul.tags").append(view.render().el);
		},
					
		addTags: function() {
			this.tagList.each(this.addTag, this);
		},

		addLink: function(link) {
			var view = new LinkView({model: link});
			this.$("div.links").append(view.render().el);
		},
					
		addLinks: function() {
			this.linkList.each(this.addLink, this);
		},

		addPlace: function(place) {
			var view = new PlaceView({model: place});
			this.$("ul.places").append(view.render().el);
		},
					
		addPlaces: function() {
			this.placeList.each(this.addPlace, this);
		},
					
		addBucket: function() {
			this.createBucketMenu();				
		},
		
		addDiscussion: function(discussion) {
			var view = new DiscussionView({model: discussion});
			this.$("section.discussion > div.loading").before(view.render().el);
		},
		
		addDiscussions: function() {
			this.discussionsList.each(this.addDiscussion, this);
		},
		
		// Add and animate a discussion
		showDiscussion: function(discussion) {
			var view = new DiscussionView({model: discussion});
			view.$el.hide();
			this.$("section.discussion > div.loading").before(view.render().el);			
			view.$el.fadeIn("slow");
		},
		
		
		// Creates the buckets dropdown menu
		createBucketMenu: function() {				
			var dropdown = this.$(".actions .bucket div.dropdown .buckets-list ul");
			dropdown.empty();				
			var droplet = this.model
			
			// Render the buckets
			bucketList.each(function (bucket) {
				// Attach the droplet's buckets' to the model
				// Buckets this droplet belongs to will appear selected
				bucket.set('droplet_buckets', droplet.get('buckets'));
				bucket.set('droplet_id', droplet.get('id'));
				
				var bucketView = new BucketView({model: bucket});
				dropdown.append(bucketView.render().el);
			});
		},
	
		// Event callback for the "view more detail" action
		showDetail: function() {				
			var button = this.$(".button-view a.detail-view");
		
			// Display the droplet detail
			if (button.hasClass("detail-hide")) {
				button.removeClass('detail-hide');
				this.$('div.drawer').slideUp('slow');
				
				// If the drawer is closed from the bottom link and the
				// top of the droplet is hidden, scroll to the top of the
				// droplet to avoid losing the current position in the list
				if (this.$el.offset().top < $(window).scrollTop()) {
					$("html,body").animate({
						scrollTop: this.$el.offset().top
					}, 600);
				}
			} else {
				button.addClass('detail-hide');
				this.$('div.drawer').slideDown('slow');
				
				// Only show droplet content when drawer is open
				this.$("article.fullstory div span").html(this.model.get("droplet_content"));				
			}
		},
		
		// Event callback for the "Add to Bucket" action
		showBuckets: function(e) {
			var parentEl = $(e.currentTarget).parent("p");
			parentEl.toggleClass("active");
			
			var dropdown = $("div.dropdown", parentEl.parent("div"));
			
			if (parentEl.hasClass("active")) {					
				this.createBucketMenu();
				dropdown.show();
			} else {
				dropdown.hide();
			}
			return false;
		},
		
		// Event callback for the "create & add to a new bucket" action
		showCreateBucket: function(e) {
			this.$("div.dropdown div.create-name").fadeIn();
			this.$("div.dropdown p.create-new a.plus").fadeOut();
			this.$("div.create-name #new-bucket-name").focus();
		},
		
		// When cancel buttons is clicked in the add bucket drop down
		cancelCreateBucket: function (e) {
			this.$("div.dropdown div.create-name").fadeOut();
			this.$("div.dropdown p.create-new a.plus").fadeIn();
			e.stopPropagation();
		},
		
		// When enter key pressed in new bucket name input
		checkSaveBucket: function (e) {
			if(e.which == 13){
				this.saveBucket(e);
			}
		},
		
		// When save button is clicked in the add bucket drop down
		saveBucket: function(e) {
			if(this.$("#new-bucket-name").val()) {
				var create_el = this.$("div.dropdown div.create-name");
				create_el.fadeOut();
				
				var loading_msg = window.loading_message.clone();
				loading_msg.appendTo(this.$(".bucket .dropdown div.loading")).show();
				
				var button = this.$("div.dropdown p.create-new a.plus");
				var error_el = this.$(".bucket .dropdown div.system_error");
				
				var input_el = this.$("#new-bucket-name");
				
				bucketList.create({bucket_name: this.$("#new-bucket-name").val()}, {
					wait: true,
					complete: function() {						
						loading_msg.fadeOut();
					},
					success: function() {
						button.fadeIn();
						input_el.val('');
					},
					error: function(model, response) {
						var message = "<ul>";
						if (response.status == 400) {
							errors = JSON.parse(response.responseText)["errors"];
							_.each(errors, function(error) { message += "<li>" + error + "</li>"; });
						} else {
							message += "<?php echo __('An error occurred while saving the bucket.'); ?>";
						}
						message += "</ul>";
						// Show error message and fade it out slooooowwwwwwlllllyyyy
						error_el.html(message).fadeIn("fast").fadeOut(4000).html();
						create_el.fadeIn();
					}
				});				
			}
			e.stopPropagation();
		},
		
		// When delete droplet button is clicked in the droplet detail view
		deleteDroplet: function(e) {
			var viewItem = this;
			
			// Delete on the server
			this.model.destroy({
				wait: true,
				success: function(model, response) {
					$(viewItem.el).hide("slow");
				}
			});
			
			return false;
		},
		
		// When add reply is clicked
		addReply: function(e) {
			var textarea = this.$(".discussion .add-reply :input");
			
			var create_el = this.$("section.discussion article.add-reply");
			create_el.fadeOut();
			
			var loading_msg = window.loading_message.clone();
			loading_msg.appendTo(this.$("section.discussion div.loading")).show();
			
			var error_el = this.$("section.discussion div.system_error");
			
			if ($(textarea).val() != null) {
				this.discussionsList.create({
					droplet_content: $(textarea).val()
				},
				{
					wait: true,
					complete: function() {
						loading_msg.fadeOut();
						create_el.fadeIn();
					},
					success: function(model, response) {
						textarea.val("");
					},
					error: function(model, response) {
						var message = "<?php echo __('Uh oh. An error occurred while adding the comment.'); ?>";
						error_el.html(message).fadeIn("fast").fadeOut(4000).html();
					}
				}); // end save
				
			} // end if 
		},

		// When add tag link is clicked
		showCreateTag: function() {
			this.$(".detail section.meta #add-tag .create-name").fadeIn();
			this.$(".detail section.meta #add-tag p.button-change").fadeOut();
		},
		
		// When cancel button in the add tag is clicked
		cancelCreateTag: function() {
			this.$(".detail section.meta #add-tag .create-name").fadeOut();
			this.$(".detail section.meta #add-tag p.button-change").fadeIn();
		},
		
		
		// When save button in the add tag is clicked
		saveTag: function() {
			if(this.$("#new-tag-name").val()) {
				var create_el = this.$(".detail section.meta #add-tag .create-name");
				create_el.fadeOut();
				
				var button = this.$(".detail section.meta #add-tag p.button-change");
				var input_el = this.$("#new-tag-name");
				
				var loading_msg = window.loading_message.clone();
				loading_msg.appendTo(this.$(".detail section.meta div.item ul.tags").next("div.loading")).show();
				
				var error_el = this.$(".detail section.meta div.item ul.tags").siblings("div.system_error");
				
				this.tagList.create({droplet_id: this.model.get("id") ,tag: this.$("#new-tag-name").val()}, {wait: true,
					complete: function() {
						loading_msg.fadeOut();
					},
					success: function() {						
						button.fadeIn();
						input_el.val('');
					},
					error: function() {
						var message = "<?php echo __('An error occurred while adding the tag.'); ?>";
						error_el.html(message).fadeIn("fast").fadeOut(4000).html();
						create_el.fadeIn();
					}
				});				
			}
		},
		
		// When "this is useful" link is clicked
		saveUseful: function () {
			this.model.score(1);
			this.$('div.summary section.source .dropdown li.confirm').hide();
			this.$('div.summary section.source .dropdown li.not_useful').show();
		},

		// When "this is not useful" link is clicked
		saveNotUseful: function () {
			this.model.score(-1);
			this.$('div.summary section.source .dropdown li.confirm').show();
			this.$('div.summary section.source .dropdown li.not_useful').hide();
		},
		
		updateScoreCount: function(val) {
			this.$('div.summary section.source p.score a').addClass('scored');
			this.$('div.summary section.source .dropdown').fadeOut('fast').children("p").show();
			this.$("div.summary section.source p.score span").html(this.model.get("scores"));
		}
	});
	
	// VIEW: Drops
	window.DropletDropsView = Backbone.View.extend({
	
		el: $("#drops-view-drops"),
		
		noContentElHidden: false,
				
		initialize: function() {
			this.droplets = new DropletList;
			this.droplets.on('add',	 this.addDroplet, this);
			this.droplets.on('add',	 this.updateSinceId, this);
			this.droplets.on('reset', this.addDroplets, this); 
			this.droplets.on('destroy', this.checkEmpty, this);
			
			// Poll for new droplets every 30 seconds
			context = this;
			callback = this.pollNewDroplets;
			setInterval(function() {
				callback.call(context);
			}, 30000);
		},
		
		addDroplet: function(droplet) {

			var view = new DropletView({model: droplet});
			droplet.view = view;
			
			// Droplets add themselves in the view sorted
			// according to published date.
			var index = this.droplets.indexOf(droplet);
			if (index > 0) {
				// Newer droplets are added in the view before droplets
				// they follow in the list i.e. newer droplets are added
				// on top
				this.droplets.at(index-1).view.$el.before(view.render().el);
			} else {
				// First droplet is simply appended in the view
				this.$el.append(view.render().el);
			}
			
			if (!this.noContentElHidden) {
				this.hideNoContentEl();
			}
			
		},
		
		addDroplets: function() {
			this.$('article.item').remove();
			this.droplets.each(this.addDroplet, this);
			if (this.droplets.length) {
				this.hideNoContentEl();
			}
			this.checkEmpty();
		},
		
		hideNoContentEl: function() {
			this.$(".no-content").hide();
			this.noContentElHidden = true;
		},
		
		checkEmpty: function() {
			if (!this.droplets.length) {
				this.$(".no-content").show();
				this.noContentElHidden = false;
			}
		},
		
		filterDroplets: function(filters) {
			// If there is another ajax request, try again shortly
			if (isPageFetching || this.isSyncing) {
				context = this;
				callback = this.filterDroplets;
				setTimeout(function() { callback.call(context, filters); }, 100);
				return;
			}
			isPageFetching = true;
			this.isSyncing = true
			
			// Generate the new filter url parameters
			var new_filter_arr = [];
			for(var p in filters) {
				new_filter_arr.push(encodeURIComponent(p) + "=" + encodeURIComponent(filters[p]));
			}
			var new_filter = new_filter_arr.join("&");
						
			var dropletList = this;
			dropletList.droplets.url = getDropletListUrl(new_filter);
			this.droplets.fetch({
				complete: function() {
					isPageFetching = false;
					dropletList.isSyncing = false;
				},
				success: function (model, response) {
					// Reset pagination
					pageNo = 1;
					isAtLastPage = false;
					
					if (typeof window.history.pushState != "undefined") {
						window.history.pushState(response, "Droplets", base_url + '?' + new_filter);
					}
			    }
			});
		},
		
		sinceId: 0,
		
		isSyncing: false,
		
		updateSinceId: function(droplet) {
			if (parseInt(droplet.get("sort_id")) > this.sinceId) {
				this.sinceId = parseInt(droplet.get("sort_id"));
			}
		},
		
		pollNewDroplets: function() { 
			if (!this.isSyncing) {
				this.isSyncing = true;
				var dropletList = this;
				this.droplets.fetch({data: {since_id: this.sinceId}, 
				    add: true, 
				    complete: function () {
				        dropletList.isSyncing = false;
				    }
				});   
			}		    
		}
		
	});

	// VIEW: List
	window.DropletListView = Backbone.View.extend({
	
		el: $("#drops-view-list"),
		
		noContentElHidden: false,
				
		initialize: function() {
			this.droplets = new DropletList;
			this.droplets.on('add',	 this.addDroplet, this);
			this.droplets.on('add',	 this.updateSinceId, this);
			this.droplets.on('reset', this.addDroplets, this); 
			this.droplets.on('destroy', this.checkEmpty, this);
			
			// Poll for new droplets every 30 seconds
			context = this;
			callback = this.pollNewDroplets;
			setInterval(function() {
				callback.call(context);
			}, 30000);
		},
		
		addDroplet: function(droplet) {

			var view = new DropletView({model: droplet});
			droplet.view = view;
			
			// Droplets add themselves in the view sorted
			// according to published date.
			var index = this.droplets.indexOf(droplet);
			if (index > 0) {
				// Newer droplets are added in the view before droplets
				// they follow in the list i.e. newer droplets are added
				// on top
				this.droplets.at(index-1).view.$el.before(view.render().el);
			} else {
				// First droplet is simply appended in the view
				this.$el.append(view.render().el);
			}
			
			if (!this.noContentElHidden) {
				this.hideNoContentEl();
			}
			
		},
		
		addDroplets: function() {
			this.$('article.item').remove();
			this.droplets.each(this.addDroplet, this);
			if (this.droplets.length) {
				this.hideNoContentEl();
			}
			this.checkEmpty();
		},
		
		hideNoContentEl: function() {
			this.$(".no-content").hide();
			this.noContentElHidden = true;
		},
		
		checkEmpty: function() {
			if (!this.droplets.length) {
				this.$(".no-content").show();
				this.noContentElHidden = false;
			}
		},
		
		filterDroplets: function(filters) {
			// If there is another ajax request, try again shortly
			if (isPageFetching || this.isSyncing) {
				context = this;
				callback = this.filterDroplets;
				setTimeout(function() { callback.call(context, filters); }, 100);
				return;
			}
			isPageFetching = true;
			this.isSyncing = true
			
			// Generate the new filter url parameters
			var new_filter_arr = [];
			for(var p in filters) {
				new_filter_arr.push(encodeURIComponent(p) + "=" + encodeURIComponent(filters[p]));
			}
			var new_filter = new_filter_arr.join("&");
						
			var dropletList = this;
			dropletList.droplets.url = getDropletListUrl(new_filter);
			this.droplets.fetch({
				complete: function() {
					isPageFetching = false;
					dropletList.isSyncing = false;
				},
				success: function (model, response) {
					// Reset pagination
					pageNo = 1;
					isAtLastPage = false;
					
					if (typeof window.history.pushState != "undefined") {
						window.history.pushState(response, "Droplets", base_url + '?' + new_filter);
					}
			    }
			});
		},
		
		sinceId: 0,
		
		isSyncing: false,
		
		updateSinceId: function(droplet) {
			if (parseInt(droplet.get("sort_id")) > this.sinceId) {
				this.sinceId = parseInt(droplet.get("sort_id"));
			}
		},
		
		pollNewDroplets: function() { 
			if (!this.isSyncing) {
				this.isSyncing = true;
				var dropletList = this;
				this.droplets.fetch({data: {since_id: this.sinceId}, 
				    add: true, 
				    complete: function () {
				        dropletList.isSyncing = false;
				    }
				});   
			}		    
		}
		
	});
	
	// View for an individual tag
	window.TagView = Backbone.View.extend({
		
		tagName: "li",

		className: "tag",
		
		template: _.template($("#tag-template").html()),
		
		events: {
			"click span.actions .dropdown .confirm": "deleteTag",

			"click a.tag-name": "applyTagsFilter"
		},

		render: function() {
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		},
		
		deleteTag: function() {
			var viewItem = this.$el;
			// Delete on the server
			this.model.destroy({
				wait: true,
				success: function(model, response) {
					// Remove from UI
					viewItem.fadeOut("slow");
				}
			});
		},

		applyTagsFilter: function(e) {
			dropletList.filterDroplets({tags: this.model.get('tag')});
		}
		
	});

	// View for an individual link
	window.LinkView = Backbone.View.extend({
		
		tagName: "p",
		
		template: _.template($("#link-template").html()),

		render: function() {
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		},
		
	});

	// View for an individual place
	window.PlaceView = Backbone.View.extend({
		
		tagName: "li",

		template: _.template($("#place-template").html()),

		events: {
			"click a.place-name": "applyPlacesFilter",
		},
		
		applyPlacesFilter: function(e) {
			dropletList.filterDroplets({places: this.model.get('place_name')});

		},

		render: function() {
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		},
		
	});
		
	
	// View for individual bucket item in a droplet list dropdown
	window.BucketView = Backbone.View.extend({
		
		tagName: "li",
		
		className: "checkbox",
		
		template: _.template($("#bucket-template").html()),
		
		events: {
			"click li.checkbox a": "toggleDropletMembership"
		},
					
		render: function() {
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		},
		
		// Toggles the bucket membership of a droplet
		toggleDropletMembership: function(e) {
			droplet = dropletList.droplets.get(this.model.get('droplet_id'));
			droplet.setBucket(this.model);
			this.$("li.checkbox a").toggleClass("selected");
		}
	});
	
	// View for individual discussion items
	window.DiscussionView = Backbone.View.extend({
		tagName: "article",
		
		className: "item",
		
		template: _.template($("#discussion-item-template").html()),
		
		render: function() {
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		}
		
	});
	
	// Load content while scrolling - Infinite Scrolling
	var pageNo = 1;
	var maxId = 0;
	var isAtLastPage = false;
	var isPageFetching = false;
	
	function nearBottom() {
		var bufferPixels = 40;
		return $(document).height() - $(window).scrollTop() - $(window).height() - bufferPixels < $(document).height() - $("#next_page_button").offset().top;
	}

	
	var loading_msg = window.loading_message.clone();
	$(window).scroll(function() {
	    
		if (nearBottom() && !isPageFetching && !isAtLastPage) {
			// Advance page and fetch it
			isPageFetching = true;
			pageNo += 1;		
			
			// Hide the navigation selector and show a loading message				
			loading_msg.appendTo($("#next_page_button")).show();
			
			dropletList.droplets.fetch({
			    data: {
			        page: pageNo, 
			        max_id: maxId
			    }, 
			    add: true, 
			    complete: function (model, response) {
			        isPageFetching = false;
			        loading_msg.fadeOut('normal');
			    },
			    error: function (model, response) {
			        if (response.status == 404) {
			            isAtLastPage = true;
			        }
			    }
			});
	    }
	});
	
			
	// Bootstrap the droplet list
	window.dropletList = new DropletListView;
	dropletList.droplets.reset(<?php echo $droplet_list; ?>);
	
	// Set the maxId after inital rendering of droplet list
	maxId = dropletList.sinceId = <?php echo $max_droplet_id ?>;
	
});
</script>