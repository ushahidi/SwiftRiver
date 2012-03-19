$(document).ready(function() {	
	// MOVE ARTICLE ACTIONS	
	$.each($('div.canvas article.item section.actions, article.single > div.canvas > div.summary > section.actions'), function() {
		var articleTools = $(this).siblings('div.content').children('div.button');
		$(this).append(articleTools);
		if ($(this).children().length > 1) {
			$(this).addClass('two_buttons');
		}
	});
	
	function hideDropdowns() {
		$('.dropdown').fadeOut('slow');
		$('.actions p, .actions span, .actions h3').removeClass('active');
		$('.actions .button_change, .actions span').removeClass('active');
		$('.has_dropdown > a, .actions .button-delete, .actions .button-change').removeClass('active');
	}
		

	// TOGGLE DROPDOWN
	$('.dropdown .cancel').live('click', function(e) {
		$(this).closest('.dropdown').fadeOut('fast');
		$(this).closest('.dropdown').siblings('p').removeClass('active');
	});
	$('.has_dropdown > a, .actions .button-delete, .actions .button-change').live('click', function(e) {
		
		// Hide any other dropdowns that may be open
		if (! $(this).hasClass('active')) {
			hideDropdowns();
		}
		
		$(this).toggleClass('active');
		$(this).siblings('.dropdown').fadeToggle('fast')
		e.stopPropagation();
		return false;
	});
	$('.dropdown').live('click', function(e) {
		e.stopPropagation();
	});
	
	// Hide dropdowns on click outside
	$(document).click(function(e) {
		if(e.isPropagationStopped()) return;
	    hideDropdowns();
	});
	
	
	// TOGGLE PANEL, DRAWER
	if (screen.width <= 600) {
		$('ul.views li').not('ul.views li.active, ul.views li.more').remove();
	}
	else {
		$('ul.views li.more').remove();
	}
	
	$("section.panel nav ul li.view-panel a").toggle(
		function(e) {
			var url = $(this).attr("href");
			$('ul.views li, ul.actions li').fadeTo('fast', 0);
			$(this).parent('li').addClass('open').fadeTo('fast', 1);
			$('section.panel div.drawer').slideDown(200).load(url);
			e.preventDefault();
		}, 
		function(e) {
			$('ul.views li, ul.actions li').fadeTo('fast', 1);
			$(this).parent('li').removeClass('open');
			$('section.panel div.drawer').slideUp(200).empty();
			e.preventDefault();
		}
	);
	
	// TODO: E.Kala Review this segment
	$('section.panel nav ul.actions li.view-panel a').live('click', function() {
		$.getScript('/themes/default/media/js/settings.js');
	});
    
	$('section.panel a.close').live('click', function() {
		$('section.panel nav, .canvas > .container').fadeIn('fast');
		$('ul.views li, ul.actions li').fadeTo('fast', 1);
		$('section.panel nav ul.actions li.view_panel').removeClass('active');
		$('section.panel div.drawer').slideUp('fast').remove();
		return false;
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
	

	// Display "Edit Multiple"
	function countChecked() {
		var editMultiple = $("article.item div.checkbox input:checked").length;
		if (editMultiple == 1) {
			$('.edit-multiple').fadeOut('fast');
			$('.edit-single').fadeIn('fast');
		}
		else if (editMultiple >= 2) {
			$('.edit-single').fadeOut('fast');
			$('.edit-multiple').fadeIn('fast');
		}
		else {
			$('.edit-multiple,.edit-single').fadeOut('fast');
		}
	}
	countChecked();
	$("article.item div.checkbox input").click(countChecked);
	

	// Submit form when enter key hit in a password field
	$('input[type=password]').keypress(function(e){
		if(e.which == 13){
			$(this).parents('form:first').submit();
			e.preventDefault();
		}
	});
	
	// Global Bucket list
	window.Bucket = Backbone.Model.extend({
		defaults: {
			account_id: logged_in_account
		},
		initialize: function() {
						
			// Namespace bucket name if the logged in user is not the owner
			if (parseInt(this.get("account_id")) != logged_in_account) {
				this.set('bucket_name', this.get("account_path") + " / " + this.get("bucket_name"));
			}
		}
	});

	// Collection for all the buckets accessible to the current user
	window.BucketList = Backbone.Collection.extend({
		model: Bucket,
		url: buckets_url
	});
	
	// View for individual bucket item in a droplet list dropdown
	window.HeaderBucketView = Backbone.View.extend({
		tagName: "li",
				
		template: _.template($("#header-bucket-template").html()),
		
		render: function() {
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		}
	});
	
	window.bucketList = new BucketList();
	
	var HeaderBucketList = Backbone.View.extend({
	
		el: $("#header_dropdown_buckets"),
		
		initialize: function() {
			bucketList.on('add', this.addBucket, this);
			bucketList.on('reset', this.addBuckets, this); 
		},
		
		
		addBucket: function(bucket) {
			var bucketView = new HeaderBucketView({model: bucket});
			this.$el.prepend(bucketView.render().el);
		},
		
		addBuckets: function() {
			bucketList.each(this.addBucket, this);
		}
	});
	
	var headerBucketList = new HeaderBucketList();
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