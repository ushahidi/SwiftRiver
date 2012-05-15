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
						<h1>
							<a href="<?php echo URL::site().$user['account_path']; ?>">
								<?php echo $user['name']; ?>
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