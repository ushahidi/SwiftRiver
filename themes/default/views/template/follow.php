<script type="text/template" id="follow-button-template">
	<a href="#" class="<% if (following) { %>button-primary selected<% } else { %>button-white<% } %>">
		<% if (following) { %>
			<i class="icon-checkmark"></i>
			<?php echo __("Following"); ?>
		<% } else { %>
			<?php echo __("Follow"); ?>
		<% } %>
	</a>
</script>

<script type="text/javascript">
/**
 * Backbone JS wiring for the "Follow" button
 */
$(function() {
	// Boostrap the follow button
	var data = <?php echo $data; ?>;
	var asset = null;
	
	var collection = null;
	if (data.type == "bucket")
	{
		asset = new Assets.Bucket(data);
		collection = Assets.bucketList;
	}
	else if (data.type == "river")
	{
		asset = new Assets.River(data);
		collection = Assets.riverList;
	} else {
		asset = new Assets.Asset(data);
		asset.on("change", function() {
			var count = parseInt($("#follower_count > strong").html());
			if (asset.get("following")) {
				count += 1;
			} else {
				count -= 1;
			}	
			$("#follower_count > strong").html(count);
		});
	}
	
	asset.urlRoot = "<?php echo $action_url; ?>";
	(new Assets.FollowButtonView({model: asset, collection: collection})).render();
});
</script>