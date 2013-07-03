/**
 * Assets module
 */
(function (root) {
	
	// Init the module
	Assets = root.Assets = {};
	
	// Base view for all modal views
	var BaseModalView = Backbone.View.extend({
		
	});
	
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
			this.set('is_collaborator', false);
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
				return asset.get('is_owner'); 
			});
		},
		
		following: function() {
			return this.filter(function(asset) {
				return asset.get('following');
			});
		},

		collaborating: function() {
			return this.filter(function(asset) { 
				return asset.get('is_collaborator');
			});
		}
	});

	// Collection for all the buckets accessible to the current user
	var RiverList = Assets.RiverList = AssetList.extend({
		model: River,

		url: site_url + logged_in_account_path + "/rivers"
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
			if (this.model.get("is_collaborator")) {
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
			return true;
		},

		isCreator: function(asset) {
			return asset.get("is_owner");
		},

		isCollaborator: function(asset) {
			return asset.get("is_collaborator");
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
			
			modalShow(view.render().el);
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
			return asset.get("is_owner") || asset.get("is_collaborator");
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

		className: "modal modal-view",

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
	});

	// 
	// View for creating a new bucket via the modal dialog
	// 
	var CreateBucketModalView = Assets.CreateBucketModalView = Backbone.View.extend({
		
		tagName: "article",
		
		className: "modal modal-view",
		
		events: {
			"click .modal-toolbar a.button-submit": "save",
			"submit": "save",
		},

		initialize: function(options) {
			this.template = _.template($("#create-bucket-modal-template").html());
			if (options && options.listView !== undefined) {
				this.listView = options.listView;
			}

			this.viewData = {closable: false};

			if (options && options.closable) {
				this.viewData.closable = true;
			} else {
				this.className += " modal-segment";
			}
		},
		
		render: function() {
			this.$el.html(this.template(this.viewData));
			return this;
		},
		
		save: function() {
			var bucketName = $.trim(this.$("#bucket_name").val());
			if (!bucketName) {
				return false;
			}

			// Check if the bucket exists in the list of buckets owned by
			// the current user
			var bucket = _.find(Assets.bucketList.own(), function(bucket) { 
				return bucket.get('name').toLowerCase() == bucketName.toLowerCase();
			});

			if (bucket) {
				var message = "You already own a bucket named \"" + bucketName + "\"";
				showFailureMessage(message);

				this.$("#bucket_name").val("");
				return false;
			}

			bucket = new Bucket({name: bucketName, urlRoot: site_url + logged_in_account_path + "/buckets"});

			var context = this;
			Assets.bucketList.create(bucket, {
				wait: true,
				error: function(model, response){
					// Show failure message
				},
				success: function() {
					// Show success message
					var message = "Bucket \"" + bucketName + "\" successfully created!"
					showSuccessMessage(message, {flash: true});

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
	
	var CreateRiverModalView = Assets.CreateRiverModalView = Backbone.View.extend({
		
		tagName: "article",

		className: "modal modal-view",
		
		events: {
			"click .modal-toolbar a.button-submit": "doCreateRiver",
		},
		
		initialize: function(options) {
			this.template = _.template($("#create-river-modal-template").html());
			this.model = new River();
			this.model.urlRoot = site_url + logged_in_account_path + "/rivers";

			this.viewData = {closable: false};
			
			if (options && options.closable) {
				this.viewData.closable = true;
			} else {
				this.className += " modal-segment";
			}
		},
		
		render: function() {
			this.$el.html(this.template(this.viewData));
			return this;
		},
		
		doCreateRiver: function() {
			var riverName = this.$('input[name=river_name]').val();
			var description = this.$('input[name=river_description]').val();
			var isPublic = this.$('select[name=public]').val();

 			var view = this;
			if (!riverName.length || view.isFetching)
				return false;
			
			view.isFetching = true;
			var button = this.$(".button-submit");
			var originalButton = button.clone();
			var t = setTimeout(function() {
				button.children("span").html("<em>Creating river</em>").append(loading_image_squares);
			}, 500);
				
			// Disable form elements	
			this.$("input,select").attr("disabled", "disabled");
			
			this.model.save({
				name: riverName,
				description: description,
				public: isPublic,
			},{
				error: function(model, response) {
					if (response.status == 400) {
						showFailureMessage("A river with the name '" + riverName + "' already exists.");
					} else {
						showFailureMessage("There was a problem creating the river. Try again later.");
					}
					
					view.$("input,select").removeAttr("disabled");
				},
				success: function(model) {
					window.location = model.get("url");
				},
				complete: function() {
					view.isFetching = false;
					button.replaceWith(originalButton);
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

				showSuccessMessage(message, {flash:true});
				
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
				showFailureMessage("Oops, unable to " + action + ". Try again later.");
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
	
	// Form model
	var Form = Assets.Form = Asset.extend({
		defaults: {
		    "is_owner":  true,
			"is_collaborator":  false,
			"follower":  false,
			"url": null,
		},
		
		validate: function(attrs, options) {
			
			if (! _.has(attrs, "name"))
				return "Form name not provided.";
				
			if (attrs.name.length == 0)
				return "Form name cannot be empty.";
				
			if (! _.has(attrs, "fields") || attrs.fields.length == 0)
				return "A form must have at least one field.";
		},
		
		// Helper function for obtaining a field using its id
		getField: function(id) {
			return _.filter(this.get("fields"), function(field) {
				return field.id == id; 
			}, this).pop();
		},
	});
	
	var FormList = Assets.FormList = AssetList.extend({
		model: Form,

		url: site_url + logged_in_account_path + "/forms"
	});
	// Global form list
	var formList = Assets.formList = new FormList();
	
	// Form field model
	var FormField = Backbone.Model.extend({
		"defaults": {
			"title": null,
			"description": null,
			"options": []
		}
	});
	
	// List of fields in a form
	var Fields = Assets.Fields = Backbone.Collection.extend({
		model: FormField
	})	
	
	// Single Field in a list view
	var FieldView = Assets.FieldView = Backbone.View.extend({

		tagName: "li",

		events: function() {
			// When options.isLive is true, allow user input for values otherwise
			// disable the inputs and allow editing of the field.
			if (!this.options.isLive) {
				return {
					"click span.icon-cancel": "removeField",
					"click span.icon-pencil": "editField"
				};
			}
		},
		
		initialize: function() {
			this.model.on("destroy", this.onRemove, this);
		},

		render: function() {
			switch (this.model.get("type")) {
				case "text":
					this.template = _.template($("#text-field-template").html());
					break;
					
				case "multiple":
					this.template = _.template($("#checkbox-field-template").html());
					this.optionTemplate = _.template($("#checkbox-field-option-template").html());
					break;
				
				case "select":
					this.template = _.template($("#list-field-template").html());
					this.optionTemplate = _.template($("#list-field-option-template").html());
					break;
			}
			
			var data = this.model.toJSON();
			data.isLive = this.options.isLive;
			this.$el.html(this.template(data));
			
			if (this.model.get("type") == "multiple") {
				_.each(this.model.get("options"), function(option) {
					var view = this.optionTemplate({
						option: option,
						isLive:this.options.isLive
					})
					this.$(".modal-field").append(view);
				}, this);
			}
			
			if (this.model.get("type") == "select") {
				_.each(this.model.get("options"), function(option) {
					var view = this.optionTemplate({
						option: option,
						isLive:this.options.isLive
					})
					this.$("select").append(view);
				}, this);
			}
			
			return this;	
		},
		
		onRemove: function() {
			this.$el.fadeOut("slow");
		},
		

		removeField: function() {
			var view = this;
			
			if (view.isFetching)
				return;		
				
			if (this.model.isNew()) {
				this.onRemove();
				return;
			}    
				
			view.isFetching = true;
			
			var button = this.$("span.remove");
			
			// Show loading icon if there is a delay
			var t = setTimeout(function() { button.removeClass("icon-cancel").html(loading_image); }, 500);
			
			this.model.destroy({
				wait: true,
				complete: function() {
					clearTimeout(t);
					view.isFetching = false;
				},
				error: function() {
					showFailureMessage("Unable to remove field. Try again later.");
					button.html("");
					button.addClass("icon-cancel");
				}
			});
			return false;
		},
		
		editField: function() {
			var view = new EditFieldModalView({model: this.model});
			view.on("change", function() { 
				this.trigger("change", this.model);
			}, this);
			modalShow(view.render().el);
			
			return false;
		},
		
		getValue: function() {
			var val = null;
			
			switch (this.model.get("type")) {
				case "text":
					val = this.$("input[type=text]").val();
					break;
					
				case "multiple":
					val = [];
					_.each(this.$("input[type=checkbox]:checked"), function(e) {
						val.push($(e).val());
					}, this);
					break;
				
				case "select":
					val = $("select").val();
					break;
			}
			
			return val;
		},
		
		setValue: function(value) {
			switch (this.model.get("type")) {
				case "text":
					this.$("input[type=text]").val(value);
					break;
					
				case "multiple":
					val = [];
					_.each(this.$("input[type=checkbox]"), function(e) {
						var el = $(e);
						if (_.contains(value, el.val())) {
							el.attr("checked", "checked")
						}
					}, this);
					break;
				
				case "select":
					_.each(this.$("select option"), function(e) {
						var el = $(e);
						if (el.val() == value) {
							el.attr("selected", "selected")
						}
					}, this);
					break;
			}
		}
	});
	
	
	// Modal for creating/editing Forms
	var FormModalView = Assets.FormModalView = Backbone.View.extend({
		
		tagName: "article",

		className: "modal modal-view",
		
		events: {
			"click .modal-toolbar a.button-submit": "doCreateForm",
			"click li.add a": "showAddField",
		},
		
		initialize: function(options) {
			this.template = _.template($("#create-form-modal-template").html());
			this.model.urlRoot = site_url + logged_in_account_path + "/forms";
			this.model.on("invalid", this.onFormInvalid, this);

			this.fields = new Fields();
			if (!this.model.isNew()) {
				var baseUrl = site_url + logged_in_account_path + "/form"
				this.fields.url = baseUrl + "/" + this.model.get("id") + "/fields";	
			}
			this.fields.on('add',	 this.addField, this);
			this.fields.on('reset', this.addFields, this);
		},
		
		addField: function(field) {
			var view = field.view = new FieldView({model: field});
			this.$("ul.view-table .add").before(view.render().el);
			view.on("change", this.onFieldChanged, this);
		},
		
		addFields: function() {
			this.fields.each(this.addField, this);
		},
		
		render: function() {
			var data = this.model.toJSON();
			data.isNew = this.model.isNew();
			this.$el.html(this.template(data));
			
			// Render existing fields if any
			this.fields.reset(this.model.get("fields"));
			
			return this;
		},
		
		onFieldAdded: function(field) {
			this.fields.add(field);
			modalBack();
		},
		
		onFieldChanged: function(field) {
			field.view.render();
			modalBack();
		},
		
		showAddField: function() {
			var view = new AddFieldModalView({urlRoot: this.fields.url});
			view.on("change", this.onFieldAdded, this);
			modalShow(view.render().$el);
			return false;
		},
		
		onFormInvalid: function(model, error) {
			showFailureMessage(error);
		},
		
		doCreateForm: function() {
			var view = this;
			
			if (view.isFetching)
				return false;
			
			view.isFetching = true;
			var button = this.$(".button-submit");
			var originalButton = button.clone();
			var t = setTimeout(function() {
				button.children("span").html("<em>Creating form</em>").append(loading_image_squares);
			}, 500);
				
			// Disable form elements	
			this.$("input[name=form_name]").attr("disabled", "disabled");
			
			var formName = $.trim(this.$("input[name=form_name]").val());
			this.model.set("name", formName);
			this.model.set("fields", this.fields.toJSON());
			
			var saveStatus = this.model.save({
				name: formName,
				fields: this.fields.toJSON()
			},{
				wait: true,
				success: function() {
					modalHide();
					view.model.set("display_name", formName);
					view.collection.add(view.model);
					showSuccessMessage("Form updated successfully", {flash: true});
				},
				error: function(model, response) {
					if (response.status == 400) {
						showFailureMessage("A form with the name '" + formName + "' already exists.");
					} else {
						showFailureMessage("There was a problem creating the form. Try again later.");
					}
					view.$("input[name=form_name]").removeAttr("disabled");
				},
				complete: function() {
					view.isFetching = false;
					button.replaceWith(originalButton);
					clearTimeout(t);
				},
			});
			
			if (!saveStatus) {
				this.isFetching = false;
				this.$("input[name=form_name]").removeAttr("disabled");
				button.replaceWith(originalButton);
				clearTimeout(t);
			}
			
			return false;
		}
	});
	
	// Add field modal
	var AddFieldModalView = Backbone.View.extend({
		
		tagName: "article",

		className: "modal modal-view modal-segment",
		
		events: {
			"click .modal-tabs-menu a": "showEditField",
			"click .button-submit": "saveField",
		},
		
		initialize: function() {
			this.template = _.template($("#add-field-modal-template").html());
		},
		
		render: function(eventName) {
			this.$el.html(this.template());
			return this;
		},
		
		showEditField: function(e) {
			// Hide the currently visible field editor
			this.$(".modal-tabs-window div.active").removeClass("active");
			
			var hash = $(e.currentTarget).prop('hash');
			
			switch(hash) {
				case "#add-custom-text":
					this.fieldView = this.textFieldView;
					if (this.fieldView == undefined) {
						var field = new FormField({type:"text"});
						field.urlRoot = this.options.urlRoot;
						this.fieldView = this.textFieldView = new EditFieldView({model: field});
						this.renderField(this.fieldView);
					}
				break;
				
				case "#add-custom-list":
					this.fieldView = this.listFieldView;
					
					if (this.fieldView == undefined) {
						var field = new FormField({type:"select"});
						field.urlRoot = this.options.urlRoot;
						this.fieldView = this.listFieldView = new EditFieldView({model: field});
						this.renderField(this.fieldView);
					}
				break;
			}
			
			// Show the selected view
			this.fieldView.$el.addClass("active");
			$(e.currentTarget).parents("li").addClass("active").siblings().removeClass("active");
			return false;
		},
		
		renderField: function(view) {
			// Show the new field
			this.$(".modal-tabs-window").append(view.render().$el);
		},
		
		saveField: function() {
			view = this;
			
			if (this.fieldView == undefined || view.isFetching)
				return false;
				
			view.isFetching = true;
			
			var button = this.$(".button-submit");
			var originalButton = button.clone();
			var t = setTimeout(function() {
				button.children("span").html("<em>Saving field</em>").append(loading_image_squares);
			}, 500);
			
			this.fieldView.save( function() {
					view.isFetching = false;
					button.replaceWith(originalButton);
				},
				function() {
					view.trigger("change", view.fieldView.model);
				},
				function() {
					showFailureMessage("There was a problem adding the field. Try again later.");
				}
			);
			return false;
		}
	});
	
	// Single field edit view
	var EditFieldView = Backbone.View.extend({
		
		tagName: "div",
		
		// True whether this is a list/checkbox field
		multi: false,
		
		events: {
			"click .icon-plus ": "addOption",
			"click .icon-cancel": "removeOption",
			"change input[name=multi]": "toggleMulti"
		},
		
		initialize: function() {
			
			switch (this.model.get("type")) {
				case "text":
					this.template = _.template($("#edit-text-field-modal-template").html());
				break;

				case "multiple":
				case "select":
					this.template = _.template($("#edit-list-field-modal-template").html());
					this.multi = true;
				break;
			}
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			
			// Render options if a multi field
			if (this.multi) {
				// Use the current option field as a template
				var optionField = this.$(".option-field").last();
				
				var options = this.model.get("options");
				_.each(options, function(option) {
					var el = optionField.clone();
					el.children("input").val(option);
					optionField.before(el);
				}, this);
				
				if (options.length) {
					optionField.remove();
				}
			}
			
			return this;
		},
		
		save: function(complete, success, error) {
			var title = this.$("input[name=title]").val()
			var description = this.$("input[name=description]").val()
			
			
			this.model.set("title", title);
			this.model.set("description", description);
			
			var fieldType = this.model.get("type");
			if (fieldType == "multiple" || fieldType == "select") {
				var options = [];
				_.each($(".modal-field .option-field input"), function(input) {
					var val = $(input).val();
					if (val.length > 0) {
						options.push(val);
					}
				})
				this.model.set("options", options);
			}
			
			if (this.model.urlRoot != undefined || !this.model.isNew()) {
				this.model.save(this.model.toJSON(), {
					wait: true,
					complete: complete,
					success: success,
					error: error
				});
			} else {
				success();
				complete();
			}
		},
		
		addOption: function(e) {
			// Copy an existing option field and insert it into the DOM
			var button = $(e.currentTarget);
			
			var optionField = button.closest(".option-field");
			
			// Add new option to the dom
			var el = optionField.clone()
			el.children("input").val("");
			optionField.after(el);

			return false;
		},
		
		removeOption: function(e) {
			$(e.currentTarget).closest(".option-field").fadeOut("slow", function() { $(this).remove(); });
			
			return false;
		},
		
		toggleMulti: function() {
			var newType = this.model.get("type") == "select" ? "multiple" : "select";
			this.model.set("type", newType);
		}
	});
	
	// Modal view for editing a field
	var EditFieldModalView = Backbone.View.extend({
		
		tagName: "article",

		className: "modal modal-view modal-segment",
		
		events: {
			"click .button-submit": "saveField",
		},
				
		initialize: function() {
			this.template = _.template($("#edit-field-modal-template").html());
		},
		
		render: function(eventName) {
			this.$el.html(this.template());
			
			this.fieldView = new EditFieldView({model: this.model});					
			this.$(".modal-field-tabs-window").replaceWith(this.fieldView.render().el);
			
			return this;
		},
		
		saveField: function() {
			view = this;
			
			if (this.fieldView == undefined || view.isFetching)
				return false;
				
			view.isFetching = true;
			
			var button = this.$(".button-submit");
			var originalButton = button.clone();
			var t = setTimeout(function() {
				button.children("span").html("<em>Saving field</em>").append(loading_image_squares);
			}, 500);
			
			this.fieldView.save(
				function() {
					view.isFetching = false;
					button.replaceWith(originalButton);
				},
				function() {
					view.trigger("change", view.fieldView.model);
				},
				function() {
					showFailureMessage("There was a problem saving the field. Try again later.");
				}
			);
			return false;
		}
	});
	
}(this));