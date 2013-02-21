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
	
	// FILTERS TOGGLE DISPLAY
	$('.filters-type .toggle-filters-display').live('click touchstart', function() {
		$(this).parent().toggleClass('visible');
		$(this).siblings('.filters-type-details').slideToggle('fast');
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
		this.container.removeClass("visible");
		
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
		
		this.container.addClass("visible");
		this.container.fadeIn(350);
		$('body').addClass('noscroll');
		
		if (!this.modal) {
			$('body').addClass('zoomed');
			this._registerHide();
		} else {
			$('body').addClass('has_modal');
		}

		return this;
	};
	
	Dialog.prototype.transition = function() {
		var root = $(this.container);
		
		$('#modal-viewport', root).addClass('view-secondary');
		$('#modal-primary > div', root).fadeOut('fast');
		$('#modal-secondary .modal-segment', root).fadeIn('fast');
		root.scrollTop(0,0);		
		this._registerBackHandler(); 
		
		return this;
	};
	
	Dialog.prototype.back = function(hash) {
		var root = $(this.container);
		
		$('#modal-viewport', root).removeClass('view-secondary');
		$('#modal-secondary .modal-segment', root).fadeOut('fast');
		$('#modal-primary > div', root).fadeIn('fast');
		
		return this;
	};
	
	Dialog.prototype._registerBackHandler = function() {
		
		var root = $(this.container);
		$('a.modal-back', root).bind('click', function() {
			$('#modal-viewport', root).removeClass('view-secondary');
			$('#modal-primary > div', root).fadeIn('fast');
			$('#modal-secondary .modal-segment', root).fadeOut('fast');
			return false;
		});
		
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
	window.modalTransition = function (contents) {
		if(modalWindow) {
			modalWindow.transition(contents);
		}
	}
	$('a.modal-trigger').live('click', function() {
		loadUrl($(this).attr('href'), "modal", modalShow);
		return false;
	});
	$('article.modal a.modal-close').live('click', function(e) {
		modalWindow.hide();
		return false;
	});
	$('a.modal-transition').live('click', function(e) {
		modalWindow.transition();
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
	$('#zoom-container a.zoom-close').live('click', function() {
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
		// Header menu button
		$('header .user-menu .bucket a').live('click', function () {
			modalShow(new Assets.HeaderBucketsModal({collection: Assets.bucketList}).render().el);
			return false;
		});
		$('header .user-menu .rivers a').live('click', function () {
			modalShow(new Assets.HeaderRiversModal({collection: Assets.riverList}).render().el);
			return false;
		});
	}
	
	// Confirmation window
	if ($("#confirm-window-template").length) {
		window.ConfirmationWindow = Backbone.View.extend({
			tagName: "article",
			
			className: "modal",
			
			template: _.template($("#confirm-window-template").html()),
			
			events: {
				"click a.button-submit": "confirm"
			},
			
			constructor: function(message, callback, context) {
				Backbone.View.prototype.constructor.apply(this);
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
	}
	
	// // Display any queued system messages
	// if (window.system_messages) {
	// 	_.each(window.system_messages, function(m) {
	// 		showSysMessage(m['type'], m['title'], m['message'], m['flash']);
	// 	});
	// }

	// System message
	window.SystemMessage = Backbone.View.extend({
		tagName: "article",
		
		className: "system-message",
		
		messageTypes: ["failure", "success", "confirmation"],
		
		template: _.template($("#system-message-template").html()),
		
		constructor: function(message, messageType) {
			Backbone.View.prototype.constructor.apply(this);
			if (_.indexOf(this.messageTypes, messageType) == -1) {
				throw messageType + "is an invalid message type";
			}
			this.message = message;
			this.messageType = messageType;
		},
		
		render: function() {
			this.$el.attr("id", this.messageType);
			this.$el.addClass(this.messageType);
			
			this.$el.html(this.template({message: this.message}));
			this.$el.hide();
			return this;
		},
		
		show: function() {
			// Remove any existing messages
			var selector = "article#" +  this.messageType;
			if ($(selector, "#content")) {
				$(selector).remove();
			}
			$("body").append(this.render().el);
			this.$el.show();
		},
		
	});
	
	// TABS (Body content)
	$('.body-tabs-menu a').live('click', function(e) {
		var bodyTabHash = $(this).prop('hash');
		$('.body-tabs-window div.active').removeClass('active').fadeOut(100, function() {
			$('.body-tabs-window ' + bodyTabHash).fadeIn('fast').addClass('active');
		});
		$('.body-tabs-menu li').removeClass('active');
		$(this).parent().addClass('active');
		e.preventDefault();
	});	
	
	// SYSTEM Messages
	$("a.system-message-close").live('click', function(e) {
		$(this).closest('article.system-message').slideUp('fast');
		e.preventDefault();
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

function showSysMessage(type, title, message, flash) {
	var container = $("#system-message-template").clone().appendTo($("body"));
	container.addClass(type);
	$("p strong", container).prepend(title);
	$("p", container).append(message);
	
	if(flash) {
		container.slideDown('fast').delay(2000).slideUp('fast');
	} else {
		container.slideDown('fast', function(){
			$('a.system-message-close', container).on('click', function(e) {
				container.slideUp('fast');
				return false;
			});
		});
	}
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

// Returns true if the window scrolled to bottomEL
function nearBottom(bottomEl) {
	var bufferPixels = 40;
	return $(document).height() - $(window).scrollTop() - $(window).height() - bufferPixels < $(document).height() - bottomEl.offset().top;
}