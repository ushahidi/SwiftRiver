<script type="text/javascript">
$(function(){ 
	
	var isPageFetching = false;
	
	function doInvite() {
		var email = $("input[name=new_email]").val();
		
		if (!email.length || isPageFetching)
			return
			
		isPageFetching = true;
		
		var loading_msg = window.loading_image.clone().append("<br />Sending invite...");
		var save_toolbar = $(".save-toolbar .button-blue").clone();
		$(".alert-message").hide();
		
		// Show the loading message if syncing takes longer than 500ms
		var t = setTimeout(function() { $(".save-toolbar .button-blue").replaceWith(loading_msg); }, 500);
		$.ajax({
			url: "<?php echo URL::site('login/register_ajax') ?>",
			type: "POST",
			dataType: 'json',
			data: {new_email: email, invite: true},
			complete: function(){
				clearTimeout(t);
				loading_msg.replaceWith(save_toolbar);
				isPageFetching = false;
			},
			success: function(data) {
				if(data.status == 'OK') {
					$(".alert-message.blue").find("span.message").html(data.messages[0]).parents(".alert-message").show();
					$("input[name=new_email]").val("");
				} else {
					$(".alert-message.red").find("span.message").html(data.errors[0]).parents(".alert-message").show();
				}
			},
			error: function() {
				$(".alert-message.red").find("span.message").html("<?php echo __('Unable to send invite. Try again later.'); ?>").parents(".alert-message").show();
			}
		});
		
		return false;
	}
	
	$(".button-blue a").click(doInvite);
	
	// Send the invite if the enter key is pressed in the input field
	$("input[name=new_email]").keypress(function (e) {
		if(e.which == 13){
			return doInvite();
		}
	});
	
	// Focus the search box
	$("input[name=new_email]").focus();
	
	// File upload handler
	var loading_msg = window.loading_image.clone();
	var file_input = $("span.has_file").clone(true);
	var t;
	function setFileHandler() {
		$("#email_list").fileupload({
			dataType: 'json',
			add: function(e, data) {
				data.submit();
			},
			start: function (e) {
				$(".alert-message").hide();
				// Show the loading message if syncing takes longer than 500ms			
				t = setTimeout(function() { $("span.has_file").replaceWith(loading_msg); }, 500);
			},
			done: function (e, data) {
				clearTimeout(t);
				loading_msg.replaceWith(file_input);
				setFileHandler();
				response = data.result;
				if (!response.status_ok) {
					var error_msg = "<ul>";
					for (key in response.errors) {
						error_msg += "<li>" + response.errors[key] + "</li>";
					}
					error_msg += "</ul>";
					$(".alert-message.red").find("span.message").html(error_msg).parents(".alert-message").show();
				} else {
					$(".alert-message.blue").find("span.message").html("All invites sent successfully.").parents(".alert-message").show();
				}
			},
			fail: function (e, data) {
				clearTimeout(t);
				loading_msg.replaceWith(file_input);
				setFileHandler();
				$(".alert-message.red").find("span.message").html("<?php echo __('Unable to send invite. Try again later.'); ?>").parents(".alert-message").show();
			}
		});
	}
	setFileHandler();
});

</script>

<div class="alert-message red" style="display:none">
	<p><span class="message"></span></p>
</div>

<div class="alert-message blue" style="display:none">
	<p><span class="message"></span></p>
</div>

<?php echo Form::open(); ?>
<article class="container base">
	<header class="cf">
		<div class="property-title col_12">
			<h1>Select a file</h1>
		</div>
	</header>
	<section class="property-parameters">
		<div class="parameter">
			<div class="field">
				<p class="field-label">File</p>
				<span class="button-blue has_file"><a href="#">Select file</a><input type="file" name="file" id="email_list"></span>
			</div>
			<div class="save-toolbar">
				<p class="button-blue"><a href="#">Send invite</a></p>
				<p class="button-blank cancel"><a href="#">Cancel</a></p>
			</div>			
		</div>
	</section>
</article>

<article class="container base">
	<header class="cf">
		<div class="property-title col_12">
			<h1>Or enter an email address</h1>
		</div>
	</header>
	<section class="property-parameters">
		<div class="parameter">
			<div class="field">
				<p class="field-label">Email</p>
				<input type="email" name="new_email" />
			</div>
			<div class="save-toolbar">
				<p class="button-blue"><a href="#">Send invite</a></p>
				<p class="button-blank cancel"><a href="#">Cancel</a></p>
			</div>			
		</div>
	</section>
</article>
<?php echo Form::close(); ?>