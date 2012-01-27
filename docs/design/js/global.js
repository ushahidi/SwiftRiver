$(document).ready(function() {
	// Hide dropdowns on click outside
	$('body').live('click', function() {
	    $('ul.dropdown').fadeOut('fast');
		$('.actions p, .actions span').removeClass('active');
		$('.has_dropdown').removeClass('active');
	});
	
	// View or hide a dropdown menu
	$('.has_dropdown > a').click(function(e) {
		// $('ul.dropdown').fadeOut('fast');
		$(this).parent().toggleClass('active');
	    $(this).siblings('ul.dropdown').fadeToggle('fast');
		e.stopPropagation();
	});
	$('ul.dropdown li.cancel').click(function() {
		$(this).parent().fadeOut('fast');
	});
	$('.actions .button_delete, .actions p.bucket, .actions p.score').live('click', function(e) {
		$(this).toggleClass('active');
		$(this).siblings('ul.dropdown').fadeToggle('fast');
		e.stopPropagation();
	});
	
	// Create new bucket
	$('li.create-new').live('click', function(e) {
		$(this).empty();
		$(this).parents('ul.dropdown').append('<li class="create-name"><input type="text" value="" placeholder="Name your new bucket"><div class="buttons"><button class="save">Save</button><button class="cancel">Cancel</button></div></li>');
		e.stopPropagation();
		$('li.create-name').click(function(e) {
			e.stopPropagation();	
		});
		$('button.cancel').click(function(e) {
			$(this).closest('ul.dropdown').children('li.create-new').append('<a onclick=""><span class="create_trigger"><em>Create new</em></span></a>');
			$(this).closest('li.create-name').remove();
			e.stopPropagation();
		});
	});
	
	// Edit page contents
	$('.edit-trigger').live('click', function() {
		var inputValue = $(this).html();
		var inputType = $(this).attr('title');
		$(this).closest('.edit').append('<div class="buttons"><button class="save">Save</button><button class="cancel">Cancel</button></div>');
		if (inputType == 'text') {
			$(this).replaceWith('<span class="edit_input"><input type="' + inputType + '" value="' + inputValue + '" placeholder="Enter the name of your River"></span>');			
		}
		else if (inputType == 'textarea') {
			$(this).replaceWith('<span class="edit_input"><textarea rows="6">' + inputValue + '</textarea></span>');				
		}
		$('button.cancel').click(function() {
			if (inputType == 'text') {
				$(this).closest('.edit').find('.edit_input').replaceWith('<span class="edit-trigger" title="text" onclick="">' + inputValue + '</span>');
			}
			else if (inputType == 'textarea') {
				$(this).closest('.edit').find('.edit_input').replaceWith('<span class="edit-trigger" title="textarea" onclick="">' + inputValue + '</span>');
			}
			$(this).parent().remove();
		});
	});
	
	// View or hide page actions panel
	if (screen.width <= 600) {
		$('ul.views li').not('ul.views li.active, ul.views li.view_panel').remove();
	}
	$('section.panel nav ul li.view_panel a').toggle(function(e) {
			var url = $(this).attr('href');
			$('ul.views li, ul.actions li').fadeTo('fast', 0);
			$(this).parent('li').addClass('active').fadeTo('fast', 1);
			$('section.panel div.panel_body').slideDown('fast').load(url);
			e.preventDefault();				
		}, function(e) {
			$('ul.views li, ul.actions li').fadeTo('fast', 1);
			$(this).parent('li').removeClass('active');
			$('section.panel div.panel_body').slideUp('fast').empty();				
			e.preventDefault();
	});
	$('section.panel a.close').live('click', function() {
		$('ul.views li, ul.actions li').fadeTo('fast', 1);
		$('section.panel nav ul li.view_panel').removeClass('active');
		$('section.panel div.panel_body').slideUp('fast').empty();
	});
	
	// Toggle following or subscribing
	$('.button-change a.subscribe').click(function() {
		$(this).parent().toggleClass('active');
		$(this).toggleClass('subscribed');
	});
	
	// Show/Hide a droplet's detail drawer
	$('section.actions p.button_view a').toggle(function(e) {
			var url = $(this).attr('href');
			$(this).addClass('detail_hide').closest('article.droplet').children('section.detail').slideDown('slow').load(url);
			e.preventDefault();				
		}, function(e) {
			$(this).removeClass('detail_hide').closest('article.droplet').children('section.detail').slideUp('slow').empty();
			e.preventDefault();
	});
	$('section.detail div.bottom a.close').live('click', function() {
		$(this).closest('article.droplet').find('section.actions p.button_view a').removeClass('detail_hide');
		$(this).closest('article.droplet').children('section.detail').slideUp('slow').empty();
	});

	// Add or remove a droplet from buckets
	$('section.actions ul.dropdown li.bucket a.selected').closest('ul.dropdown').siblings('p.button-change').children('a').addClass('bucket_added');
	jQuery.fn.checkBuckets = function() {
		if ($('section.actions ul.dropdown li.bucket a').is('.selected')) {
			$(this).closest('ul.dropdown').siblings('p.bucket').children('a').addClass('bucket_added');
		}
		else {
			$(this).closest('ul.dropdown').siblings('p.bucket').children('a').removeClass('bucket_added');
		}
	};
	$('section.actions ul.dropdown li.bucket a').live('click', function(e) {
		$(this).toggleClass('selected').checkBuckets();
		e.stopPropagation();
	});
	
	// Score a droplet
	$('section.source div.actions ul.dropdown li.useful a.selected').closest('ul.dropdown').siblings('p.score').children('a').addClass('scored');
});