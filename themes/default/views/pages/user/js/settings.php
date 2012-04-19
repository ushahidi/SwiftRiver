<script type="text/javascript">
$(document).ready(function() {
	// Prompt for current password in a modal window
	$('.save-toolbar .button-blue a').live('click', function(e) {
		if (!$(this).parents('.save-toolbar').hasClass('visible'))
			return false;
		
		// Button clicked in the modal window or on the settings page?
		if ($(this).parents(".modal").length) {
			var loading_msg = window.loading_image.clone();
			$("article.modal .button-blue").filter(":visible").replaceWith(loading_msg);
			var pass = $('article.modal input[name=current_password_prompt]').filter(":visible").val()
			$("#account-settings-form input[name=current_password]").val(pass);
			$("#account-settings-form").submit();
		} else {
			modalShow($("#password_prompt").clone().show());
		}
		
		return false;
	});	
});
</script>