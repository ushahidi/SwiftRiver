$(document).ready(function() {	
	// View or hide a dropdown menu
	$('.has_dropdown > a').live('click', function(e) {
		// $('ul.dropdown').fadeOut('fast');
		$(this).parent().toggleClass('active');
	    $(this).siblings('ul.dropdown').fadeToggle('fast');
		e.stopPropagation();
	});
	$('.dropdown .cancel').live('click', function(e) {
		$(this).closest('.dropdown').fadeOut('fast');
		$(this).closest('.dropdown').siblings('p').removeClass('active');
	});
	$('.actions .button_delete, .actions .button_change').live('click', function(e) {
		$(this).toggleClass('active');
		$(this).siblings('.dropdown').fadeToggle('fast');
		e.stopPropagation();
	});
	$('.actions .dropdown').live('click', function(e) {
		e.stopPropagation();
	});
	
	// TOGGLE PANEL, DRAWER
	if (screen.width <= 600) {
		$('ul.views li').not('ul.views li.active, ul.views li.more').remove();
	}
	else {
		$('ul.views li.more').remove();
	}
	$('section.panel nav ul li.view_panel a, section.actions p.button_view a, div.edit_advanced p.button_view a').toggle(function(e) {
			var url = $(this).attr('href');
			if ( url == '/map/reports/filters.php' ) {
				$.getScript('/js/filters.js');
			}
			$(this).addClass('detail_hide').closest('section.panel, article.item, div.edit_advanced').append('<div class="drawer"></div>');
			if ( $(this).is('section.panel nav ul li.view_panel a') ) {
				$('ul.views li, ul.actions li').fadeTo('fast', 0); // for panel
				$(this).parent('li').addClass('open').fadeTo('fast', 1); // for panel
				$(this).closest('section.panel').children('div.drawer').slideDown('slow').load(url + ' section.panel .panel_body');
			}
			else {
				$(this).closest('article.item, div.edit_advanced').children('div.drawer').slideDown('slow').load(url + ' div.detail', function() {
					convertForm();
					$('section.discussion').remove();
				});
				$(this).closest('.row').find('p a').not('p a.detail_hide').fadeTo('fast', 0); // for drawer
			}		
			e.preventDefault();				
		}, function(e) {
			$('ul.views li, ul.actions li').fadeTo('fast', 1);
			$(this).parent('li').removeClass('open');
			$(this).removeClass('detail_hide').closest('section.panel, article.item, div.edit_advanced').children('div.drawer').slideUp('fast').remove();				
			$(this).closest('.row').find('p a').not('p a.detail_hide').fadeTo('fast', 1); // for drawer
			e.preventDefault();
	});
	$('section.panel nav ul.actions li.view_panel a').one('click', function() {
		$.getScript('/themes/default/media/js/settings.js');
	});
	$('section.panel a.close').live('click', function() {
		$('section.panel nav, .canvas > .container').fadeIn('fast');
		$('ul.views li, ul.actions li').fadeTo('fast', 1);
		$('section.panel nav ul.actions li.view_panel').removeClass('active');
		$('section.panel div.drawer').slideUp('fast').remove();
		return false;
	});
	$('div.detail a.close').live('click', function() {
		$(this).closest('article.item, div.edit_advanced').find('section.actions p.button_view a, div.edit_advanced p.button_view a').removeClass('detail_hide');
		$(this).closest('div.edit_advanced').find('div.row > p a').fadeTo('fast', 1);
		$(this).closest('article.item, div.edit_advanced').children('div.drawer').slideUp('fast').remove();
	});
	
	//Show a trend
	$('section.panel ul.views li.view_trend a').live('click', function(e) {
	    //Make the clicked tab the only one active
	    $('section.panel ul.views li').each(function(){
	        $(this).removeClass('active');
	    });
	    $(this).parent('li').addClass('active');
	    
	    //Load the referenced URL into the viewport
	    var url = $(this).attr('href');	    
	    $('article .trend_container').load(url);
		e.preventDefault();
	});	
	
	
	// Toggle following or subscribing
	$('.button_change a.subscribe').click(function() {
		$(this).parent().toggleClass('active');
		$(this).toggleClass('subscribed');
	});
	
	//Delete a droplet
	$('section.detail .actions #delete_droplet .confirm a').live('click', function(e) {
	   var article = $(this).closest('article.droplet');
	   var url = $(this).attr('href');
	   $.get(url, function() {
           article.hide('slow');
         })
         .success(function() {  })
         .error(function() { 
             //TODO: flash message that something went wrong?
              })
         .complete(function() { });
	   e.preventDefault();
	});
	
	// Item checkboxes
	$('.actions .dropdown li.checkbox a.selected').closest('.button').find('p.button_change a').addClass('selected');
	jQuery.fn.checkBuckets = function() {
		if ($('.actions .dropdown li.checkbox a').is('.selected')) {
			$(this).closest('.button').find('p.checkbox_options a').addClass('selected');
		}
		else {
			$(this).closest('.button').find('p.checkbox_options a').removeClass('selected');
		}
	};
	$('.actions .dropdown li.checkbox a').live('click', function(e) {
		$(this).toggleClass('selected').checkBuckets();
		e.stopPropagation();
	});
	
	// Item radio buttons
	$(".actions .dropdown ul li.radio_button a").click(function() {
		$(this).closest('ul').find('a').removeClass('selected');
		$(this).addClass('selected');
	});
	
	// Score a droplet
	$('section.source div.actions ul.dropdown li.useful a.selected').closest('ul.dropdown').siblings('p.score').children('a').addClass('scored');

	// Display "Edit Multiple"
	function countChecked() {
		var editMultiple = $("article.item div.checkbox input:checked").length;
		if (editMultiple == 1) {
			$('.edit_multiple').fadeOut('fast');
			$('.edit_single').fadeIn('fast');
		}
		else if (editMultiple >= 2) {
			$('.edit_single').fadeOut('fast');
			$('.edit_multiple').fadeIn('fast');
		}
		else {
			$('.edit_multiple,.edit_single').fadeOut('fast');
		}
	}
	countChecked();
	$("article.item div.checkbox input").click(countChecked);

	// Hide dropdowns on click outside
	$(document).click(function(e) {
		if(e.isPropagationStopped()) return;
	    $('.dropdown').fadeOut('fast');
		$('.actions p, .actions span').removeClass('active');
		$('.has_dropdown').removeClass('active');
	});
});


function submitForm(button){
	var form = $(button).parents('form:first');
	form.submit();
}

function submitAjax(button){
	var form = $(button).parents('form:first');
	form.submit();
}