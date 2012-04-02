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
 
	// MODAL WINDOWS
	window.modalHide = function () {
		var el = $("#modal-container div.modal-window");
		el.parent().fadeOut('fast').removeClass('visible');
		$('body').removeClass('noscroll');
		el.unbind();
	}
	function registerModalHide() {
		$("#modal-container div.modal-window").bind( "clickoutside", function(event){
			modalHide();
		});
		function keyHandler(e) {
			if(e.keyCode == 27){
				// Escape key pressed
				$(window).unbind("keypress", keyHandler);
				modalHide();
			}
		}
		$(window).bind( "keypress", keyHandler);
	}
	window.modalShow = function (el) {
		$('#modal-container div.modal-window').html(el);
		$('#modal-container').fadeIn('fast').addClass('visible');
		$('body').addClass('noscroll');
		if ($('body').hasClass('zoomed')) {
			$('div.modal-window').unbind();
		} 
		else {
			registerModalHide(); 
		}
	}
	$('a.modal-trigger').live('click', function(e) {
		var url = $(this).attr('href');
		$.get(url, function(data) {
			modalShow($(data).filter(".modal"));
		})
		e.preventDefault();
	});
	$('article.modal h2.close a').live('click', function(e) {
		$('#modal-container').fadeOut('fast').removeClass('visible');
		if ($('body').hasClass('zoomed')) {
			registerZoomHide();
		} 
		else {
			$('body').removeClass('noscroll');
			$('div.modal-window').unbind();
		}
		e.preventDefault();
	});

	// ZOOM WINDOWS
	window.zoomHide = function() {
		var el = $("#zoom-container div.modal-window");
		el.parent().fadeOut('fast').removeClass('visible');
		$('body').removeClass('noscroll zoomed');
		el.unbind();
	}
	function registerZoomHide() {
		$("#zoom-container div.modal-window").bind( "clickoutside", function(event){
			zoomHide();
		});
		function keyHandler(e) {
			if(e.keyCode == 27){
				// Escape key pressed
				$(window).unbind("keypress", keyHandler);
				zoomHide();
			}
		}
		$(window).bind( "keypress", keyHandler);
	}
	window.zoomShow = function (el) {
		$('#zoom-container div.modal-window').html(el);
		$('#zoom-container').fadeIn('fast').addClass('visible');
		$('body').addClass('noscroll zoomed');
		registerZoomHide();
	}
	$('a.zoom-trigger').live('click', function(e) {
		var url = $(this).attr('href');
		$.get(url, function(data) {
			zoomShow($(data).filter(".modal"));
		})
		e.preventDefault();
	});
	$('#zoom-container .close a').live('click', function(e) {
		$('#zoom-container').fadeOut('fast').removeClass('visible');
		$('body').removeClass('noscroll zoomed');
		$('div.modal-window').unbind();
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