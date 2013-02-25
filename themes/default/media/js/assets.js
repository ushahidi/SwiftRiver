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
			if (this.get("account")) {
				// Namespace bucket name if the logged in user is not the owner
				this.set('name_namespaced', this.get("account").account_path + " / " + this.get("name"));
				if (parseInt(this.get("account").id) != logged_in_account) {
					this.set('display_name', this.get("name_namespaced"));
				} else  {
					this.set('display_name', this.get("name"));
				}
			}
		},

		toggleFollowing: function (success_callback, error_callback, complete_callback) {
			this.save({following: !this.get('following')}, {
				wait: true,
				success: success_callback,
				error: error_callback,
				complete: complete_callback
				});
		},

		toggleFollowingNoSync: function () {
			this.set('following', !this.get('following'));

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
			return this.filter(function(asset) { 
				return !asset.get('following') && asset.get('is_owner'); 
			});
		},
		
		following: function() {
			return this.filter(function(asset) {
				return !asset.get('collaborator') && asset.get('following');
			});
		},

		collaborating: function() {
			return this.filter(function(asset) { 
				return !asset.get('following') && asset.get('collaborator'); 
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

		url: site_url + logged_in_account_path + "/buckets"
	});
	// Global bucket list
	var bucketList = Assets.bucketList = new BucketList();

	// Common view for a single river / bucket
	var BaseAssetView = Assets.BaseAssetView = Backbone.View.extend({

		tagName: "div",

		className: "parameter",

		events: {
			"click div.actions .button-white a": "toggleFollowing",
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

		doToggleFollowing: function(successMessage) {
			// Toggle the model's subscription status and provide visual feedback
			var loading_msg = window.loading_image.clone();
			var button = this.$("p.button-white");
			var t = setTimeout(function() { button.replaceWith(loading_msg); }, 500);
			this.model.toggleFollowing(function() {
				button.toggleClass("selected");
				showConfirmationMessage(successMessage);
			}, function() {
				showConfirmationMessage("Oops, unable to change subscription. Try again later.");
			}, function() {
				clearTimeout(t);
				loading_msg.replaceWith(button);
			});
		},

		toggleFollowing: function() {
			if (this.model.get("collaborator")) {
				// Collaborator
				var message = 'Stop collaborating on <a href="#">' + this.model.get('display_name') + "</a>?";
				new ConfirmationWindow(message, function() {
					message = 'You are no longer collaborating on <a href="#">' + this.model.get('display_name') + "</a>";
					this.doToggleFollowing(message);
				}, this).show();
			} else {
				var message = 'You are no longer following <a href="#">' + this.model.get('display_name') + "</a>";
				if (!this.model.get('following')) {
					message = 'You are now following <a href="#">' + this.model.get('display_name') + "</a>";
				}
				this.doToggleFollowing(message);
			}
			return false;
		}
	});	

	// Common view for river and bucket lists
	var BaseAssetListView = Assets.BaseAssetListView = Backbone.View.extend({

		constructor: function(message, callback, context) {
			Backbone.View.prototype.constructor.apply(this, arguments);

			this.delegateEvents({
				"click .empty-message a": "showAddBucketsModal",
				"click li.add a": "showAddAsset"
			});
		},

		initialize: function(options) {
			this.collection.on("reset", this.addAssets, this);
			this.collection.on("add", this.addAsset, this);
			this.collection.on("change:following", this.followingChanged, this);
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

		followingChanged: function(model, following) {
			if (this.collection != this.globalCollection) {
				// Update the global bucket list when we are not
				// using the global list.
				var globalAsset = this.globalCollection.get(model.get("id"));
				if (globalAsset != undefined) {
					globalAsset.toggleFollowingNoSync();
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
		},
		
		showAddAsset: function() {
			var view = null;
			if (this.collection instanceof BucketList) {
				view = new CreateBucketModalView({listView: this});
			} else if (this.collection instanceof RiverList) {
				view = new CreateRiverModalView();
			}
			
			this.$("#modal-secondary").html(view.render().el);
		}
	});

	// Common view for river / bucket modal views
	var BaseModalAssetListView = Assets.BaseModalAssetListView = BaseAssetListView.extend({

		constructor: function(message, callback, context) {
			BaseAssetListView.prototype.constructor.apply(this, arguments);
		},

		isPageFetching: false,

		render: function() {
			this.addAssets(this);
			return this;
		},

		// Override default determination for assets to be rendered
		renderAsset: function(asset) {
			return asset.get("is_owner") || asset.get("following");
		},

		renderOwn: function(view) {
			this.$(".own").prepend(view.render().el);
			this.$(".own-title").show();
		},

		renderCollaborating: function(view) {
			this.$(".collaborating").append(view.render().el);
			this.$(".collaborating-title").show();
		},

		renderFollowing: function(view) {
			this.$(".following").append(view.render().el);
			this.$(".following-title").show();
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
		},
		
		// Assets in the header are listed before the create button
		renderOwn: function(view) {
			this.$(".own").prepend(view.render().el);
			this.$(".own-title").show();
		},
		
		// Collaborating assets are managed assets
		renderCollaborating: function(view) {
			this.renderOwn(view);
		},
	});

	// 
	// View for creating a new bucket via the modal dialog
	// 
	var CreateBucketModalView = window.CreateBucketModalView = Backbone.View.extend({
		
		tagName: "div",
		
		className: "modal-segment",
		
		events: {
			"click .modal-toolbar a.button-submit": "save",
			"submit": "save",
		},
		
		initialize: function(options) {
			this.template = _.template($("#create-bucket-modal-template").html());
			if (options && options.listView !== undefined) {
				this.listView = options.listView;
			}
		},
		
		render: function() {
			this.$el.html(this.template());
			return this;
		},
		
		save: function() {
			var bucketName = $.trim(this.$("#bucket_name").val());
			if (!bucketName) {
				return false;
			}

			var message = "";

			// Check if the bucket exists in the list of buckets owned by
			// the current user
			var bucket = _.find(Assets.bucketList.own(), function(bucket) { 
				return bucket.get('name').toLowerCase() == bucketName.toLowerCase();
			});

			if (bucket) {
				// message = "You already own a bucket named \"" + bucketName + "\"";
				// showSysMessage("failure", "Error", message, false);

				this.$("#bucket_name").val("");
				return false;
			}

			bucket = new Bucket({name: bucketName});

			var context = this;
			Assets.bucketList.create(bucket, {
				wait: true,
				error: function(model, response){
					// Show failure message
				},
				success: function() {
					// Show success message
					// message = "Bucket \"" + bucketName + "\" successfully created!"
					// showSysMessage("success", "Success",message, true);

					if (context.listView) {
						context.listView.onSaveNewBucket(bucket);
						bucket.getView(context.listView).setSelected();
					}

					context.$("a.modal-back").trigger("click");
					context.$("#bucket_name").val("");
				}
			});

			return false;
		}
	});

	// 
	// Buckets modal view in the header
	// 
	var HeaderBucketsModal = Assets.HeaderBucketsModal = HeaderAssetsModal.extend({

		initialize: function(options) {
			this.template = _.template($("#header-buckets-modal-template").html());
			HeaderAssetsModal.prototype.initialize.call(this, options);
		},
		
	});
	
	var CreateRiverModalView = window.CreateRiverModalView = Backbone.View.extend({
		
		tagName: "div",

		className: "modal-segment",
		
		events: {
			"click .modal-toolbar a.button-submit": "doCreateRiver",
		},
		
		isFetching: false,
		
		initialize: function(options) {
			this.template = _.template($("#create-river-modal-template").html());
			this.model = new River();
			this.model.urlRoot = site_url + logged_in_account_path + "/rivers";
		},
		
		render: function() {
			this.$el.html(this.template());
			return this;
		},
		
		doCreateRiver: function() {
			var riverName = this.$('input[name=river_name]').val();
			var description = this.$('input[name=river_description]').val();
			var isPublic = Boolean(this.$('select[name=public]').val());
			
			if (!riverName.length || this.isFetching)
				return false;
			
			this.isFetching = true;
			var view = this;
				
			// Disable form elements	
			this.$("input,select").attr("disabled", "disabled");
			
			this.model.save({
				name: riverName,
				description: description,
				public: isPublic,
			},{
				error: function() {
					view.$("input,select").removeAttr("disabled");
				},
				success: function(model) {
					window.location = model.get("url");
				},
				complete: function() {
					view.isFetching = false;
				}
			});
			return false;
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
		el: "#follow-button",

		events: {
			'click a.button-white': 'toggleFollowing',
			"click a.button-primary.selected": "toggleFollowing"
		},
			
		initialize: function() {
			this.template = _.template($("#follow-button-template").html());
			this.model.on('change', this.render, this);
		},

		toggleFollowing: function(e) {
			var loading_msg = window.loading_message.clone();
			var button = this.$("a.button-white");
			var t = setTimeout(function() { button.replaceWith(loading_msg); }, 500);
				
			var view = this;
			var action = this.model.get("following") ? "unfollow" : "follow";
			var name = this.model.get("name");
			this.model.toggleFollowing(function(model, response, options) {
				var message = "You are now following '" + name + "'";
				if (action == "unfollow") {
					message = "You are no longer following '" + name + "'";
				}
				showSysMessage("success", "Success", message, true);
				
				// Update the global collection
				if (view.collection != null)
				{
					var globalAsset = view.collection.get(model.get("id"));
		
					if (globalAsset != undefined) {
						globalAsset.toggleFollowingNoSync();
					} else {
						modelCopy = model.clone();
						modelCopy.set("is_owner", false);
						view.collection.add(modelCopy);
					}
				}
			}, function() {
					
				showSysMessage("failure", "Failure", "Oops, unable to " + action + ". Try again later.", false);
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