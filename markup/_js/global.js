$(document).ready(function() { 
	// DETERMINE NEED FOR MASONRY SCRIPT
	if ($("#content.drops").length > 0) {
		$.getScript('/markup/_js/jquery.masonry.js');
	}

	// DETERMINE NEED FOR SCROLLING VIEWS
	if ($("#page-views ul").children().length > 2) {
		$.getScript('/markup/_js/jquery.touch.min.js');
	}

	// DROP SHOW 'REMOVE' ON HOVER
	if (window.innerWidth > 800) {
		$('article.drop, .drop-full').hover(
			function() {
				$(this).find('.drop-status').fadeIn('fast');		
			},function() {
				$(this).find('.drop-status').fadeOut('fast');
		});
	}

	// DEMO: DROP SCORING
	$('article.drop .drop-score').toggle(function() {
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
 
	// FILTERS TOGGLE DISPLAY
	$('.filters-type .toggle-filters-display').live('click touchstart', function() {
		$(this).parent().toggleClass('visible');
		$(this).siblings('.filters-type-details').slideToggle('fast');
	});
	
	// MODAL WINDOWS
	function backModal() {		
		$('a.modal-back').live('click', function(e) {
			$('#modal-viewport').removeClass('view-secondary');
			$('#modal-primary > div').fadeIn('fast');
			$('#modal-secondary .modal-segment').fadeOut('fast');
			e.preventDefault();
		});
	}
	$('a.modal-trigger').live('click', function(e) {
		var urlModal = $(this).attr('href');
		$('#modal-container div.modal-window').load(urlModal + ' .modal', function(){
			saveToolbar();
		});
		$('#modal-container').fadeIn('fast').addClass('visible');
		$('body').addClass('noscroll');
		$('div.modal-window').unbind();
		e.preventDefault();
	});
	$('a.modal-transition').live('click', function(e) {
		var modalHash = $(this).prop('hash');
		$('#modal-viewport').addClass('view-secondary');
		$('#modal-secondary ' + modalHash).fadeIn('fast');
		$('#modal-primary > div').fadeOut('fast');
		$('#modal-container').scrollTop(0,0);		
		backModal();
		e.preventDefault();
	});	
	$('a.modal-close').live('click', function(e) {
		$('#modal-container, #filters.visible').fadeOut('fast').removeClass('visible');
		$('body').removeClass('noscroll');
		$('div.modal-window').unbind();
		e.preventDefault();
	});
	$('.modal-tabs-menu a').live('click', function(e) {
		var modalTabHash = $(this).prop('hash');
		$('.modal-tabs-window > div.active').removeClass('active').fadeOut(100, function(){
			$('.modal-tabs-window ' + modalTabHash).fadeIn('fast').addClass('active');		
		});
		$('.modal-tabs-menu li').removeClass('active');
		$(this).parent().addClass('active');
		e.preventDefault();
	});
	$('.modal-field-tabs-menu a').live('click', function(e) {
		var modalFieldTabHash = $(this).prop('hash');
		$('.modal-field-tabs-window > div.active').removeClass('active').fadeOut(100, function(){
			$('.modal-field-tabs-window ' + modalFieldTabHash).fadeIn('fast').addClass('active');		
		});
		$('.modal-field-tabs-menu li').removeClass('active');
		$(this).parent().addClass('active');
		e.preventDefault();
	});	

	// TABS (Body)	
	$('.body-tabs-menu a').live('click', function(e) {
		var bodyTabHash = $(this).prop('hash');
		$('.body-tabs-window div.active').removeClass('active').fadeOut(100, function(){
			$('.body-tabs-window ' + bodyTabHash).fadeIn('fast').addClass('active');		
		});
		$('.body-tabs-menu li').removeClass('active');
		$(this).parent().addClass('active');
		e.preventDefault();
	});
	
	// DROPDOWN
	$('.has-dropdown > a').live('click', function(e) {
		$(this).siblings('.dropdown-menu').fadeToggle('fast');
		e.preventDefault();
	});
	
	// TABLE TOOLBAR (Container) INTERACTION
	function toolbarStatus () {
		if ($('.container input:checkbox:checked').length > 0) {
			$('.container-toolbar').addClass('toolbar-active');
		}
		else {
			$('.container-toolbar').removeClass('toolbar-active');		
		}
	}
	$('.container tr input:checkbox').change(function() {
		if ($(this).is(':checked')) {
			$(this).closest('tr').addClass('row-selected');			
			toolbarStatus();
		}
		else {
			$(this).closest('tr').removeClass('row-selected');	
			toolbarStatus();
		}
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
		var urlZoom = $(this).attr('href');
		$('#zoom-container div.modal-window').load(urlZoom + ' .modal');
		$('#zoom-container').fadeIn('fast').addClass('visible');
		$('body').addClass('noscroll zoomed');
		zoomHide();
		e.preventDefault();
	});
	$('a.zoom-close').live('click', function(e) {
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
		$.getScript('/markup/_js/jquery.scrollto.js');
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
	
	// SMALL-SCREEN SCRIPTING
	if (window.innerWidth < 615) {
		$('a.filters-trigger').live('click', function(e) {
			$('#filters').fadeIn('fast').addClass('visible');
			$('body').addClass('noscroll');
			$('div.modal-window').unbind();
			e.preventDefault();
		});
	}

});

window.addEventListener("load",function() {
  setTimeout(function(){
	window.scrollTo(0, 1);
  }, 0);
});

