<div class="feed">
	<?php
	if ($actions):
		foreach ($actions as $action)
		{?>
			<article class="item">
				<div class="summary cf">
					<section class="source twitter">
						<a href="/user"><img src="/images/content/avatar1.gif" /></a>
					</section>
					<div class="content">
						<hgroup>
							<p class="date">2:35 p.m., July 4</p>
							<h1>Adam Tinworth <span><a href="/droplet">added a reply to your droplet</a></span></h1>
						</hgroup>
						<div class="body">
							<p>OK, the Ushahidi section of this afternoon's #likeminds post should now be more link rich and comprehensible: <a href="#">t.co/D2lk9lRg</a></p>
						</div>
					</div>
				</div>
			</article>
		<?php } ?>
		<div class="page_buttons">
			<p class="button_view"><a href="/droplet"><?php echo __('View more'); ?></a></p>
		</div>
	<?php else:?>
		<h2 class="null"><?php echo __('Nothing to display yet.'); ?></h2>
	<?php endif ?>
</div>

<aside>
	<div class="item">
		<h2><?php echo __('This week'); ?></h2>
		<ul class="stats">
			<li>0 <?php echo __('new followers'); ?></li>
			<li>0 <?php echo __('new subscribers'); ?></li>
		</ul>
	</div>
	
	<div class="item">
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
	
	<div class="item">
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