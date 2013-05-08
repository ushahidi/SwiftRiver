<!-- Displayed when the river channels have already been defined but there are no drops yet -->
<article class="stream-message no-drops">
	<?php if ( ! $has_drops): ?>
	<h1><?php echo __("Your river does not have any drops yet"); ?></h1>
	<p>
		<?php echo __("Your river is currently dry but SwiftRiver is in the process of fetching drops from your channels. It's best to either wait or "); ?>
		<?php echo HTML::anchor("#", __("adjust your channels' parameters "), array('id' => 'adjust-river-channels')); ?>
		<a class="button-primary" style="padding:0.3em 0.5em;">
			<span class="icon-add-to-bucket"></span>
		</a>
	</p>
	<div id="stream-illustration"></div>
	<?php else: ?>
	<h2><i class="icon-search"></i><?php echo __("Your search did not return any drops"); ?></h2>
	<p>
		<?php echo __("Click on the 'Add search filter' button to refine your search"); ?>
	</p>
	<?php endif; ?>
</article>
