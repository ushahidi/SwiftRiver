<div class="container list select data">


	<?php if (count($buckets)): ?>
	<div class="controls edit_advanced">
		<div class="row cf">
			<p class="button_go edit_single"><a href="#">Edit map</a></p>
			<p class="button_view edit_multiple"><a href="<?php echo URL::site()?>dashboard/edit_multiple">Edit multiple</a></p>
			<p class="button_view"><a href="<?php echo URL::site()?>dashboard/filter_rivers">Filter</a></p>
			<p class="button_go create_new"><a href="<?php echo URL::site().'bucket/new'; ?>"><?php echo __('Create new');?></a></p>
		</div>
	</div>

	<?php foreach ($buckets as $bucket)	{
	?>
	<article class="item cf" id="item_<?php echo $bucket->id; ?>">
		<div class="content">
			<div class="checkbox"><input type="checkbox" /></div>
			<h1><a href="<?php echo URL::site().'bucket/index/'.$bucket->id; ?>" class="title"><?php echo $bucket->bucket_name; ?></a></h1>
		</div>
		<div class="summary">
			<section class="actions">
				<div class="button">
					<p class="button_change"><a class="delete" onclick=""><span class="icon"></span><span class="nodisplay"><?php echo __('Delete Bucket'); ?></span></a></p>
					<div class="clear"></div>
					<div class="dropdown container">
						<p><?php echo __('Are you sure you want to delete this Bucket?'); ?></p>
						<ul>
							<li class="confirm"><a onclick="deleteItem(<?php echo $bucket->id; ?>,'bucket')"><?php echo __('Yep.'); ?></a></li>
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
	<h2 class="null"><?php echo __('No Buckets to display yet'); ?> <em><a href="<?php echo URL::site().'bucket/new'; ?>"><?php echo __('Create one.');?></a></em></h2>
<?php endif; ?>
</div>