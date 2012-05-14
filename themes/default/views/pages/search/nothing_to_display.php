<div class="center no-content" style="display:none;">
	<div class="col_12">
		<article class="container base">
			<div class="alert-message blue">
				<p>
					<strong><?php echo __('No results found.') ?></strong>
					<?php 
					echo __('Your search for ":search_term" did not return any :result_text', 
						array(':result_text' => $result_text, ':search_term' => $search_term));
					?>
				</p>
			</div>
		</article>
	</div>
</div>