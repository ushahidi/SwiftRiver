$(document).ready(function() { 
	// DETERMINE NEED FOR MASONRY SCRIPT
	if ($("#content.drops").length > 0) {
		$.getScript('/markup/_js/jquery.masonry.js');
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

	// (DEMO) DROP: Create new drop from selection	
	function getSelected() {
		if(window.getSelection) {
			return window.getSelection(); 
		} else if(document.getSelection) {
			return document.getSelection(); 
		} else {
			var selection = document.selection && document.selection.createRange();
			if(selection.text) { 
				return selection.text; 
			}
			return false;
		}
		return false;
	}
			
	$('article#content').mouseup(function(e) {
		var selection = getSelected();
		if (selection && (selection = new String(selection).replace(/^\s+|\s+$/g,''))) {
			$('#create-new').fadeIn('fast');
//			alert(selection);
		}
	});

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
	
	// MODAL WINDOWS: Tabs 
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

	// TABS (Body content)	
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

