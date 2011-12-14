function inlineEdit() {
	inputID = $('#inline_edit_id').val();
	inputName = $('#inline_edit_name').val();
	inputValue = $('#inline_edit_text').val();

	if ( (typeof (inputID) != 'undefined' && inputID) && (typeof (inputName) != 'undefined' && inputName) && (typeof (inputValue) != 'undefined' && inputValue) ) {
		$.post('<?php echo URL::site()?>'+inputName+'/ajax_title', { edit_id: inputID, edit_value: inputValue },
			function(data){
				$('button.cancel').parent().remove();
				$('.edit_input').replaceWith('<span class="edit_trigger" title="'+ inputName +'" id="'+ inputID +'" onclick="">' + inputValue + '</span>');
			}, "json");
	}
}

// Add/Remove Droplet from Bucket
function addBucketDroplet(bucket, bucket_id, droplet_id){
	// get the new action from the link title
	var bucket_action = $(bucket).attr('title');
	$.post('<?php echo URL::site()?>bucket/ajax_droplet', {
		bucket_id: bucket_id, 
		droplet_id: droplet_id,
		action: bucket_action
	},
	function(data){
		if ( typeof(data.status) != 'undefined' ) {
			if (data.status == 'success') {
				if (bucket_action == 'add') {
					$(bucket).parents('ul.dropdown').siblings('p.bucket').children('a').addClass('bucket_added');
					$(bucket).toggleClass('selected').checkBuckets();
					$(bucket).attr('title', 'remove');
					event.stopPropagation();					
				}
				else {
					$(bucket).parents('ul.dropdown').siblings('p.bucket').children('a').removeClass('bucket_added');
					$(bucket).toggleClass('selected').checkBuckets();
					$(bucket).attr('title', 'add');
					event.stopPropagation();
				}
				event.stopPropagation();
			} else if (data.status == 'error') {
				// some kind of error
			};		
		}
	}, 'json');
	event.stopPropagation();
}

// Create A Bucket
function createBucket(create, where, droplet_id){
	console.log(where);
	droplet_id = parseInt(droplet_id);
	$(create).empty();
	$(create).parents('ul.dropdown').append('<li class="create_name"><input type="text" id="bucket_name" name="bucket_name" value="" placeholder="<?php echo __('Name your new bucket'); ?>"><div class="buttons"><button class="save"><?php echo __('Save'); ?></button><button class="cancel"><?php echo __('Cancel'); ?></button></div></li>');
	event.stopPropagation();
	$('li.create_name').click(function(e) {
		e.stopPropagation();	
	});
	$('button.save').click(function(e) {
		$.post('<?php echo URL::site()?>bucket/ajax_new', { bucket_name: $('#bucket_name').val() },
		function(data){
			if ( typeof(data.status) != 'undefined' ) {
				if (data.status == 'success') {
					if (where == 'droplet') {
						$('<li class="bucket"><a onclick="addBucketDroplet(this, '+data.bucket.id+', '+droplet_id+')" title="add" class="" ><span class="select"></span>'+data.bucket.name+'</a></li>').insertBefore('li.create_new');
					} else {
						$('<li><a href="<?php echo $base_url; ?>bucket/index/'+data.bucket.id+'">'+data.bucket.name+'</a></li>').insertBefore('li.create_new');
					}
					$('button.cancel').closest('ul.dropdown').children('li.create_new').append('<a onclick=""><span class="create_trigger"><em>Create new</em></span></a>');
					$('button.cancel').closest('li.create_name').remove();
					e.stopPropagation();
				} else if (data.status == 'error') {
					var errors = data.errors;
					var html = '';
					for (i in errors){
						html += '<?php echo __('Uh oh.'); ?> '+errors[i]+'\n';
					}
					alert(html);
				};
			}
		}, 'json');			
	});
	$('button.cancel').click(function(e) {
		$(create).html('<?php echo __('Create new'); ?>');
		$(create).parents('ul.dropdown').children('li.create_name').remove();
		e.stopPropagation();
	});	
}

var inlineInputValue;
$(document).ready(function() {

	// Inline Editing
	$('.edit_trigger').live('click', function() {
		inlineInputValue = $(this).text();
		inlineInputType = $(this).attr('title');
		inlineInputId = $(this).attr('id').replace(/[^0-9]/g, '');
		$(this).replaceWith('<span class="edit_input"><input type="hidden" id="inline_edit_id" value="'+ inlineInputId +'"><input type="hidden" id="inline_edit_name" value="'+ inlineInputType +'"><input type="text" id="inline_edit_text" value="'+ inlineInputValue +'" placeholder="Enter the name of your River"></span>');
		$('.edit').append('<div class="buttons"><button class="save" onclick="inlineEdit()"><?php echo __('Save'); ?></button><button class="cancel"><?php echo __('Cancel'); ?></button></div>');
		$('button.cancel').click(function() {
			$(this).parent().remove();
			$('.edit_input').replaceWith('<span class="edit_trigger" title="'+ inlineInputType +'" id="edit_'+ inlineInputId +'" onclick="">' + inlineInputValue + '</span>');
		});
	});

	// Add or remove a droplet from buckets
	$('section.actions ul.dropdown li.bucket a.selected').closest('ul.dropdown').siblings('p.button_change').children('a').addClass('bucket_added');
	jQuery.fn.checkBuckets = function() {
		if ($('section.actions ul.dropdown li.bucket a').is('.selected')) {
			$(this).closest('ul.dropdown').siblings('p.bucket').children('a').addClass('bucket_added');
		}
		else {
			$(this).closest('ul.dropdown').siblings('p.bucket').children('a').removeClass('bucket_added');
		}
	};	
});