<div class="activity_feed">
	<?php
	if ($actions):
		foreach ($actions as $action)
		{?>
			<article class="droplet cf">
				<div class="summary">
					<section class="source twitter">
						<a href="/user"><img src="/images/content/avatar1.gif" /></a>
					</section>
					<section class="content">
						<div class="title">
							<p class="date">2:35 p.m., July 4</p>
							<h1>Adam Tinworth <span><a href="/droplet">added a reply to your droplet</a></span></h1>
						</div>
						<div class="body">
							<p>OK, the Ushahidi section of this afternoon's #likeminds post should now be more link rich and comprehensible: <a href="#">t.co/D2lk9lRg</a></p>
						</div>
					</section>
				</div>
			</article>
		<?php } ?>
		<div class="page_buttons">
			<p class="button_view"><a href="/droplet"><?php echo __('View more'); ?></a></p>
		</div>
	<?php else:?>
		<div class="list_stream">
			<ul>
				<li><?php echo __('Nothing to Display Yet'); ?></li>
			</ul>
		</div>
	<?php endif ?>
</div>

<aside>
	<div class="item cf">
		<h2><?php echo __('This week'); ?></h2>
		<ul>
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
				?><li><a href="#"><img src="<?php echo Swiftriver_Users::gravatar($follow['email']); ?>" /></a></li><?php
			}
			?>
		</ul>
	</div>
	
	<div class="item cf">
		<h2><?php echo __('Followers'); ?> <span><?php echo count($followers); ?></span></h2>
		<ul class="relationships">
			<?php
			foreach ($followers as $follow)
			{
				?><li><a href="#"><img src="<?php echo Swiftriver_Users::gravatar($follow['email']); ?>" /></a></li><?php
			}
			?>
		</ul>
	</div>																					
</aside>