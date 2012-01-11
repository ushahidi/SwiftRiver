$(document).ready(function() {
	/* Settings primary navigation */
	$('#settings ul.views li a').live('click', function() {
		$('section.panel nav, .canvas > .container').fadeOut('fast');
		return false;
	});
	
	/* Settings secondary navigation */
	$('#settings ul.views li a, #settings ul.settings-navigation li a, #settings a.go').live('click', function() {
		var url = $(this).attr('href');
		if ( url == '/map/settings/' ) {
			$('section.panel nav, .canvas > .container').fadeIn('fast');
		}
		$(this).closest('section.panel').children('div.drawer').load(url + ' section.panel .panel_body', function() {
			/* TABS */
			$(".tab_content").hide(); //Hide all content
			$("ul.tabs li:first").addClass("active").show(); //Activate first tab
			$(".tab_content:first").show(); //Show first tab content	
			$("ul.tabs li").click(function() {
				$("ul.tabs li").removeClass("active"); //Remove any "active" class
				$(this).addClass("active"); //Add "active" class to selected tab
				$(".tab_content").hide(); //Hide all tab content
				var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
				$(activeTab).fadeIn(); //Fade in the active ID content
				return false;
			});
			$('ul.tabs li.button_view a span.switch').live('click', function() {
				$(this).toggleClass('switch_on').toggleClass('switch_off');
			});
			Modernizr.load({
				test: Modernizr.inputtypes.range,
				nope: '/js/jquery.ui.js'
			});
			if (Modernizr.inputtypes.range){
				$('input[type="range"]').change(function() {
					var rangeValue = $(this).val();
					$('span#range').html(rangeValue);
				});
			}
		});
		return false;
	});

	/* Show added inputs */
	$('p.button_view a').live('click', function(e) {
		var url = $(this).attr('href');
		var detailDrawer= $(this).closest('div.row').children('div.detail');
		e.preventDefault();
		if($(detailDrawer).is(':visible')){
			$(this).removeClass('detail_hide');
			$(detailDrawer).slideUp('fast').empty();			
		} else {
			$(this).addClass('detail_hide');
			$(detailDrawer).slideDown('fast').load(url);
		};
	});
	
	//edit field - this is ghetto but it gets the point across 
	$("a.edit_field").live('click', function() { 
	  //show/hide the proper divs
	  $(this).parent().hide().siblings(".unedited").hide().siblings(".the-inputs").show();
	  
	  //change the "delete" button text to "save" and then add a "cancel" link
	  $(this).parent().parent().siblings(".summary").find(".button_delete a").text("Save");
	  $(this).parent().parent().siblings(".summary").find(".cancel").css("display", "block");
	  
	  return false;
	});
	// cancel/save
	$("p.cancel a, .button_delete a").live('click', function() {
	  if ($(this).text() == "cancel")
		$(this).parent().hide();
	  else
		$(this).parent().siblings().find("p.cancel").hide();
	  
	  $(this).parent().parent().find(".button_delete a").text("Delete");
	  $(this).parent().parent().parent().siblings(".content").find("h1").show();
	  $(this).parent().parent().parent().siblings(".content").find(".unedited").show();
	  $(this).parent().parent().parent().siblings(".content").find(".the-inputs").hide();
	});
    // color picker - https://github.com/claviska/jquery-miniColors
    $(".colorpicker").miniColors();
	// display the additional public listing fields after its enabled
	$("input:checkbox[name:'publiclisting']").live('click', function() {
	  if ($(this).attr("checked") == "checked")
		$('.more-fields').slideDown("fast");
	  else
		$('.more-fields').slideUp("fast");
	});
});