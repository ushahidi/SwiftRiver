<script type="text/javascript">
/**
 * Backbone.js wiring for the droplets MVC
 */
$(function() {
	window.base_url = "<?php echo $fetch_base_url; ?>";
	window.dropletFetchURL = "<?php echo $droplet_fetch_url; ?>";
	
	// Models for the droplet places, tags and links 
	window.DropletPlace = Backbone.Model.extend();
	window.DropletTag = Backbone.Model.extend();
	window.DropletLink = Backbone.Model.extend();
	window.Bucket = Backbone.Model.extend({
		defaults: {
			account_id: <?php echo $user->account->id ?>
		},
		initialize: function() {
			if (parseInt(this.get("account_id")) != <?php echo $user->account->id ?>) {
				this.set('bucket_name', this.get("account_path") + "/" + this.get("bucket_name"));
			}
		}
	});
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
	})
	
	// Collection for all the buckets accessible to the current user
	window.BucketList = Backbone.Collection.extend({
		model: Bucket,
		url: "<?php echo url::site().$user->account->account_path.'/bucket/buckets/manage'; ?>"
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
	
	// Droplet & Bucket collection
	window.DropletList = Backbone.Collection.extend({
		
		model: Droplet,
		
		url: window.dropletFetchURL,

		filterDroplets: function(filters) {
			window.isPageFetching = true;

			_.each(filters, function(data, param){
				var dataStr = getParameterByName(param);
				dataStr += (dataStr != "") ? "," : "";

				if (Object.prototype.toString.call(data) === "[object Array]") {
					dataStr += data.toString();
				}
				if (typeof data == "string") {
					dataStr += data;
				}
				if (dataStr == "") {
					delete filters[param];
				} else {
					// Modiy the value of the filter param
					filters[param] = dataStr;
				}

			});

			// Halt if no filters present
			if (_.size(filters) == 0)
				return;

			// Proceed....

			// Ensures that the server response includes the modified request URL
			filters.return_url = true;
			
			var list = this;

			// Fetch the data
			$.ajax({
				url: window.dropletFetchURL,

				data: filters,

				success: function(response) {
					// Clear the current droplet listing
					window.dropletList.$el.fadeOut('slow');
					window.dropletList.$el.html("");

					window.dropletList.$el.fadeIn('slow');

					// Render
					list.url = response.fetch_url;
					list.reset(response.droplets);

					// Update the URL
					if (typeof window.history.pushState != "undefined") {
						window.history.pushState(response, "Droplets", response.base_url);
					}

					window.isPageFetching = false;
				},

				error: function(response) {
					window.isPageFetching = false;
				},

				dataType: "json"
			});
		}
	});
	
	window.Droplets = new DropletList;
	window.bucketList = new BucketList();
	
	// Rendering for a single droplet in the list view
	window.DropletView = Backbone.View.extend({
	
		tagName: "article",
	
		className: "item",
	
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
		},
		
		// When cancel buttons is clicked in the add bucket drop down
		cancelCreateBucket: function (e) {
			this.$("div.dropdown div.create-name").fadeOut();
			this.$("div.dropdown p.create-new a.plus").fadeIn();
			e.stopPropagation();
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
	
	// The droplest list
	window.DropletListView = Backbone.View.extend({
	
		el: $("#droplet-list"),
		
		noContentElHidden: false,
				
		initialize: function() {
			Droplets.on('add',	 this.addDroplet, this);
			Droplets.on('reset', this.addDroplets, this); 
			Droplets.on('destroy', this.checkEmpty, this); 
		},
		
		addDroplet: function(droplet) {

			var view = new DropletView({model: droplet});

			// Recent items populate at the top othewise append
			if (maxId && droplet.get('id') > maxId) {
				this.$el.prepend(view.render().el);
			} else {
				this.$el.append(view.render().el);
			}
			
			if (!this.noContentElHidden) {
				this.hideNoContentEl();
			}
			
		},
		
		addDroplets: function() {
			Droplets.each(this.addDroplet, this);
			if (Droplets.length) {
				this.hideNoContentEl();
			}
		},
		
		hideNoContentEl: function() {
			this.$("h2.no-content").hide();
			this.noContentElHidden = true;
		},
		
		checkEmpty: function() {
			if (!Droplets.length) {
				this.$("h2.no-content").show();
				this.noContentElHidden = false;
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
			var tagId = [$(e.currentTarget).data("droplet-tag-id")];

			// Apply the tags filter
			Droplets.filterDroplets({tags: tagId});

			return false;
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
			$(e.currentTarget).toggleClass("active").toggleClass("");

			// Get all the selected place names
			var placeId = [$(e.currentTarget).data("droplet-place-id")];

			// Apply the places filter
			Droplets.filterDroplets({places: placeId});

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
			droplet = Droplets.get(this.model.get('droplet_id'));
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
	window.isPageFetching = false;
	var isAtLastPage = false;
	
	function nearBottom() {
		var bufferPixels = 40;
		return $(document).height() - $(window).scrollTop() - $(window).height() - bufferPixels < $(document).height() - $("#next_page_button").offset().top;
	}

	function getParameterByName(name) {
		name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\[");
		var regexStr = "[\\?&]" + name + "=([^&#]*)";
		var regex = new RegExp(regexStr);

		var results = regex.exec(window.location.search);

		return (results == null) ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
	}
	
	var loading_msg = window.loading_message.clone();
	$(window).scroll(function() {
	    
		if (nearBottom() && !isPageFetching && !isAtLastPage) {
			// Advance page and fetch it
			isPageFetching = true;
			pageNo += 1;		
			
			// Hide the navigation selector and show a loading message				
			loading_msg.appendTo($("#next_page_button")).show();
			
			Droplets.fetch({
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
	
			
	// Poll for new droplets
	// We will only fetch droplets with and id newer than this
	var sinceId = 0;
	var isSyncing = false;
	
	// Update our sinceId when new droplets are added
	Droplets.on("add", function(droplet) {
 		if (parseInt(droplet.get("id")) > sinceId) {
			sinceId = parseInt(droplet.get("id"));
		}
	});
	
	// Poll for new droplets every 30 seconds
	setInterval(function() { 
		if (!isSyncing) {
			isSyncing = true;
			Droplets.fetch({data: {since_id: sinceId}, 
 			    add: true, 
			    complete: function () {
			        isSyncing = false;
			    }
			});   
		}		    
	}, 
	30000);
			
	// Bootstrap the droplet list
	window.dropletList = new DropletListView;
	Droplets.reset(<?php echo $droplet_list; ?>);		
	bucketList.reset(<?php echo $bucket_list; ?>);
	
	// Set the maxId after inital rendering of droplet list
	maxId = sinceId = <?php echo $max_droplet_id ?>;
	
});
</script>