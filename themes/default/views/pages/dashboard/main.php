<div class="feed">
	<?php
	if ($actions):
		foreach ($actions as $action)
		{?>
			<article class="item">
				<div class="summary cf">
					<section class="source twitter">
						<a href="/user"><img src="http://www.gravatar.com/avatar/cf5c8c89d5bb777f732144990fff0abe?s=80&d=mm&r=g" /></a>
					</section>
					<div class="content">
						<hgroup>
							<p class="date">2:35 p.m., July 4</p>
							<h1>Adam Tinworth <span><a href="/river">invited you to collaborate on a River</a></span></h1>
						</hgroup>
						<div class="body">
							<p>By accepting this invitation, you'll be able to view and edit the settings for the <a href="#">Popular news</a> river, along with <a href="#">Adam Tinworth</a>.</p>
						</div>
					</div>
					<section class="actions">
						<div class="button">
							<p class="button-change checkbox-options" onclick=""><a><span class="icon"></span></a></p>
							<div class="clear"></div>
							<ul class="dropdown">
								<li class="checkbox"><a onclick=""><span class="input"></span>Accept</a></li>
								<li class="checkbox"><a onclick=""><span class="input"></span>Ignore</a></li>
							</ul>
						</div>
					</section>
				</div>
			</article>
		<?php } ?>
		<div class="page_buttons">
			<p class="button-view"><a href="/droplet"><?php echo __('View more'); ?></a></p>
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