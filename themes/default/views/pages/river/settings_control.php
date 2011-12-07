<script type="text/javascript">
// Adds channel options
var ci = 0;
function channelOption(channel, option, label){
	if (ci == 0) {
		ci = $('input.filter_option').length;
	}
	if ( typeof (channel) != 'undefined' && channel ) {
		ci++;
		$('#'+channel).append('<div class="input" id="channel_option_'+ci+'"><h3>'+label+' <span>[ <a href="javascript:channelOptionR(\'channel_option_'+ci+'\')">&#8212;</a> ]</span></h3><input type="text" class="filter_option" name="'+channel+'_'+option+'[]" /></div>');
	}
}

// Deletes channel options
function channelOptionR(id){
	if ( typeof (id) != 'undefined' && id ) {
		$('#'+id).remove();
		var data = {
			filter_option_id: id.substr("channel_option_".length)
		}
		
		// Submit the selected channel filter option for deleting
		$.post('<?php echo $base_url; ?>ajax_delete_option', data, function(response) {
			if (response.success) {
				// Show success message
			} else {
				// Show delete message
			}
		}, 'json');
	}
}
	
$(document).ready(function() {
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
	
	
	$('ul.tabs li.button_view a span.switch').click(function() {
		$(this).toggleClass('switch_on').toggleClass('switch_off');
		
		// Get the new status and channel name
		var enabled = $(this).hasClass('switch_on')? 1 : 0;
		var channel = $(this).attr("id").substr("channel_".length);
		
		// POST data
		var data = {
			enabled: enabled, 
			channel: channel, 
			river_id: <?php echo (isset($river) ? $river->id : 0); ?>
		};
		
		// Submit the data
		$.post('<?php echo $base_url; ?>ajax_channels', data, function(response){
			if (response.success) {
				// Show success message
			} else {
				// Show error message
			}
		});
	});
	
	// When an filter option is about to be removed
	$("div.input").each(function(index) {
		$(this).addEventListener('remove', function() {
			console.log('deleting item');
		}, false);
	});

	// When the "Apply Changes" button is clicked
	$("#settings_apply").click(function() {
		var filterOptions = {};
		filterOptions.options = [];
		
		// Get all the input fields
		$("input.filter_option").each(function(index) {
			// Get the element name
			var channelKey = $(this).attr("name").split("_");
			var optionItem = {};
		
			// Get the channel filter option and value
			if (typeof($(this).attr("id")) !=  'undefined') {
				optionId = $(this).attr("id").substr("filter_option_".length);
				optionItem.filter_option_id = optionId;
			} else {
				optionItem.filter_option_id = '';
			}
		
			optionItem.filter_channel = channelKey[0];
			optionItem.filter_option_key = channelKey[1].substring(0, channelKey[1].length-2)
			optionItem.filter_option_value = $(this).val();
			filterOptions.options.push(optionItem);
		});
		
		// Add the River ID
		filterOptions.river_id = <?php echo (isset($river) ? $river->id : 0); ?>;
		
		// Submit the data for saving
		$.post('<?php echo $base_url; ?>ajax_channel_options', filterOptions, function(response) {
			if (response.success) {
				// Changes saved - show success message
			} else {
				// An error occurred while saving
				// Backtrack to the affected filter option
			}
		}, 'json');
		
		return false;
	});
	
});
</script>

<div id="channels">
	<div class="controls">
		<div class="row cf">
			<h2>Channels</h2>
			<div class="tab_controls cf">
				<ul class="tabs">
					<?php foreach ($channels as $key => $channel): ?>
						<li class="button_view <?php echo $key; ?>">
							<a href="#<?php echo $key; ?>">
								<?php $switch_class = ($channel['enabled'] == 1)? 'switch_on' : 'switch_off'; ?>
								<span class="switch <?php echo $switch_class; ?>" id="channel_<?php echo $key; ?>"></span>
								<span class="label"><?php echo $channel['name']; ?></span>
							</a>
						</li>
					<?php endforeach; ?>
					<li class="more"><a href="#">More channels</a></li>
				</ul>				
				<div class="tab_container">
					<?php foreach ($channels as $key => $channel): ?>
						<article id="<?php echo $key; ?>" class="tab_content">
							<?php if (isset($channel['options'])): ?>
							<ul class="channel_options cf">
								<?php foreach ($channel['options'] as $option_key => $option_value): ?>
									<li><a href="javascript:channelOption('<?php echo $key; ?>', '<?php echo $option_key; ?>', '<?php echo $option_value; ?>')"><span></span><?php echo $option_value; ?></a></li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>
							
							<?php
							$c = 0;
							if (isset($post[$key])): ?>
								<!-- Display each of the  configured channel filter options -->
								<?php foreach ($post[$key] as $option): ?>
								
								<div id="channel_option_<?php echo $c; ?>" class="input">
									<h3>
										<?php echo $channel['options'][$option['key']]; ?>
										<span>[<a href="javascript:channelOptionR('<?php echo $channel_option_id; ?>')">&mdash;</a>]</span>
									</h3>
									<input type="text" class="filter_option" id="filter_option_<?php echo $option['id']; ?>" name="<?php echo $key."_".$option['key']?>[]" value="<?php echo $option['value']; ?>">
								</div>
								
								<?php
								$c++;
								endforeach; ?>
							<?php endif; ?>
						</article>				
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php if ( isset($river) AND $river->loaded() ) : ?>
			<div class="row controls cf">
				<h2>Collaborators</h2>
				<div class="input">
					<h3>Add people to collaborate on this River</h3>
					<input type="text" placeholder="+ Type name..." />
				</div>
				
				<!-- TODO - Check if there are people collaborating on this river -->
				<div class="list_stream">
					
					<!--
					<h3>People who collaborate on this River</h3>
					<ul class="users">
						<li>
							<a href="#">Caleb Bell</a>
							<div class="actions">
								<span class="button_delete"><a onclick="">Remove</a></span>
								<ul class="dropdown right">
									<p>Are you sure you want to stop collaborating with this person?</p>
									<li class="confirm"><a onclick="">Yep.</a></li>
									<li class="cancel"><a onclick="">No, nevermind.</a></li>
								</ul>
							</div>
						</li>
					</ul>
					-->
					
				</div>
				
			</div>
		<?php endif; ?>
		<div class="row controls_buttons cf">
			<p class="button_go"><a id="settings_apply">Apply changes</a></p>
			<p class="other"><a class="close" onclick="">Cancel</a></p>
			<?php if ( isset($river) AND $river->loaded() ) : ?>
				<div class="item actions">
					<p class="button_delete button_delete_subtle"><a onclick="">Delete River</a></p>
					<div class="clear"></div>
					<ul class="dropdown">
						<p>Are you sure you want to delete this River?</p>
						<li class="confirm"><a onclick="">Yep.</a></li>
						<li class="cancel"><a onclick="">No, nevermind.</a></li>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>