<article class="river" id="system_notification">
	<div class="center cf">
		<article class="container base alert-message red">
			<p>
				<?php echo __("Your river has already expired and is no longer receiving drops from your channels."); ?>
				<?php echo __("Click "); ?><a href="<?php echo $river_base_url."/extend?token=".$lifetime_extension_token ?>">here</a>
				<?php echo __(" to extend the lifetime of your river by another :extension_period days", 
				    array(":extension_period" => $extension_period)); ?>
			</p>	
		</article>
	</div>
</article>