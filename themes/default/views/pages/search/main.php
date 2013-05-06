<div id="content">
	<div class="center">
		<article class="modal" id="search-container">
			<div class="modal-body">
				<div class="base" align="center">
					<?php echo Form::open(URL::site('search'), array('method' => 'GET')); ?>
					<div class="modal-field">
						<h2>
							<i class="icon-search"></i>
							<?php echo __("Search for <strong>drops, rivers, buckets and users</strong>"); ?>
						</h2>
						<?php echo Form::input('q', ''); ?>
						<?php echo HTML::anchor("#", __("Search"), array('class' => 'button-primary selected', 'onClick'=>'submitForm(this);')); ?>
					</div>
					<?php echo Form::close(); ?>
				</div>
			</div>
		</article>
	</div>
</div>
