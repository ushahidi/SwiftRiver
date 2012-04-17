<div id="content" class="cf">
	<div class="center">
		<div class="col_12">
		<?php if ($buckets): ?>
			<?php foreach ($buckets as $bucket): ?>
			<article class="container base">
				<header class="cf">
					<div class="actions">
					</div>
					<div class="property-title">
						<h1>
							<a href="<?php echo URL::site().$bucket['account_path'].'/bucket/'.$bucket['bucket_name_url']; ?>">
								<?php echo $bucket['account_path'].'/'.$bucket['bucket_name']; ?>
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
						<strong><?php echo __("No buckets"); ?></strong>
						<?php echo __("The search for \":search_term\" did not return any buckets", array(':search_term' => $search_term)); ?>
					</p>
				</div>
			</article>
		<?php endif; ?>
		</div>
	</div>
</div>