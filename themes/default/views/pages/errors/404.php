<hgroup class="page-title user-title cf">
	<div class="center">
		<div class="col_9">
			<h1><?php echo __("404"); ?></h1>
			<h2 class="label"><?php echo __("Page not found."); ?></h2>
		<div>
	</div>
</hgroup>
<div id="content" class="cf">
	<div class="center">
		<article class="modal">
			<article class="stream-message" style="display:block;">
				<?php echo __("The page - <em><strong>:page</strong></em> - you are looking for does not exist on this server.", array(":page" => $page)); ?>
			</article>
		</article>
	</div>
</div>