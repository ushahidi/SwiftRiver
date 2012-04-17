<div id="content" class="cf">
	<div class="center">
		<div class="col_12">
		<?php if ($rivers): ?>
			<?php foreach ($rivers as $river): ?>
				<article class="container base">
					<header class="cf">
						<div class="actions"></div>
						<div class="property-title">
							<h1>
								<a href="<?php echo URL::site().$river['account_path'].'/river/'.$river['river_name_url']; ?>">
									<?php echo $river['account_path'].'/'.$river['river_name']; ?>
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
						<strong><?php echo __("No rivers"); ?></strong>
						<?php 
						echo __('The search for ":search_term" did not return any rivers', 
						    array(':search_term' => $search_term)); 
						?>
					</p>
				</div>
			</article>
		<?php endif; ?>

		</div>
	</div>
</div>