$(document).ready(function() { 
	// DETERMINE NEED FOR MASONRY SCRIPT
	if ($("#content.drops").length > 0) {
		$.getScript('/markup/js/jquery.masonry.js');
	}

	// DETERMINE NEED FOR SCROLLING VIEWS
	if ($("#page-views ul").children().length > 2) {
		$.getScript('/markup/js/jquery.touch.min.js');
	}
	
	// DISPLAY SAVE TOOLBAR
	function saveToolbar () {
		if ($(".save-toolbar").length > 0) {
			$('select').change(function() {
				$(this).closest('section.property-parameters, .modal-body form').find('.save-toolbar').fadeIn('fast');
			});
			$('input, textarea').keypress(function() {
				$(this).closest('section.property-parameters, .modal-body form').find('.save-toolbar').fadeIn('fast');
			});	
			$(':radio, :checkbox').click(function() {
				$(this).closest('section.property-parameters, .modal-body form').find('.save-toolbar').fadeIn('fast');
			});						
			$('.save-toolbar .cancel a').live('click', function(e) {
				$(this).closest('.save-toolbar').fadeOut('fast');
				e.preventDefault();
			});
		}
	}
	saveToolbar();	
	
	// DROP SHOW 'REMOVE' ON HOVER
	if (window.innerWidth > 800) {
		$('article.drop, .drop-full').hover(
			function() {
				$(this).find('.remove').fadeIn('fast');		
			},function() {
				$(this).find('.remove').fadeOut('fast');
		});
	}

	// DROP SCORING
	$('article.drop ul.score-drop li.star').toggle(function() {
		$(this).addClass('selected').children('a').append('<span class="star-total">23</span>');	
	}, function() {
		$(this).removeClass('selected');
		$(this).find('.star-total').remove();	
	});

	// POPOVER WINDOWS
	function popoverHide () {
		$(".popover-window").bind( "clickoutside", function(event){
			$(this).fadeOut('fast').unbind();
		});
	}
	$('a.popover-trigger').live('click', function(e) {
		$(this).closest('.popover').toggleClass('active');
		$(this).closest('.popover').find('.popover-window').fadeToggle('fast');
		popoverHide();
		return false;
	});	
 
	// MODAL WINDOWS
//	function modalHide () {
//		$("#modal-container div.modal-window").bind( "clickoutside", function(event){
//			$(this).parent().fadeOut('fast').removeClass('visible');
//			$('body').removeClass('noscroll');
//			$(this).unbind();
//		});
//	}
	$('a.modal-trigger').live('click', function(e) {
		var url = $(this).attr('href');
		$('#modal-container div.modal-window').load(url + ' .modal', function(){
			saveToolbar();
		});
		$('#modal-container').fadeIn('fast').addClass('visible');
		$('body').addClass('noscroll');
		if ($('body').hasClass('zoomed')) {
			$('div.modal-window').unbind();
		} 
		else {
//			modalHide(); 
		}
		e.preventDefault();
	});
	$('a.remove-large').live('click', function(e) {
		$('div.modal-window').unbind();
	});
	$('article.modal .close a').live('click', function(e) {
		$('#modal-container').fadeOut('fast').removeClass('visible');
		if ($('body').hasClass('zoomed')) {
//			zoomHide();
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

	// HIDE OPTION MENU
	$('.remove a').live('click', function(e) {
		var optionToHide = $(this).attr("href");
		$(optionToHide).fadeOut('fast').remove();
		$(this).parent().fadeOut('fast').remove();
		e.preventDefault();
	});
	
	 // PARAMETERS EDIT
	$('a.parameters-edit').live('click', function(e) {
		$(this).closest('article.container').toggleClass('active');
		$(this).closest('.settings').toggleClass('active');
		e.preventDefault();
	}); 	
	
	// ACCORDION MENU
	$('section.meta-data h3').live('click', function(e) {
		$(this).toggleClass('open').siblings('div.meta-data-content').slideToggle('fast');
	});

	// CHECKBOX TOGGLE
	function checkboxCheck () {
		$('a.checkbox input:checkbox:checked').addClass('checked');
	}
	$('a.checkbox').live('click', function(e) {
		checkboxCheck();
		$(this).toggleClass('checked');
		e.preventDefault();
	});
	checkboxCheck();
	
	// SCROLL TO BUOY
	if ($("#buoy").length > 0) {
		$.getScript('/markup/js/jquery.scrollto.js');
		$('#buoy').prepend("<div class='buoy-message base'><p>Here's where you left off.</p></div>");
		$('#buoy .buoy-message').delay(1000).fadeIn('fast');
		$('#buoy .buoy-message').delay(2000).fadeOut('slow');
	}

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

