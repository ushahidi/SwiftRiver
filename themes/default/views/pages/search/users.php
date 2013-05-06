<div id="settings" class="col_9">
	<?php if ($users): ?>
	<article class="base settings-category">
		<ul class="view-table">
		<?php foreach ($users as $user): ?>
			<li class="user cf">
				<a href="<?php echo $user['account_path']; ?>">
					<?php echo HTML::image($user['owner']['avatar'], array('class' => 'avatar')); ?>
					<?php echo $user['account_path']; ?>
				</a>
			</li>
		<?php endforeach; ?>
		</ul>
	</article>
	<?php else: ?>
		<article class="stream-message" style="display: block;">
			<p>
				<strong><?php echo __("No users found."); ?></strong>
				<?php 
				echo __("The search for ':search_term' did not return any users", 
				    array(':search_term' => $search_term)); 
				?>
			</p>
		</article>
	<?php endif; ?>
</div>
