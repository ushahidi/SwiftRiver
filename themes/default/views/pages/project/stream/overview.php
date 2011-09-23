<table width="100%" class="nostyle">
	<?php
	$i = 0;
	foreach ($items as $item)
	{
		$service = $item->service;
		$item_title = $item->item_title;
		$item_content = Text::limit_chars($item->item_content, 150, '...');
		$item_author = $item->item_author;
			$source = $item->source;
			$item_author_count = $source->items->count_all();
		$item_date_pub = date('H:i M d, Y', strtotime($item->item_date_pub));
		$tag_count = $item->tags->count_all();
		$tags = $item->tags->find_all();
		?>	
		<tr <?php if ($i == 0) { echo 'class="bg"'; } ?>>
			<td>
				<?php
				if ($item_title)
				{
					?><h4><?php echo $item_title; ?></h4><?php
				}
				?>
				<span class="item-content"><?php echo $item_content; ?></span>
				<p class="item-extras">
					<?php
					if ($tag_count)
					{
						?><span class="item-tags">Tags: <?php
						foreach ($tags as $tag)
						{
							?><code class="blue"><?php echo $tag->tag; ?></code>&nbsp;<?php
						}
						?></span><?php
					}
					?>
					<span class="item-meta">From <code><?php echo $service; ?></code> by <code><?php echo $item_author . '('.$item_author_count.')'; ?></code></span>
				</p>
				<p class="item-functions">
					<a href="javascript:showInfo(<?php echo $item->id; ?>);" class="ico-edit">Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="#" class="ico-user-03">Discuss</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="#" class="ico-delete">Sweep</a>
				</p>
				<div id="dialog-<?php echo $item->id; ?>" class='sweeper-dialog' title="Edit Item"></div><script type="text/javascript">$(function() {$("#dialog-<?php echo $item->id; ?>").dialog({autoOpen:false,modal: true,width:720,height:530});});</script>
			</td>
		</tr>
		<?php
		if ($i == 1)
		{
			$i = 0;
		}
		else
		{
			$i++;
		}
	}
	?>
</table>

<?php echo $paging; ?>