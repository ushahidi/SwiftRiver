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
		?>	
		<tr <?php if ($i == 0) { echo 'class="bg"'; } ?>>
			<td>
			<?php
			if ($item_title)
			{
				?><h4><?php echo $item_title; ?></h4>><?php
			}
			?>
			<p>
				<span class="item-content"><?php echo $item_content; ?></span>
				<span class="item-meta">From <code><?php echo $service; ?></code> by <code><?php echo $item_author . '('.$item_author_count.')'; ?></code></span>
			</p>
			<p class="item-functions"><a href="#" class="ico-info">Info</a>&nbsp;&nbsp;<a href="#" class="ico-edit">Edit</a>&nbsp;&nbsp;<a href="#" class="ico-delete">Delete</a></p>
			</td>
			<td>XXXX</td>
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