<div id="content" class="cf">
	<div class="center">
		<div class="col_12">
		<?php if ($users): ?>
			<?php foreach ($users as $user): ?>
			<article class="container base">
				<header class="cf">
					<div class="actions">
					</div>
					<div class="property-title">
						<a href="<?php echo URL::site().$user['account_path']; ?>" class="avatar-wrap">
							<img src="<?php echo $user['avatar']; ?>"/>
						</a>
						<h1>
							<a href="<?php echo URL::site().$user['account_path']; ?>">
								<?php echo $user['account_path']; ?> 
								<em>(<?php echo $user['name']; ?>)</em>
							</a>
						</h1>
					</div>
				</header>
			</article>
			<?php endforeach; ?>
		<?php else: ?>
			<article class="container base">
				<div class="alert-message blue">
					<p>
						<strong><?php echo __("No users"); ?></strong>
						<?php echo __("The search for \":search_term\" did not return any users", array(':search_term' => $search_term)); ?>
					</p>
				</div>
			</article>
		<?php endif; ?>
		</div>
	</div>
</div>