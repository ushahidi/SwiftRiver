$(document).ready(function() {	
	// BUTTON CHECK FOR ICON
	$('.button-blue a, .button-white a').has('span.icon' && 'span.nodisplay').parents('p').addClass('only-icon');
	$('.button-blue a, .button-white a').has('span.icon').parents('p, li').addClass('has-icon');

	// POPOVER WINDOWS
	function popoverHide () {
		$(".popover-window").bind( "clickoutside", function(event){
			$(this).fadeOut('fast').unbind();
		});
	}
	$('a.popover-trigger').live('click', function(e) {
		$(this).closest('.popover').toggleClass('active');
		$(this).closest('.popover').find('.popover-window').fadeToggle('fast')
		popoverHide();
		return false;
	});
	
	// A common object for all window types
	function Dialog(contents, modal) {
		// Whether the dialog is modal or not
		this.modal = (modal == undefined ? false : modal);

		// Overlay  holding the dialog
		this.container = this.modal ? $("#modal-container") : $("#zoom-container");

		// Contents of the dialog
		this.contents = contents;

		// The dialog box
		this.dialogBox = $("div.modal-window", this.container);
	}
	
	// Hides a window
	Dialog.prototype.hide = function() {
		// Do nothing if an attempt is made to close a zoom window
		// when a modal window is open
		if (!this.modal && $('body').hasClass('has_modal')) {			
			return;
		} 
		
		this.container.fadeOut('slow');

		if (!this.modal) {			
			$('body').removeClass("zoomed");
		} else {			
			$('body').removeClass("has_modal");
		}
		
		if (!$('body').hasClass('zoomed') && !$('body').hasClass('has_modal')) {
			$('body').removeClass('noscroll');
		}
		
		this.dialogBox.unbind();
		return this;
	};
	
	// Bind window close event handlers
	Dialog.prototype._registerHide = function() {
		var context = this;
		
		// Close when clicked outside
		this.dialogBox.bind("clickoutside", function(event){
			context.hide();
			return false;
		});
		
		// Close the window when the escape key is pressed
		var keyHandler = function (e) {
			if(e.keyCode == 27){
				if (context.hide()) {
					$(window).unbind("keypress", keyHandler);
				}
				return false;
			}
		}
		$(window).bind("keypress", keyHandler);
		return this;
	};
	
	// Show the window
	Dialog.prototype.show = function() {
		this.dialogBox.html(this.contents);
		this.container.fadeIn(350);
		$('body').addClass('noscroll');
		this._registerHide(); 
		
		if (!this.modal) {
			$('body').addClass('zoomed');
		} else {
			$('body').addClass('has_modal');
		}

		return this;
	};
	
	function loadUrl(url, cssClass, callback, context) {
		if (!context) {
			context = this;
		}
		$.get(url, function(data) {
			// jQuery doesn't have a method like closest() that traverses
			// down the DOM so the below is required to check if the root
			// node of the data retrieved contains the class we want otherwise
			// find starting with the children.
			var content = $(data);
			if (! content.hasClass(cssClass)) {
				content = $(data).find("." + cssClass);
			}
			callback.call(context, content);
		})
	}
	
	// MODAL WINDOWS
	var modalWindow = null;
	window.modalHide = function () {
		if(modalWindow) {
			modalWindow.hide();
		}
	}
	window.modalShow = function (contents) {
		modalWindow = new Dialog(contents, true).show();
	}
	$('a.modal-trigger').live('click', function() {
		loadUrl($(this).attr('href'), "modal", modalShow);
		return false;
	});
	$('article.modal .close a').live('click', function(e) {
		modalWindow.hide();
		return false;
	});

	// ZOOM WINDOWS
	var zoomWindow = null;
	window.zoomHide = function() {
		if (zoomWindow) {
			zoomWindow.hide();
		}
	}
	window.zoomShow = function (contents) {
		zoomWindow = new Dialog(contents).show();
	}
	$('a.zoom-trigger').live('click', function() {
		loadUrl($(this).attr('href'), "modal", zoomShow);
		return false;
	});
	$('#zoom-container .close a').live('click', function() {
		zoomHide();
		return false;
	});
	

	// Click handler for the DOM elements that
	// generate confirmation messages when clicked
	var clickHandler = function(obj, evt) {
		var msg = $(obj).data('title');
		// Only proceed when there's content
		if (msg != undefined) {
			var context = $(obj).closest('div.parameter').find('h2').html();
			if (context == null) {
				context = '';
			}

			// Container for the confirmatino messages
			container = $('#confirmation-container');

			// Build out the HTML
			var replaceHTML = "<div class=\"modal-window\">" +
			    "<article class=\"modal base\">" + 
			    "<p>You are " + msg + " " + context + "</p>" +
			    "</article>" +
			    "</div>";
			
			$('div.modal-window', container).replaceWith(replaceHTML);

			container.fadeIn('fast').addClass('visible');
			container.delay(1000).fadeOut('fast').removeClass('visible');
			evt.stopPropagation();
		}
	};

	// CONFIRMATION MESSAGES
	$('.follow a').live('click', function(e) {
		clickHandler(this, e);
	});

	// HIDE OPTION MENU
	$('.remove a').live('click', function(e) {
		var optionToHide = $(this).attr("href");
		$(optionToHide).fadeOut('fast').remove();
		$(this).parent().fadeOut('fast').remove();
		e.preventDefault();
	});

	// DISPLAY SAVE TOOLBAR
	$('input, textarea').live('keypress', function () {
		$(this).closest("form").find('.save-toolbar').addClass('visible');
	});
	$('select').live('change', function () {
		$(this).closest("form").find('.save-toolbar').addClass('visible');
	});
	$(':radio, :checkbox').click(function(){
		$(this).closest("form").find(".save-toolbar").addClass("visible");
	});
	

	// ACCORDION MENU
	$('section.meta-data h3').live('click', function(e) {
		$(this).toggleClass('open').siblings('div.meta-data-content').slideToggle('fast');
	});

	// SCROLL TO BUOY
	if ($("#buoy").length > 0) {
		scrollToBuoy();
	}

	// Submit form when enter key hit in a password field
	$('input[type=password]').keypress(function(e){
		if(e.which == 13){
			$(this).parents('form:first').submit();
			e.preventDefault();
		}
	});
	
	
	// Global river and bucket lists
	if (typeof(logged_in_account) !== 'undefined')
	{
		// Base object for rivers and buckets
		var Asset = Backbone.Model.extend({
			
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
		window.Bucket = Asset.extend();
		window.River = Asset.extend();
		
		// Base collection for rivers and buckets
		window.AssetList = Backbone.Collection.extend({
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
		window.RiverList = AssetList.extend({
			model: River,

			url: site_url + logged_in_account_path + "/river/rivers/manage"
		});
		// Global river list
		window.riverList = new RiverList();
		

		// Collection for all the buckets accessible to the current user
		window.BucketList = AssetList.extend({
			model: Bucket,

			url: site_url + logged_in_account_path + "/bucket/buckets/manage"
		});
		// Global bucket list
		window.bucketList = new BucketList();
		
		// Common view for a single river / bucket
		window.BaseAssetView = Backbone.View.extend({

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
					message = '<a href="#">' + this.model.get('display_name') + "</a> has been deleted.";
					this.model.destroy();
					showConfirmationMessage(message);
					this.$el.fadeOut("slow", function () {
						$(this).remove();
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
		window.BaseAssetListView = Backbone.View.extend({
			
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
				
				if (this.collection instanceof window.BucketList) {
					this.globalCollection = window.bucketList;
				} else 	if (this.collection instanceof window.RiverList) {
					this.globalCollection = window.riverList;
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
				if (this.collection instanceof window.BucketList) {
					modalShow(new HeaderBucketsModal({collection: window.bucketList}).render().el);
					return false;
				}
			}
		});
		
		// Common view for river / bucket modal views
		window.BaseModalAssetListView = BaseAssetListView.extend({
			
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
				if (!(this.collection instanceof window.BucketList))
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
		var HeaderAssetView = BaseAssetView.extend({

			tagName: "li",

			template: _.template($("#header-asset-template").html()),

			setSelected: function() {
				this.$el.addClass("selected");
			}
		});
		
		// Common view for river and bucket lists in the header menu
		var HeaderAssetsModal = BaseModalAssetListView.extend({
			
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
		
		var HeaderBucketsModal = HeaderAssetsModal.extend({
			template: _.template($("#header-buckets-modal-template").html()),
		});
		
		var HeaderRiversModal = HeaderAssetsModal.extend({
			template: _.template($("#header-rivers-modal-template").html()),
		});
		
		// Header menu button
		$('header .user-menu .bucket a').live('click', function () {
			modalShow(new HeaderBucketsModal({collection: window.bucketList}).render().el);
			return false;
		});
		$('header .user-menu .rivers a').live('click', function () {
			modalShow(new HeaderRiversModal({collection: window.riverList}).render().el);
			return false;
		});
	}
	
	// Confirmation window
	window.ConfirmationWindow = Backbone.View.extend({
		tagName: "article",
		
		className: "modal",
		
		template: _.template($("#confirm-window-template").html()),
		
		events: {
			"click .button-blue a": "confirm"
		},
		
		constructor: function(message, callback, context) {
			Backbone.View.prototype.constructor.apply( this, arguments);
			this.message = message;
			this.callback = callback;
			this.context = context;
		},
		
		show: function() {
			modalShow(this.render().el);
		},
		
		render: function() {
			this.$el.html(this.template({message: this.message}));
			return this;	
		},
		
		confirm: function() {
			modalHide();
			this.callback.call(this.context);
			return false;
		}
	});

	// Feedback window
	window.FeedbackWindow = Backbone.View.extend({
		
		className: "modal",
		
		template: _.template($("#feedback-modal-template").html()),

		events: {
			"click .close a": "close"
		},
		
		show: function() {
			modalShow(this.render().el);
		},
		
		render: function() {
			this.$el.html(this.template({message: this.message}));
			return this;	
		},

		close: function() {
			modalWindow.hide();
		}
	});
	$('footer a.btn-feedback').live('click', function () {
		modalShow(new FeedbackWindow().render().el);
		return false;
	});
});

// Hide mobile address bar
window.addEventListener("load",function() {
	setTimeout(function(){
		window.scrollTo(0, 1);
	}, 0);
});


function submitForm(button){
	// Remove any onclick handler attached to the button
	$(button).removeAttr("onclick");

	// Get the form
	var form = $(button).parents('form:first');

	// Disable all input submit buttons
	$("input:submit", form).attr('disabled', 'disabled');

	// Delay form submission by 500ms
	setTimeout(function() { form.submit(); }, 500);
}

function submitAjax(button){
	var form = $(button).parents('form:first');
	form.submit();
}

function flashMessage(el, text) {
	var message = "<ul>";
	message += text;
	message += "</ul>";
	// Show message and fade it out slooooowwwwwwlllllyyyy
	el.html(message).fadeIn("fast").fadeOut(4000).html();
}

function showConfirmationMessage(message) {
	var container = $("#confirmation-container");

	// HTML with the message
	var replaceHTML = "<div class=\"modal-window\">" +
	    "<article class=\"modal base\">" + 
	    "<p>" + message + "</p>" +
	    "</article></div>";

	$('div.modal-window', container).replaceWith(replaceHTML);
	container.fadeIn('fast').addClass('visible');
	container.delay(1250).fadeOut('fast').removeClass('visible');
}

function showDefaultAvatar(source) {
	source.onerror = "";
	source.src = window.default_avatar_url;
	return true;
}

function scrollToBuoy() {
	$.getScript('/themes/default/media/js/jquery.scrollto.js', function() {
		$.scrollTo($('#buoy'), {
			duration: 300,
			axis: 'y',
			easing: 'linear',
			offset: -100
		});
		$('#buoy').prepend("<div class='buoy-message base'><p>Here's where you left off.</p></div>");
		$('#buoy .buoy-message').fadeIn('fast');
		$('#buoy .buoy-message').delay(2000).fadeOut('slow');
	});
}
