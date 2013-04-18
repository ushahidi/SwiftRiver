<!-- Displayed when a bucket does not have any drops -->
<article class="stream-message no-drops">
	<h1><?php echo __("Your bucket is currently empty"); ?></h1>
	<p>
		<?php echo __("You can add a drop to this bucket from anywhere by selecting its \"Add to bucket\" button."); ?>
		<a class="button-primary" style="padding:0.3em 0.5em;">
			<span class="icon-add-to-bucket"></span>
		</a>
	</p>
	<?php echo HTML::image('media/img/bucket-empty.gif'); ?>
</article>
