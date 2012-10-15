<article class="river" id="system_notification">
	<div class="center cf">
		<article class="container base alert-message red">
			<p>
				<?php echo __("Your river has expired and is no longer receiving drops from your channels."); ?>
				<?php echo __("You can click :here to reactivate it for another :extension_period days", array(":here" => HTML::anchor($river_base_url, 'here'), ":extension_period" => $extension_period)); ?>
			</p>	
		</article>
	</div>
</article>