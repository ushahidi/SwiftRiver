<script type="text/template" id="follow-button-template">
	<p class="button-white follow has-icon <% if (subscribed) { %> selected <% } %> ">
		<a href="#">
			<span class="icon"></span>
			<% if (subscribed) { %> 
				Following 
			<% } else { %>
				Follow
			<% } %>
		</a>
	</p>
</script>

<script type="text/javascript">
/**
 * Backbone JS wiring for the "Follow" button
 */
$(function() {
	// Boostrap the follow button
	var asset = new Assets.Asset(<?php echo $data; ?>);
	asset.urlRoot = "<?php echo $action_url; ?>";
	(new Assets.FollowButtonView({model: asset})).render();
});
</script>