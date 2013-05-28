<article class="stream-message cf" id="no-activity-message" style="display:none;">
	<h1>This is where you'll see the latest from people, rivers and buckets you follow.</h1>

	<div class="callout">
		<span class="icon-river"></span>
		<h2>Get real-time information flowing.</h2>
		<p>Rivers are real-time streams of information from the channels and topics that matter to you.</p>
		<a href="#" id="create-new-river" class="button-primary"><?php echo __("Create a river"); ?></a>
	</div>

	<div class="callout">
		<span class="icon-bucket"></span>
		<h2>Save your hand-picked information.</h2>
		<p>Buckets are containers for the information you want to save, organize and gain insight from.</p>
		<a href="#" id="create-new-bucket" class="button-primary"><?php echo __("Create a bucket"); ?></a>
	</div>

	<div class="callout">
		<span class="icon-search"></span>
		<h2>Find information and people of interest.</h2>
		<p>Search rivers, buckets and people that are wading through real-time information.</p>
		<a href="<?php echo URL::site('search', TRUE); ?>" id="general-search" class="button-primary"><?php echo __("Search"); ?></a>
	</div>
</article>

<script type="text/javascript">
$("#create-new-bucket").on("click", function() {
	var view = new Assets.CreateBucketModalView({closable: true});
	modalShow(view.render().el);
	return false;
});

$("#create-new-river").on("click", function(){
	var view = new Assets.CreateRiverModalView({closable: true});
	modalShow(view.render().el);
	return false;
});
</script>