<div class="col_9">
	<?php if ($rivers): ?>
	<div class="container base">
		<table>
		<?php foreach ($rivers as $river): ?>
			<tr>
				<td class="item-type"><span class="icon-river"></span></td>
				<td class="item-summary">
					<h2>
						<a href="<?php echo $river['url']; ?>">
							<?php echo $river['name']; ?>
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
				<strong><?php echo __("No rivers found."); ?></strong>
				<?php 
				echo __("The search for ':search_term' did not return any rivers", 
				    array(':search_term' => $search_term)); 
				?>
			</p>
		</article>
	<?php endif; ?>
</div>