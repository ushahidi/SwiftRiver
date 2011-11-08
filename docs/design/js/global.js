$(document).ready(function() {
	// Hide dropdowns on click outside
	$('ul.dropdown').bind('clickoutside', function(event) {
		$(this).fadeOut('fast');
		$('section.actions p.button_change').removeClass('active');
		$('.has_dropdown').removeClass('active');
	});
	
	// View or hide a dropdown menu
	$('.has_dropdown > a').click(function(event) {
		// $('ul.dropdown').fadeOut('fast');
		$(this).parent().toggleClass('active');
	    $(this).siblings('ul.dropdown').fadeToggle('fast');
		event.stopPropagation();
	});
	
	// Create new bucket
	$('li.create_new').live('click', function() {
		$(this).empty();
		$(this).parents('ul.dropdown').append('<li class="create_name"><input type="text" value="" placeholder="Name your new bucket"><div class="buttons"><button class="save">Save</button><button class="cancel">Cancel</button></div></li>');
		$('button.cancel').click(function(event) {
			$(this).closest('ul.dropdown').children('li.create_new').append('<a onclick=""><span class="create_trigger"><em>Create new</em></span></a>');
			$(this).closest('li.create_name').remove();
			event.stopPropagation();
		});
	});
	
	// Edit page contents
	$('.edit_trigger').live('click', function() {
		var inputValue = $(this).text();
		$(this).replaceWith('<span class="edit_input"><input type="text" value="' + inputValue + '" placeholder="Enter the name of your River"></span>');
		$('.edit').append('<div class="buttons"><button class="save">Save</button><button class="cancel">Cancel</button></div>');
		$('button.cancel').click(function() {
			$(this).parent().remove();
			$('.edit_input').replaceWith('<span class="edit_trigger" onclick="">' + inputValue + '</span>');
		});
	});
	
	// View or hide page actions panel
	$('section.panel nav ul li.view_panel a').toggle(function(e) {
			var url = $(this).attr('href');
			$('ul.views').fadeOut('fast');
			$(this).parent('li').addClass('active');
			$('section.panel div.panel_body').slideDown('fast').load(url);
			e.preventDefault();				
		}, function(e) {
			$('ul.views').fadeIn('fast');
			$(this).parent('li').removeClass('active');
			$('section.panel div.panel_body').slideUp('fast').empty();				
			e.preventDefault();
	});
	
	// Toggle following or subscribing
	$('.button_change a.subscribe').click(function() {
		$(this).parent().toggleClass('active');
		$(this).toggleClass('subscribed');
	});
	
	// Show a droplet's detail drawer
	$('section.actions p.button_view a').toggle(function(e) {
			var url = $(this).attr('href');
			$(this).addClass('detail_hide').closest('article.droplet').children('section.detail').slideDown('slow').load(url);
			e.preventDefault();				
		}, function(e) {
			$(this).removeClass('detail_hide').closest('article.droplet').children('section.detail').slideUp('slow').empty();
			e.preventDefault();
	});

	// Add or remove a droplet from buckets
	$('section.actions p.bucket').click(function(event) {
		// $('ul.dropdown').fadeOut('fast');
		$(this).toggleClass('active');
		$(this).siblings('ul.dropdown').fadeToggle('fast');
		event.stopPropagation();
	});
	$('section.actions ul.dropdown li.bucket a.selected').closest('ul.dropdown').siblings('p.button_change').children('a').addClass('bucket_added');
	jQuery.fn.checkBuckets = function() {
		if ($('section.actions ul.dropdown li.bucket a').is('.selected')) {
			$(this).closest('ul.dropdown').siblings('p.bucket').children('a').addClass('bucket_added');
		}
		else {
			$(this).closest('ul.dropdown').siblings('p.bucket').children('a').removeClass('bucket_added');
		}
	};
	$('section.actions ul.dropdown li.bucket a').live('click', function() {
		$(this).toggleClass('selected').checkBuckets();
	});
	
	// Score a droplet
	$('section.source div.actions p.score').click(function(event) {
		$(this).toggleClass('active');
		$(this).siblings('ul.dropdown').fadeToggle('fast');
		event.stopPropagation();
	});
	$('section.source div.actions ul.dropdown li.useful a.selected').closest('ul.dropdown').siblings('p.score').children('a').addClass('scored');

	// Submit Button
	$(".btn_click").click(function(){
    	var form = $(this).parents('form:first');
    	form.submit();
	});

});