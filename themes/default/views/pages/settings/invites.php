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
					$("#invites input[name=new_email]").val("");
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
});

</script>

<div class="alert-message red" style="display:none">
	<p><strong>Uh oh.</strong> <span class="message"></span></p>
</div>

<div class="alert-message blue" style="display:none">
	<p><strong>Success</strong> <span class="message"></span></p>
</div>

<?php echo Form::open(); ?>
<article class="container base">
	<header class="cf">
		<div class="property-title">
			<h1>Enter an email address</h1>
		</div>
	</header>
	<section class="property-parameters">
		<div class="parameter">
			<label for="river_name">
				<p class="field">Email</p>
				<input type="email" name="new_email" />
			</label>
		</div>
	</section>
</article>

<div class="save-toolbar">
	<p class="button-blue"><a href="#">Send invite</a></p>
</div>
<?php echo Form::close(); ?>