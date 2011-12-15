<script type="text/javascript">

/**
 * Adds channel options
 *
 * @param channel - Name of the channel for which to add an option item
 * @param option - Channel option to be added to the UI
 * @param group - Whether to add a single item or group of channel options
 */
function channelOption(channel, option, group) {
	// Get the current item count - varies depending on the value of the "group"
	// parameter
	var item_count = (group) 
	    ? $(".group-item").length 
	    : $(".single .filter-option").length;
	
	if (typeof(channel) != 'undefined' && channel) {
		item_count++;
		
		var postData = {
			channel: channel,
			option: option,
			item_no: item_count
		};
		
		// Post the data for saving
		$.post('<?php echo $base_url?>ajax_channel_option_ui', postData, 
		    function(response) {
			    if (response.success) {
				    $("#"+channel).append(response.html);
			    }
		    }, 
		    "json"
		);
	}
}

// Deletes channel options
function channelOptionR(id) {
	if ( typeof (id) != 'undefined' && id ) {
		$('#'+id).remove();
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

		//Find the href attribute value to identify the active tab + content
		var activeTab = $(this).find("a").attr("href");
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
	
	
	$('ul.tabs li.button_view a span.switch').click(function() {
		$(this).toggleClass('switch_on').toggleClass('switch_off');
		
		// Get the new status and channel name
		var enabled = $(this).hasClass('switch_on')? 1 : 0;
		var channel = $(this).attr("id").substr("channel_".length);
		
	<?php if (isset($river)): ?>
		// POST data
		var data = {
			enabled: enabled, 
			channel: channel, 
			river_id: <?php echo $river->id; ?>};
	
		// Submit the data
		$.post('<?php echo $base_url; ?>ajax_channels', data, function(response){
			if (response.success) {
				// Show success message
			} else {
				// Show error message
			}
		});
	<?php endif; ?>
	});
	
	// When the "Apply Changes" button is clicked
	$('#settings-apply').click(function() {
		var groups = [];
		$(".group-item").each(function(index, item){
			groups.push($(item).find(".input .filter-option").serializeArray());
		});
		
		// Filter data to be submitted for saving
		var filters = {
			options: {
				groups: groups, 
				singles: $(".single .filter-option").serializeArray()
			},
			river_id: <?php echo $river->id; ?>
		};
			
		<?php if (isset($river) AND $river->loaded()): ?>
		// Submit the data
		$.post('<?php echo $base_url; ?>ajax_channel_filters', filters, function(response) {
			if (response.success) {
				$('#messages').html('<div class="system_message system_success"><p>'+
				   '<strong><?php echo __('Success!'); ?></strong>'+
				   '<?php echo __('Your filters have been updated'); ?>.</p></div>');
			} else {
				var errors = response.errors;
				var html = '';
						
				for (i in errors) {
					html += '<div class="system_message system_error">'+
					    '<p><strong><?php echo __('Uh oh.'); ?></strong>'+
					    errors[i]+'</p></div>';
				}
				$('#messages').html(html);
			}
		}, 'json');
		<?php endif; ?>
		
		return false;
	});
	
});

</script>

<div id="channels">
	<div id="messages"></div>
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
						
						<!-- Whether the channel options are grouped -->
						<?php $grouped_options = isset($channel['group']); ?>
						
						<article id="<?php echo $key; ?>" class="tab_content">
							<?php if (isset($channel['options'])): ?>
							<ul class="channel_options cf">
								<?php if ($grouped_options): ?>
									<?php
										// Get the group key and label
										$group_key = $channel['group']['key'];
										$group_label = $channel['group']['label'];
									?>
									<li><a href="javascript:channelOption('<?php echo $key; ?>', '<?php echo $group_key; ?>', true)">
									    <span></span><?php echo $group_label; ?></a>
									</li>
								<?php else: ?>
									<?php foreach ($channel['options'] as $option_key => $option): ?>
										<li><a href="javascript:channelOption('<?php echo $key; ?>', '<?php echo $option_key; ?>', false)">
											<span></span><?php echo $option['label']; ?></a>
									    </li>
									<?php endforeach; ?>
								<?php endif; ?>
							</ul>
							<?php endif; ?>
							
							<?php if (isset($post[$key])): ?>
								<!-- Display each of the  configured channel filter options -->
								<?php  $ci = 0; ?>
								
								<?php 
									// For grouped options, the lookup key in $post is the value
									// contained in $channel['group']['key]
									$lookup_key = ($grouped_options) ? $channel['group']['key'] : $key;
								?>
								
								<?php foreach ($post[$lookup_key] as $index => $item): ?>
									
									<!-- Open <div> for grouped options -->
									<?php if ($grouped_options): ?>
										<div id="<?php echo $lookup_key."-".$index; ?>" class="group_input">
											<h3>
												<?php echo $channel['group']['label']?>
												<span>[<a href="javascript:channelOptionR('channel_group_option_<?php echo $index; ?>')">&mdash;</a>]</span>
											</h3>
									<?php endif; ?>
									
									<?php foreach ($item as $option_key => $option_data): ?>
										<div id="channel-option-<?php echo $ci; ?>" class="input <?php echo ($grouped_options)? "" : "single"; ?>">
											<h3>
												<?php echo $option_data['label']; ?>
												<?php if ( ! $grouped_options): ?>
													<span>[<a href="javascript:channelOptionR('channel_option_<?php echo $ci; ?>')">&mdash;</a>]</span>
												<?php endif; ?>
											</h3>
											
											<?php 
												// Generate the name of the fitler option
												$option_name = ($grouped_options)
												    ? sprintf("%s_%s_%s_%s_%d_%d", $key, $lookup_key, $option_key, 
												        $option_data['type'], $index, $ci)
												
												    : sprintf("%s_%s_%s_%d", $key, $option_key, $option_data['type'], $ci);
												
												// Display the option
												echo Swiftriver_Plugins::get_channel_option_html($option_data, $option_name, $option_data['value']); 
											?>
										</div>
										<?php $ci++; ?>
										
										<!-- END foreach -->
									<?php endforeach; ?>
									
									<!-- Close the <div> for grouped options -->
									<?php if ($grouped_options): ?>
										</div>
									<?php endif; ?>
									
									<!-- END foreach -->
								<?php endforeach; ?>
								
							<?php endif; ?>
						</article>
					<?php endforeach; ?>
				</div>
			</div>
		</div>

		<?php if (isset($river) AND $river->loaded()): ?>
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
			<p class="button_go"><a href="#" id="settings-apply"><?php echo __('Apply changes'); ?></a></p>
			<p class="other"><a href="#" class="close" onclick=""><?php echo __('Cancel / Close'); ?></a></p>
			<?php if (isset($river) AND $river->loaded()) : ?>
				<div class="item actions">
					<p class="button_delete button_delete_subtle"><a onclick=""><?php echo __('Delete River'); ?></a></p>
					<div class="clear"></div>
					<ul class="dropdown">
						<p><?php echo __('Are you sure you want to delete this River?'); ?></p>
						<li class="confirm"><a onclick=""><?php echo __('Yep.'); ?></a></li>
						<li class="cancel"><a onclick=""><?php echo __('No, nevermind.'); ?></a></li>
					</ul>
				</div>
			<?php endif; ?>
		</div>
	</div>
</div>