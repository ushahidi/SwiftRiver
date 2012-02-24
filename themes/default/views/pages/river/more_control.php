<div class="panel_body">
	<div class="controls">
		<div class="row">
			<ul class="views cf">
				<?php
				// Swiftriver Plugin Hook -- Hook into to the 'More' dropdown
				Swiftriver_Event::run('swiftriver.river.nav.more', $river);
				?>
			</ul>
		</div>
	</div>
</div>