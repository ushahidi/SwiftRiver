<?php echo $activity_stream ?>

<aside>
	<div class="item">
		<h2><?php echo __('This week'); ?></h2>
		<ul class="stats">
			<li>0 <?php echo __('new followers'); ?></li>
			<li>0 <?php echo __('new subscribers'); ?></li>
		</ul>
	</div>
	
	<div class="item cf">
		<h2><?php echo __('Following'); ?> <span><?php echo count($following); ?></span></h2>
		<ul class="relationships">
			<?php
			foreach ($following as $follow)
			{
				?><li><a href="<?php echo URL::site().'user/'.$follow->account->account_path ?>"><img src="<?php echo Swiftriver_Users::gravatar($follow->email); ?>" /></a></li><?php
			}
			?>
		</ul>
	</div>
	
	<div class="item">
		<h2><?php echo __('Followers'); ?> <span><?php echo count($followers); ?></span></h2>
		<ul class="relationships">
			<?php
			foreach ($followers as $follow)
			{
				?><li><a href="<?php echo URL::site().'user/'.$follow->account->account_path ?>"><img src="<?php echo Swiftriver_Users::gravatar($follow->email); ?>" /></a></li><?php
			}
			?>
		</ul>
	</div>																					
</aside>