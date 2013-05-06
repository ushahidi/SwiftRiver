<div class="col_9">
	<?php if ($buckets): ?>
	<div class="container base">
		<table>
		<?php foreach ($buckets as $bucket): ?>
			<tr>
				<td class="item-type"><span class="icon-bucket"></span></td>
				<td class="item-summary">
					<h2>
						<a href="<?php echo $bucket['url']; ?>">
							<?php echo $bucket['name']; ?>
						</a>
					</h2>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
	</div>
	<?php else: ?>
		<article class="stream-message" style="display: block;">
			<p>
				<strong><?php echo __("No buckets found."); ?></strong>
				<?php 
				echo __("The search for ':search_term' did not return any buckets", 
				    array(':search_term' => $search_term)); 
				?>
			</p>
		</article>
	<?php endif; ?>
</div>
