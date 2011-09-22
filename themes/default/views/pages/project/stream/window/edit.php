<div id="content-dialog">

	<div class="col50">
		<?php
		if ($item->item_title)
		{
			?><h4><?php echo $item->item_title; ?></h4><?php
		}
		?>
		<span class="item-content"><?php echo $item->item_content; ?></span>
	</div> <!-- /col50 -->

	<div class="col50 f-right">
		<div id="accordion-<?php echo $item->id; ?>">
			<h3><a href="#"><?php echo __('Source'); ?></a></h3>
			<div>
				<?php echo __('Service'); ?>: <code class="blue"><?php echo $source->service; ?></code><br />
				<?php
				// If Username Available
				if ($source->source_username)
				{
					?><?php echo __('Username'); ?>: <code class="blue"><?php echo $source->source_username; ?></code><br /><?php
				}

				// If Name Available
				if ($source->source_name)
				{
					?><?php echo __('Name'); ?>: <code class="blue"><?php echo $source->source_name; ?></code><br /><?php
				}

				// If Description Available
				if ($source->source_description)
				{
					?><?php echo __('Description'); ?>: <code class="blue"><?php echo $source->source_description; ?></code><br /><?php
				}
				?>
			</div>
			<h3><a href="#"><?php echo __('Tags'); ?></a></h3>
			<div>
				<?php echo Form::open(); ?>
					<p>
						<?php echo Form::input("tag", '', array("class" => "input-text")); ?>
						<input type="submit" value="<?php echo __('Add Tag');?>" class="input-submit" />
					</p>
					<p><?php
						foreach ($tags as $tag)
						{
							?><code class="blue"><?php echo $tag->tag; ?>[<a href="#">x</a>]</code> <?php
						}
					?></p>
				<?php echo Form::close(); ?>
			</div>
			<h3><a href="#"><?php echo __('Links'); ?></a></h3>
			<div>
				<p>xxx</p>
			</div>
			<h3><a href="#"><?php echo __('Media'); ?></a></h3>
			<div>
				<p>xxx</p>
			</div>
			<h3><a href="#"><?php echo __('Locations'); ?></a></h3>
			<div>
				<p>xxx</p>
			</div>
		</div>
	</div> <!-- /col50 -->
	<div class="fix"></div>

	<p></p>
	<p class="pagination blue">
		<a href="#" class="number current">&laquo; <?php echo __('Previous'); ?></a>
		<a href="#" class="number current"><?php echo __('Next'); ?> &raquo;</a>
	</p><!-- .pagination -->

</div>