// Add/Remove Droplet from Bucket
function addBucketDroplet(bucket, bucket_id, droplet_id) {
	// get the new action from the link title
	var bucket_action = $(bucket).attr('title');
	$.post('<?php echo URL::site()?>bucket/ajax_droplet', {
		bucket_id: bucket_id,
		droplet_id: droplet_id,
		action: bucket_action
	},
	function(data) {
		if (typeof(data.status) != 'undefined') {
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
	},
	'json');
	event.stopPropagation();
}

// Create A Bucket
function createBucket(create, where, droplet_id) {
	console.log(where);
	droplet_id = parseInt(droplet_id);
	$(create).empty();
	$(create).parents('ul.dropdown').append('<li class="create_name"><input type="text" id="bucket_name" name="bucket_name" value="" placeholder="<?php echo __('Name your new bucket '); ?>"><div class="buttons"><button class="save"><?php echo __('Save '); ?></button><button class="cancel"><?php echo __('Cancel '); ?></button></div></li>');
	event.stopPropagation();
	$('li.create_name').click(function(e) {
		e.stopPropagation();
	});
	$('button.save').click(function(e) {
		$.post('<?php echo URL::site()?>bucket/ajax_new', {
			bucket_name: $('#bucket_name').val()
		},
		function(data) {
			if (typeof(data.status) != 'undefined') {
				if (data.status == 'success') {
					if (where == 'droplet') {
						$('<li class="bucket"><a onclick="addBucketDroplet(this, ' + data.bucket.id + ', ' + droplet_id + ')" title="add" class="" ><span class="select"></span>' + data.bucket.name + '</a></li>').insertBefore('li.create-new');
					} else {
						$('<li><a href="<?php echo $base_url; ?>bucket/index/' + data.bucket.id + '">' + data.bucket.name + '</a></li>').insertBefore('li.create-new');
					}
					$('button.cancel').closest('ul.dropdown').children('li.create-new').append('<a onclick=""><span class="create_trigger"><em>Create new</em></span></a>');
					$('button.cancel').closest('li.create_name').remove();
					e.stopPropagation();
				} else if (data.status == 'error') {
					var errors = data.errors;
					var html = '';
					for (i in errors) {
						html += '<?php echo __('Uh oh.'); ?> ' + errors[i] + '\n';
					}
					alert(html);
				};
			}
		},
		'json');
	});
	$('button.cancel').click(function(e) {
		$(create).html('<?php echo __('Create new '); ?>');
		$(create).parents('ul.dropdown').children('li.create_name').remove();
		e.stopPropagation();
	});
}

$(document).ready(function() {

	// Inline Editing
	$('.edit-trigger').live('click',
	function() {

		// Declare these vars as local to create a closure
		// and avoid weird problems if multiple inline edits
		// are being done at once.
		var inlineInputValue = $(this).text();
		var inlineInputType = $(this).attr('title');
		var inlineInputId = $(this).attr('id').replace(/[^0-9]/g, '');

		// Replace the text with a text box and hidden input field registers
		$(this).closest('.edit').append('<div class="buttons"><button class="save"><?php echo __('Save '); ?></button><button class="cancel"><?php echo __('Cancel '); ?></button></div>');
		$(this).replaceWith('<span class="edit_input"><input type="hidden" id="inline_edit_id" value="' + inlineInputId + '"><input type="hidden" id="inline_edit_name" value="' + inlineInputType + '"><input type="text" id="inline_edit_text" value="' + inlineInputValue + '"></span>');

		// When the cancel button is clicked
		$('.edit .buttons button.cancel').click(function() {
			$(this).closest('.edit').find('.edit_input').replaceWith('<span class="edit-trigger" title="' + inlineInputType + '" id="edit_' + inlineInputId + '" onclick="">' + inlineInputValue + '</span>');
			$(this).parent().remove();
		});

		// When the save button is clicked
		$('.edit .buttons button.save').click(function() {
			var inputValue = $(this).closest('.edit').find('.edit_input #inline_edit_text').val();
			var save_button = $(this);
			if ((typeof(inlineInputId) != 'undefined' && inlineInputId) &&
			    (typeof(inlineInputType) != 'undefined' && inlineInputType) &&
			    (typeof(inputValue) != 'undefined' && inputValue)) {
				$.post('<?php echo URL::site()?>' + inlineInputType + '/ajax_title', {
					edit_id: inlineInputId,
					edit_value: inputValue
				},
				function(data) {
					save_button.closest('.edit').find('.edit_input').replaceWith('<span class="edit-trigger" title="' + inlineInputType + '" id="' + inlineInputId + '" onclick="">' + inputValue + '</span>');
					save_button.parent().remove();
				},
				"json");
			}
		});
	});


	//Inline additions
	$('.has_inline_add .button_change a').live('click',
	function(e) {
		$(this).closest('.item').append('<div class="add"><span class="edit_input"><input type="text" id="inline_edit_text" placeholder="<?php echo __('Name your tag '); ?>"/></span><div class="buttons"><button class="save"><?php echo __('Save '); ?></button><button class="cancel"><?php echo __('Cancel '); ?></button></div></div>');

		var ajaxTarget = $(this).attr('href');
		var ajaxType = $(this).attr('title');
		var inputId = $(this).attr('id').replace(/[^0-9]/g, '');

		// When the add button is clicked
		$('.add .buttons .save').click(function() {
			var save_button = $(this);
			var inputValue = $(this).closest('.add').find('.edit_input #inline_edit_text').val();

			if ((typeof(ajaxTarget) != 'undefined' && ajaxTarget) &&
			(typeof(ajaxType) != 'undefined' && ajaxType) &&
			(typeof(inputId) != 'undefined' && inputId) &&
			(typeof(inputValue) != 'undefined' && inputValue)) {
				$.post(ajaxTarget + '/ajax_add_' + ajaxType, {
					edit_value: inputValue,
					id: inputId
				},
				function(data) {
					if (data["status"] == "success") {
						$('#inline_' + ajaxType + '_add').append(data["html"]);
						save_button.closest('.add').remove();
					}
				},
				"json");
			}

		});


		// When the cancel button is clicked
		$('.add .buttons .cancel').click(function() {
			$(this).closest('.add').remove();
		});
		e.preventDefault();
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