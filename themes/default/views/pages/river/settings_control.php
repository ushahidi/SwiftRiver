<script type="text/javascript">
// Adds channel options
var ci = 0;
function channelOption(channel, option, option_values){
	if (ci == 0) {
		ci = $('input.filter_option').length;
	}
	if ( typeof (channel) != 'undefined' && channel ) {
		ci++;
		field_type = option_values.type;
		input_name = 'filter['+channel+']['+option+']['+ci+'][value]';
		input_type = 'filter['+channel+']['+option+']['+ci+'][type]';
		$('#'+channel).append('<div class="input" id="channel_option_'+ci+'"><h3>'+option_values.label+' <span>[ <a href="javascript:channelOptionR('+ci+')">&#8212;</a> ]</span></h3>'+channelOptiontype(ci, field_type, input_name, input_type)+'</div>');
		// Focus on the new field[s]
		$('#filter_option_'+ci).focus();
	}
}
// Generate the field and type (text, radio, checkbox etc)
function channelOptiontype(id, field_type, input_name, input_type){
	switch(field_type) {
		case 'password':
			return '<input type="password" class="filter_option" id="filter_option_'+id+'" name="'+input_name+'" /><input type="hidden" class="filter_option" name="'+input_type+'" value="password" />';
			break;
		default:
			return '<input type="text" class="filter_option" id="filter_option_'+id+'" name="'+input_name+'" /><input type="hidden" class="filter_option" name="'+input_type+'" value="text" />';
	}	
}

// Deletes channel options
function channelOptionR(id){
	if ( typeof (id) != 'undefined' && id ) {
		$('#channel_option_'+id).remove();
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
//	$("div.input").each(function(index) {
//		$(this).addEventListener('remove', function() {
//			console.log('deleting item');
//		}, false);
//	});

	// When the "Apply Changes" button is clicked
	$("#settings_apply").click(function() {
		<?php if (isset($river) AND $river->loaded()): ?>
			var filters = $("input.filter_option").serializeArray();
			filters.push({name: 'river_id', value: <?php echo $river->id; ?>})
			$.post('<?php echo $base_url; ?>ajax_channel_filters', filters, function(response){
				//console.log(response);
				if ( typeof(response.status) != 'undefined' ) {
					if (response.status == 'success') {
						$('#messages').html('<div class="system_message system_success"><p><strong><?php echo __('Success!'); ?></strong> <?php echo __('Your filters have been updated'); ?>.</p></div>');
					} else if (response.status == 'error') {
						var errors = response.errors;
						var html = '';
						for (i in errors){
							html += '<div class="system_message system_error"><p><strong><?php echo __('Uh oh.'); ?></strong> '+errors[i]+'</p></div>';
						}
						$('#messages').html(html);
					};
				}
			}, 'json');
		<?php else: ?>
			var form = $(this).parents('form:first');
			form.submit();
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
						<article id="<?php echo $key; ?>" class="tab_content">
							<?php if (isset($channel['options'])): ?>
							<ul class="channel_options cf">
								<?php foreach ($channel['options'] as $option_key => $option): ?>
									<li><a href="javascript:channelOption('<?php echo $key; ?>', '<?php echo $option_key; ?>', <?php echo rawurlencode(json_encode($option)); ?>)"><span></span><?php echo $option['label']; ?></a></li>
								<?php endforeach; ?>
							</ul>
							<?php endif; ?>
							
							<?php if (isset($post['filter'][$key])): ?>
								<!-- Display each of the  configured channel filter options -->
								<?php 
								$ci = 0;
								foreach ($post['filter'][$key] as $option_key => $option):
									foreach ($post['filter'][$key][$option_key] as $option_value): ?>
										<div id="<?php echo 'channel_option_'.$ci; ?>" class="input">
											<h3>
												<?php echo $channel['options'][$option_key]['label']; ?>
												<span>[<a href="javascript:channelOptionR('<?php echo $ci; ?>')">&mdash;</a>]</span>
											</h3>
											<?php
											switch ($channel['options'][$option_key]['type']):
												case 'password': ?>
													<input type="password" class="filter_option" name="filter[<?php echo $key.']['.$option_key.']['.$ci.'][value]'; ?>" value="<?php echo $option_value['value']; ?>" />
													<input type="hidden" class="filter_option" name="filter[<?php echo $key.']['.$option_key.']['.$ci.'][type]'; ?>" value="password" />
													<?php break;												
												default: ?>
													<input type="text" class="filter_option" name="filter[<?php echo $key.']['.$option_key.']['.$ci.'][value]'; ?>" value="<?php echo $option_value['value']; ?>" />
													<input type="hidden" class="filter_option" name="filter[<?php echo $key.']['.$option_key.']['.$ci.'][type]'; ?>" value="text" />
													<?php
													break;
											endswitch;
											?>					
										</div>
										<?php
										$ci++;
									endforeach;
								endforeach;
							endif; ?>
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
			<p class="button_go"><a href="#" id="settings_apply" onclick="">Apply changes</a></p>
			<p class="other"><a href="#" class="close" onclick="">Cancel</a></p>
			<?php if (isset($river) AND $river->loaded()) : ?>
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