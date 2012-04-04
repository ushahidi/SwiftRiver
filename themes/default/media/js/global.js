$(document).ready(function() {	
	// BUTTON CHECK FOR ICON
	$('.button-blue a, .button-white a').has('span.icon' && 'span.nodisplay').parents('p').addClass('only-icon');
	$('.button-blue a, .button-white a').has('span.icon').parents('p').addClass('has-icon');

	// DETERMINE NEED FOR MASONRY SCRIPT
	if ($("#content.drops").length > 0) {
		$.getScript('jquery.masonry.js');
	}

	// DETERMINE NEED FOR SCROLLING VIEWS
	if ($("#page-views ul").children().length > 2) {
		$.getScript('jquery.touch.min.js');
	}

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
 
	// **************
	// MODAL WINDOWS
	// **************
	
	// Reference for all modal dialogs in the app
	window.swDialog = function() { return this; };
	swDialog.zoomShow = function(data) {
		swDialog.show({containerEl: "#zoom-container", data: data, zoom: true});
	}

	// Initiates dialog display and registers the necessary events
	/**
	 * Options parameter is a key-value object. Supported keys are:
	 *     containerEl - Element to overlay the current view with the dialog, 
	 *     data - Content to be rendered, 
	 *     zoom - true/false. When true, renders the dialog in zoom mode
	 *
	 */
	swDialog.show = function(options) {
		if (typeof options == 'undefined') {
			throw "ERROR: No parameters specified";
			return;
		}
		containerEl = (typeof options.containerEl == 'undefined') 
		    ? "#modal-container" : options.containerEl;
		zoom = (typeof options.zoom == 'undefined') ? false : options.zoom;

		swDialog.container  = $(containerEl);
		swDialog.modalWindow = $("div.modal-window", swDialog.container);
		bodyClass = (zoom == true) ? "noscroll zoomed" : "noscroll";

		// Hide the dialog
		swDialog.modalWindow.hide();

		// Display the dialog
		swDialog.container.fadeIn(200, function() {
			swDialog.modalWindow.show().html(options.data);
			$(this).addClass('visible');
			$('body').addClass(bodyClass);

			if ($('body').hasClass('zoomed')) {
				swDialog.modalWindow.unbind();
			}
		});

		// Create and register the hide function
		swDialog.hide = function() {
			swDialog.container.fadeOut(200, function() { 
				$('body').removeClass(bodyClass);
				$(this).removeClass("visible"); 
				swDialog.modalWindow.unbind();
			});
		}


		// ***************
		// Register events
		// ***************
		swDialog.modalWindow.bind("clickoutside", function(e){ swDialog.hide(); });
		$("h2.close a", swDialog.modalWindow).live("click", function(e) { swDialog.hide(); });

		// Keypress
		swDialog.keyHandler = function(e) {
			if (e.keyCode == 27) {
				$(window).unbind("keypress", this);
				swDialog.hide();
			}
		}
		$(window).bind("keypress", swDialog.keyHandler);

	}; // END swDialog

	$('a.modal-trigger').live('click', function(e) {
		var url = $(this).data('dialog-url');
		if (typeof url == 'undefined' || url == '') {
			url = $(this).attr("href");
		}
		$.get(url, function(data) {
			swDialog.show({
				containerEl: "#modal-container", 
				data: $(data).filter(".modal")
			});
		});
		e.preventDefault(); 
	});


	// ZOOM WINDOWS
	$('a.zoom-trigger').live('click', function(e) {
		var url = $(this).attr('href');
		$.get(url, function(data) {
			swDialog.show({
				containerEl: "#zoom-container", 
				data: $(data).filter(".modal"), 
				zoom: true
			});
		})
		e.preventDefault();
	});

	// CONFIRMATION MESSAGES
	$('.follow a').live('click', function(e) {
		var ConfirmationMessage = $(this).attr('title');
		var ConfirmationContext = $(this).closest('div.parameter').find('h2').html();
		$('#confirmation-container div.modal-window').replaceWith("<div class='modal-window'><article class='modal base'><p>You are "+ ConfirmationMessage + " " + ConfirmationContext +".</p></article></div>");
		$('#confirmation-container').fadeIn('fast').addClass('visible');
		$('#confirmation-container').delay(1000).fadeOut('fast').removeClass('visible');
		e.preventDefault();
	});

	// HIDE OPTION MENU
	$('.remove a').live('click', function(e) {
		var optionToHide = $(this).attr("href");
		$(optionToHide).fadeOut('fast').remove();
		$(this).parent().fadeOut('fast').remove();
		e.preventDefault();
	});

	// DISPLAY SAVE TOOLBAR
	$('.settings input').change(function () {
		$('.save-toolbar').addClass('visible');
	});

	// ACCORDION MENU
	$('section.meta-data h3').live('click', function(e) {
		$(this).toggleClass('open').siblings('div.meta-data-content').slideToggle('fast');
	});

	// Toggle channel selection
	//$('form input[type=checkbox]').live('click', function() {
	//	if ($(this).is(':checked')) {
	//		$(this).parents('label').addClass('selected');
	//	}
	//	else {
	//		$(this).parents('label').removeClass('selected');
	//	}
	//});

	// Submit form when enter key hit in a password field
	$('input[type=password]').keypress(function(e){
		if(e.which == 13){
			$(this).parents('form:first').submit();
			e.preventDefault();
		}
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