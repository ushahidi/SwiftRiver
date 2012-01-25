<div class="container list select data">
	<?php if (count($rivers)) : ?>
	<div class="controls edit-advanced">
		<div class="row cf">
			<p class="button-go edit-single"><a href="#">Edit map</a></p>
			<p class="button_view edit_multiple"><a href="<?php echo URL::site()?>dashboard/edit_multiple">Edit multiple</a></p>
			<p class="button_view"><a href="<?php echo URL::site()?>dashboard/filter_rivers">Filter</a></p>
			<p class="button-go create-new"><a href="<?php echo URL::site().'river/new'; ?>"><?php echo __('Create new');?></a></p>
		</div>
	</div>
	<?php foreach ($rivers as $river) {	
	?>
	<article class="item cf" id="item_<?php echo $river->id; ?>">
		<div class="content">
			<div class="checkbox"><input type="checkbox" /></div>
			<h1><a href="<?php echo URL::site().'river/index/'.$river->id; ?>" class="title"><?php echo $river->river_name; ?></a></h1>
		</div>
		<div class="summary">
			<section class="actions">
				<div class="button">
					<p class="button-change"><a class="delete" onclick=""><span class="icon"></span><span class="nodisplay"><?php echo __('Delete River'); ?></span></a></p>
					<div class="clear"></div>
					<div class="dropdown container">
						<p><?php echo __('Are you sure you want to delete this River?'); ?></p>
						<ul>
							<li class="confirm"><a onclick="deleteItem(<?php echo $river->id; ?>,'river')"><?php echo __('Yep.'); ?></a></li>
							<li class="cancel"><a onclick=""><?php echo __('No, nevermind.'); ?></a></li>
						</ul>
					</div>
				</div>
			</section>
			<section class="meta">
				<p><a href="#"><strong>4</strong> <?php echo __('subscribers'); ?></a></p>
			</section>
		</div>
	</article>

	<?php } 
	else:?>
	<h2 class="null"><?php echo __('No Rivers to display yet.'); ?> <em><a href="<?php echo URL::site().'river/new'; ?>"><?php echo __('Create one.');?></a></em></h2>
	<?php endif; ?>
</div>