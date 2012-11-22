/**
 * Assets module
 */
(function (root) {
	
	// Init the module
	Assets = root.Assets = {};
	
	// Base object for rivers and buckets
	var Asset = Assets.Asset = Backbone.Model.extend({

		defaults: {
			account_id: logged_in_account
		},

		initialize: function() {
			// Namespace bucket name if the logged in user is not the owner
			this.set('name_namespaced', this.get("account_path") + " / " + this.get("name"));
			if (parseInt(this.get("account_id")) != logged_in_account) {
				this.set('display_name', this.get("name_namespaced"));
			} else  {
				this.set('display_name', this.get("name"));
			}
		},

		toggleSubscription: function (success_callback, error_callback, complete_callback) {
			this.save({subscribed: !this.get('subscribed')}, {
				wait: true,
				success: success_callback,
				error: error_callback,
				complete: complete_callback
				});
		},

		toggleSubscriptionNoSync: function () {
			this.set('subscribed', !this.get('subscribed'));

			// Since we cannot toggle subscription for our buckets
			// because a delete button is shown or nothing at all
			this.set('is_owner', false);
			this.set('collaborator', false);
		},

		// A model can have multiple views using it
		setView: function(key, view) {
			if (typeof(this.views) === 'undefined') {
				this.views = {};
			}
			this.views[key.cid] = view;
		},

		getView: function(key) {
			if (typeof(this.views) === 'undefined') {
				return;
			}
			return this.views[key.cid];
		}
	});
	var Bucket = Assets.Bucket = Asset.extend();
	var River = Assets.River = Asset.extend();

	// Base collection for rivers and buckets
	var AssetList = Assets.AssetList = Backbone.Collection.extend({
		own: function() {
			return this.filter(function(bucket) { 
				return !bucket.get('subscribed') && bucket.get('is_owner'); 
			});
		},

		collaborating: function() {
			return this.filter(function(bucket) { 
				return bucket.get('subscribed') && bucket.get('collaborator'); 
			});
		}
	});

	// Collection for all the buckets accessible to the current user
	var RiverList = Assets.RiverList = AssetList.extend({
		model: River,

		url: site_url + logged_in_account_path + "/river/rivers/manage"
	});
	// Global river list
	var riverList = Assets.riverList = new RiverList();


	// Collection for all the buckets accessible to the current user
	var BucketList = Assets.BucketList = AssetList.extend({
		model: Bucket,

		url: site_url + logged_in_account_path + "/bucket/buckets/manage"
	});
	// Global bucket list
	var bucketList = Assets.bucketList = new BucketList();

	// Common view for a single river / bucket
	var BaseAssetView = Assets.BaseAssetView = Backbone.View.extend({

		tagName: "div",

		className: "parameter",

		events: {
			"click div.actions .button-white a": "toggleSubscription",
			"click div.actions .remove-small a": "deleteAsset"
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},

		deleteAsset: function() {
			var message = 'Delete <a href="#">' + this.model.get('display_name') + "</a>?";
			new ConfirmationWindow(message, function() {
				var loading_msg = window.loading_image.clone();
				var button = this.$(".remove-small");
				var t = setTimeout(function() { button.replaceWith(loading_msg); }, 500);
				asset = this;
				this.model.destroy({
					success: function() {
						message = '<a href="#">' + asset.model.get('display_name') + "</a> has been deleted.";
						showConfirmationMessage(message);
						asset.$el.fadeOut("slow", function () {
							$(this).remove();
						});
					},
					error: function() {
						showConfirmationMessage('Oops, unable to delete <a href="#">' + asset.model.get('display_name') + "</a>. Try again later.");
						loading_msg.replaceWith(button);
					}
				});
			}, this).show();
			return false;
		},

		doToggleSubscription: function(successMessage) {
			// Toggle the model's subscription status and provide visual feedback
			var loading_msg = window.loading_image.clone();
			var button = this.$("p.button-white");
			var t = setTimeout(function() { button.replaceWith(loading_msg); }, 500);
			this.model.toggleSubscription(function() {
				button.toggleClass("selected");
				showConfirmationMessage(successMessage);
			}, function() {
				showConfirmationMessage("Oops, unable to change subscription. Try again later.");
			}, function() {
				clearTimeout(t);
				loading_msg.replaceWith(button);
			});
		},

		toggleSubscription: function() {
			if (this.model.get("collaborator")) {
				// Collaborator
				var message = 'Stop collaborating on <a href="#">' + this.model.get('display_name') + "</a>?";
				new ConfirmationWindow(message, function() {
					message = 'You are no longer collaborating on <a href="#">' + this.model.get('display_name') + "</a>";
					this.doToggleSubscription(message);
				}, this).show();
			} else {
				var message = 'You are no longer following <a href="#">' + this.model.get('display_name') + "</a>";
				if (!this.model.get('subscribed')) {
					message = 'You are now following <a href="#">' + this.model.get('display_name') + "</a>";
				}
				this.doToggleSubscription(message);
			}
			return false;
		}
	});	

	// Common view for river and bucket lists
	var BaseAssetListView = Assets.BaseAssetListView = Backbone.View.extend({

		constructor: function(message, callback, context) {
			Backbone.View.prototype.constructor.apply(this, arguments);

			this.delegateEvents({
				"click .empty-message a": "showAddBucketsModal"
			});
		},

		initialize: function(options) {
			this.collection.on("reset", this.addAssets, this);
			this.collection.on("add", this.addAsset, this);
			this.collection.on("change:subscribed", this.subscriptionChanged, this);
			this.collection.on("destroy", this.assetDeleted, this);

			if (this.collection instanceof BucketList) {
				this.globalCollection = bucketList;
			} else 	if (this.collection instanceof RiverList) {
				this.globalCollection = riverList;
			}
		},

		addAssets: function() {
			this.collection.each(this.addAsset, this);

			if (!this.collection.length) {
				this.$(".empty-message").show();
			}
		},

		addAsset: function(asset) {
			if (!this.renderAsset(asset))
				return;

			this.$(this.listSelector).show();
			this.$(".empty-message").hide();
			var view = this.getView(asset);
			asset.setView(this, view);
			if (this.isCreator(asset)) {
				this.renderOwn(view);
			} else if (this.isCollaborator(asset)) {
				this.renderCollaborating(view);
			} else {
				this.renderFollowing(view);
			}
		},

		renderAsset: function(asset) {
			// Default render all assets
			return true;
		},

		isCreator: function(asset) {
			return asset.get("is_owner") && !asset.get("collaborator");
		},

		isCollaborator: function(asset) {
			return asset.get("collaborator");
		},

		subscriptionChanged: function(model, subscribed) {
			if (this.collection != this.globalCollection) {
				// Update the global bucket list when we are not
				// using the global list.
				var globalAsset = this.globalCollection.get(model.get("id"));
				if (globalAsset != undefined) {
					globalAsset.toggleSubscriptionNoSync();
				} else {
					modelCopy = model.clone();
					modelCopy.set("is_owner", false);
					this.globalCollection.add(modelCopy);
				}
			}
		},

		assetDeleted: function() {
			// Do nothing
		},

		showAddBucketsModal: function(e) {
			if (this.collection instanceof BucketList) {
				modalShow(new HeaderBucketsModal({collection: bucketList}).render().el);
				return false;
			}
		}
	});

	// Common view for river / bucket modal views
	var BaseModalAssetListView = Assets.BaseModalAssetListView = BaseAssetListView.extend({

		constructor: function(message, callback, context) {
			BaseAssetListView.prototype.constructor.apply(this, arguments);

			this.delegateEvents({
				"click .create-new a": "saveNewBucket",
				"submit": "saveNewBucket",
			});
		},

		isPageFetching: false,

		render: function() {
			this.addAssets(this);
			return this;
		},

		// Override default determination for assets to be rendered
		renderAsset: function(asset) {
			return asset.get("is_owner") || asset.get("subscribed");
		},

		renderOwn: function(view) {
			this.$(".own").append(view.render().el);
			this.$("p.own-title").show();
		},

		renderCollaborating: function(view) {
			this.$(".collaborating").append(view.render().el);
			this.$("p.collaborating-title").show();
		},

		renderFollowing: function(view) {
			this.$(".following").append(view.render().el);
			this.$("p.following-title").show();
		},

		saveNewBucket: function() {
			if (!(this.collection instanceof BucketList))
				return;

			var bucketName = $.trim(this.$(".create-new input[name=new_bucket]").val());

			if (!bucketName.length || this.isPageFetching)
				return false;

			this.isPageFetching = true;

			// First check if the bucket exists
			var bucket = this.collection.find(function(bucket) { 
				return bucket.get('name').toLowerCase() == bucketName.toLowerCase() 
			});
			if (bucket) {
				this.onSaveNewBucket(bucket);
				bucket.getView(this).setSelected();

				// Scroll to the bucket in the list
				var scrollOffset = bucket.getView(this).$el.offset().top - this.$(this.listSelector).offset().top;
				// Scroll only if the bucket is outside the view
				if (scrollOffset < 0 || scrollOffset > this.$(this.listSelector).height()) {
					this.$(this.listSelector).animate({
						scrollTop: this.$(this.listSelector).scrollTop() + scrollOffset
					}, 600);
				}

				this.$(".create-new input[name=new_bucket]").val("");
				this.isPageFetching = false;				

				return false;
			}

			var loading_msg = window.loading_message.clone();
			var create_el = this.$(".create-new .field").clone();
			this.$(".create-new .field").replaceWith(loading_msg);
			bucket = new Bucket({name: bucketName});
			var view = this;
			this.collection.create(bucket, {
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
						message = "An error occurred while saving the bucket. Try again later.";
					}
					flashMessage(view.$(".system_error"), message);
				},
				success: function() {
					view.onSaveNewBucket(bucket);
					bucket.getView(view).setSelected();

					// Scroll to the new bucket in the list
					view.$(view.listSelector).animate({
						scrollTop: view.$(view.listSelector).scrollTop() + (view.$(view.listItemSelector).last().offset().top - view.$(view.listSelector).offset().top)
					}, 600);
					create_el.find("input[name=new_bucket]").val("");
				}
			});

			return false;
		}

	});

	// Single river or bucket view in the header modal
	var HeaderAssetView = Assets.HeaderAssetView = BaseAssetView.extend({

		tagName: "li",

		initialize: function(options) {
			this.template = _.template($("#header-asset-template").html());
			BaseAssetView.prototype.initialize.call(this, options);
		},

		setSelected: function() {
			this.$el.addClass("selected");
		}
	});

	// Common view for river and bucket lists in the header menu
	var HeaderAssetsModal = Assets.HeaderAssetsModal = BaseModalAssetListView.extend({

		tagName: "article",

		className: "modal",

		listSelector: '.link-list',

		listItemSelector: '.link-list ul.own li',

		initialize: function(options) {
			this.$el.html(this.template());
			BaseModalAssetListView.prototype.initialize.call(this, options);
		},

		getView: function(asset) {
			return new HeaderAssetView({model: asset});
		},

		onSaveNewBucket: function(bucket) {
			// Do nothing
		}
	});

	var HeaderBucketsModal = Assets.HeaderBucketsModal = HeaderAssetsModal.extend({
		
		initialize: function(options) {
			this.template = _.template($("#header-buckets-modal-template").html());
			HeaderAssetsModal.prototype.initialize.call(this, options);
		}
	});

	var HeaderRiversModal  = Assets.HeaderRiversModal = HeaderAssetsModal.extend({
		
		initialize: function(options) {
			this.template = _.template($("#header-rivers-modal-template").html());
			HeaderAssetsModal.prototype.initialize.call(this, options);
		}
	});
	
	// View for the Follow Button
	var FollowButtonView = Assets.FollowButtonView = Backbone.View.extend({
		el: "#follow_button",

		events: {
			'click p.button-white > a': 'toggleSubscription'
		},
			
		initialize: function() {
			this.template = _.template($("#follow-button-template").html());
			this.model.on('change', this.render, this);
		},

		toggleSubscription: function(e) {
			var loading_msg = window.loading_message.clone();
			var button = this.$("p.button-white");
			var t = setTimeout(function() { button.replaceWith(loading_msg); }, 500);
				
			var view = this;
			var action = this.model.get("subscribed") ? "unfollow" : "follow";
			var name = this.model.get("name");
			this.model.toggleSubscription(function(model, response, options) {
				var message = "You are now following '" + name + "'";
				if (action == "unfollow") {
					message = "You are no longer following '" + name + "'";
				}
				showConfirmationMessage(message);
				
				// Update the global collection
				if (view.collection != null)
				{
					var globalAsset = view.collection.get(model.get("id"));

					if (globalAsset != undefined) {
						globalAsset.toggleSubscriptionNoSync();
					} else {
						modelCopy = model.clone();
						modelCopy.set("is_owner", false);
						view.collection.add(modelCopy);
					}
				}
			}, function() {
					
				showConfirmationMessage("Oops, unable to " + action + ". Try again later.");
			}, function() {
				clearTimeout(t);
				loading_msg.replaceWith(button);
			});
			return false;
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}
	});
	
}(this));