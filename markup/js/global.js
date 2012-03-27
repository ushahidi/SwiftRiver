$(document).ready(function() { 

	// DROP SCORING
	$('article.drop ul.score-drop > li.like').live('click', function(e) {
		$(this).addClass('scored').siblings().remove();
		$(this).children().append('<span class="user-score">Useful</span>');
	});
	$('article.drop ul.score-drop > li.dislike').live('click', function(e) {
		$(this).addClass('scored').siblings().remove();
		$(this).children().append('<span class="user-score">Not useful</span>');
	});

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
	function modalHide () {
		$("#modal-container div.modal-window").bind( "clickoutside", function(event){
			$(this).parent().fadeOut('fast').removeClass('visible');
			$('body').removeClass('noscroll');
			$(this).unbind();
		});
	}
	$('a.modal-trigger').live('click', function(e) {
		var url = $(this).attr('href');
		$('#modal-container div.modal-window').load(url + ' .modal');
		$('#modal-container').fadeIn('fast').addClass('visible');
		$('body').addClass('noscroll');
		if ($('body').hasClass('zoomed')) {
			$('div.modal-window').unbind();
		} 
		else {
			modalHide(); 
		}
		e.preventDefault();
	});
	$('article.modal h2.close a').live('click', function(e) {
		$('#modal-container').fadeOut('fast').removeClass('visible');
		if ($('body').hasClass('zoomed')) {
			zoomHide();
		} 
		else {
			$('body').removeClass('noscroll');
			$('div.modal-window').unbind();
		}
		e.preventDefault();
	});

	// ZOOM WINDOWS
	function zoomHide () {
		$("#zoom-container div.modal-window").bind( "clickoutside", function(event){
			$(this).parent().fadeOut('fast').removeClass('visible');
			$('body').removeClass('noscroll zoomed');
			$(this).unbind();
		});
	}
	$('a.zoom-trigger').live('click', function(e) {
		var url = $(this).attr('href');
		$('#zoom-container div.modal-window').load(url + ' .modal');
		$('#zoom-container').fadeIn('fast').addClass('visible');
		$('body').addClass('noscroll zoomed');
		zoomHide();
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

	// ACCORDION MENU
	$('section.meta-data h3').live('click', function(e) {
		$(this).toggleClass('open').siblings('div.meta-data-content').slideToggle('fast');
	});

	// Toggle channel selection
	$('form input[type=checkbox]').live('click', function() {
		if ($(this).is(':checked')) {
			$(this).parents('label').addClass('selected');
		}
		else {
			$(this).parents('label').removeClass('selected');
		}
	});

});

