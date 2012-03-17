<script type="text/javascript">
$(function(){ 
	
	var isPageFetching = false;
	
	function doInvite() {
		var email = $("#invites input[name=new_email]").val();
		
		if (!email.length || isPageFetching)
			return
			
		isPageFetching = true;
		
		var loading_msg = window.loading_message.clone().append("<br />Sending invite...");
		loading_msg.appendTo($("#invites div.loading")).show();
		$("#invites button.save").fadeOut();
			
		$.ajax({
			url: "<?php echo URL::site('login/register_ajax') ?>",
			type: "POST",
			dataType: 'json',
			data: {new_email: email, invite: true},
			complete: function(){
				loading_msg.fadeOut().remove();
				isPageFetching = false;
			},
			success: function(data) {
				if(data.status == 'OK') {
					flashMessage($("#invites div.system_success"), data.messages);
				} else {
					flashMessage($("#invites div.system_error"), data.errors);
					$("#invites button.save").fadeIn();
				}
			},
			error: function() {
				flashMessage($("#invites div.system_error"), "<?php echo __('Oops, we are unable to register you at the moment. Try again later.'); ?>");
				$("#invites div.form").fadeIn();
			}
		});
	}
	
	// When the next button is clicked, create the river
	$("#invites button.save").click(doInvite);
	
	// Create the river if the enter key is pressed in the search box
	$("#invites input[name=new_email]").keypress(function (e) {
		if(e.which == 13){
			doInvite();
		}
	});
	
	// Focus the search box
	$("#invites input[name=new_email]").focus();
});

</script>
<div id="invites">
	<div class="controls">
		<div class="row cf">
			<div class="input">
				<h3><?php echo __('Email address'); ?></h3>
				<?php echo Form::input("new_email"); ?>
				<button class="save" type="button">
					<span>Invite!</span>
				</button>
			</div>
		</div>
		<div class="global row cf">
			<div class="loading center"></div>
			<div class="system_error" style="display:none"></div>
			<div class="system_success" style="display:none"></div>
		</div>
	</div>
</div>