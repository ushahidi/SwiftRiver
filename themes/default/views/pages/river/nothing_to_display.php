<div class="no-content base" style="display: none;">
		<div class="col_9">
			<div class="alert-message blue">
				<p>
					<strong><?php echo __('Nothing to display yet.') ?></strong>
					<?php echo __('The river will start flowing as soon as there is content '
					    .'and this page will update automatically or you can refresh manually', 
					    array('refresh manually' => HTML::anchor($river_url, __('refresh manually'))));
					?>
				</p>
				<p>
					<?php echo __('Taking too long? Save this link and come back later') ?>
					<?php echo HTML::anchor($river_url, $river_url); ?>
				</p>
			</div>
		</div>
</div>