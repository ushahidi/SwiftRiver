<div class="no-content base" style="display: none;">
	<div class="col_9">
		<div class="alert-message blue">
			<p>
				<strong><?php echo __('No results found.') ?></strong>
				<?php 
				echo __('Your search for ":search_term" did not return any results', 
					array(':search_term' => $search_term)); 
				?>
			</p>
		</div>
	</div>
</div>
