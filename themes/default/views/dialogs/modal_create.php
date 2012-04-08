<article class="modal">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1><?php echo __("+ Create"); ?></h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon"></span>
					<?php echo __("Close"); ?>
				</a>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body link-list">
		<ul>
			<li>
				<a href="<?php echo URL::site().$account->account_path.'/river/create'; ?>">
					<?php echo __("River"); ?>
				</a>
			</li>
			<li>
				<a href="<?php echo URL::site().$account->account_path.'/bucket/create'; ?>">
					<?php echo __("Bucket"); ?>
				</a>
			</li>
			<li><a href="#"><?php echo __("Drop"); ?></a></li>
		</ul>
	</div>
</article>