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
				<p style="font-size: 90%;">
					<label for="source_trust"><?php echo __('Trust Level'); ?></label>
					<p></p>
					<select name="source_trust" id="slider-<?php echo $item->id; ?>">
						<option value="0">0</option>
						<option value="1">1</option>
						<option value="2" selected="selected">2</option>
						<option value="3">3</option>
						<option value="4">4</option>
						<option value="5">5</option>
					</select>
				</p>
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
				<ul><?php
					foreach ($links as $link)
					{
						$link_text = Text::limit_chars($link->link_full, 30, '...');
						?><li><code class="blue"><a href="<?php echo $link->link_full; ?>" target="_blank"><?php echo $link_text; ?></a></code></li><?php
					}
				?></ul>
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
</div>

<div class="dialog-paging">
	<p class="pagination blue">
		<a href="<?php echo ($previous->loaded()) ? 'javascript:getInfo('.$previous->id.');' : '#'; ?>" class="number current">&laquo; <?php echo __('Previous'); ?></a>
		<a href="<?php echo ($next->loaded()) ? 'javascript:getInfo('.$next->id.');' : '#'; ?>" class="number current"><?php echo __('Next'); ?> &raquo;</a>
	</p><!-- .pagination -->
</div>