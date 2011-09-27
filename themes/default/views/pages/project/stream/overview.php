<?php
if (count($filter_tags) OR count($filter_locations) OR $filter_service OR $filter_author)
{
	?>
	<fieldset>
		<legend><?php echo __('Filters'); ?></legend>
		<?php
		foreach ($filter_tags as $tag)
		{
			$new_querystring = $querystring;
			foreach ($new_querystring['t'] as $key => $value)
			{
				if($value == $tag)
				{
					unset($new_querystring['t'][$key]);
				}
			}
			$new_url = URL::site().Request::current()->uri().
				'?'.http_build_query($new_querystring, NULL, '&');
			?><span class="label label-03"><a href="<?php echo $new_url; ?>">X</a>&nbsp;&nbsp;<?php echo $tag; ?></span>&nbsp;&nbsp;<?php
		}
		foreach ($filter_locations as $location)
		{
			$new_querystring = $querystring;
			foreach ($new_querystring['l'] as $key => $value)
			{
				if($value == $location)
				{
					unset($new_querystring['l'][$key]);
				}
			}
			$new_url = URL::site().Request::current()->uri().
				'?'.http_build_query($new_querystring, NULL, '&');
			?><span class="label label-02"><a href="<?php echo $new_url; ?>">X</a>&nbsp;&nbsp;<?php echo $location; ?></span>&nbsp;&nbsp;<?php
		}
		if ($filter_service)
		{
			$new_querystring = $querystring;
			if ($new_querystring['s'] == $filter_service)
			{
				unset($new_querystring['s']);
			}
			$new_url = URL::site().Request::current()->uri().
				'?'.http_build_query($new_querystring, NULL, '&');
			?><span class="label label-05"><a href="<?php echo $new_url; ?>">X</a>&nbsp;&nbsp;<?php echo $filter_service; ?></span>&nbsp;&nbsp;<?php
		}

		if ($filter_author)
		{
			$new_querystring = $querystring;
			if ($new_querystring['a'] == $filter_author)
			{
				unset($new_querystring['a']);
			}
			$new_url = URL::site().Request::current()->uri().
				'?'.http_build_query($new_querystring, NULL, '&');
			?><span class="label label-05"><a href="<?php echo $new_url; ?>">X</a>&nbsp;&nbsp;<?php echo $filter_author; ?></span>&nbsp;&nbsp;<?php
		}
		?>
	</fieldset>
	<?php
}?>
<table width="100%" class="nostyle">
	<?php
	if ($total == 0)
	{
		?>
		<tr>
			<td align="center"><a href="<?php echo URL::site('/project/').'/'.$project->id.'/builder'; ?>"><?php echo __('There are no items in your stream. Click here to add or modify your feeds.'); ?></span></td>
		</tr>	
		<?php
	}
		
	$i = 0;
	foreach ($items as $item)
	{
		$item = ORM::factory('item', $item->id);
		$service = $item->service;
		$item_title = $item->item_title;
		$item_content = Text::limit_chars($item->item_content, 150, '...');
		$item_author = $item->item_author;
			$source = $item->source;
			$item_author_count = $source->items->count_all();
		$item_date_pub = date('H:i M d, Y', strtotime($item->item_date_pub));
		$tag_count = $item->tags->count_all();
		$tags = $item->tags->find_all();
		$location_count = $item->locations->count_all();
		$locations = $item->locations->find_all();
		$discussions = $item->discussions->count_all();
		?>	
		<tr <?php if ($i == 0) { echo 'class="bg"'; } ?>>
			<td>
				<?php
				if ($item_title)
				{
					?><span class="item-content"><strong><?php echo $item_title; ?></strong></span><?php
				}
				?>
				<span class="item-content"><?php echo $item_content; ?></span>
				<p class="item-extras">
					<?php
					if ($tag_count)
					{
						?><span class="item-tags"><?php echo __('Tags');?>: <?php
						foreach ($tags as $tag)
						{
							?><code class="blue"><a href="<?php echo $current.'&t[]='.urlencode($tag->tag); ?>"><?php echo $tag->tag; ?></a></code>&nbsp;<?php
						}
						?></span><?php
					}
					if ($location_count)
					{
						?><span class="item-locations"><?php echo __('Locations');?>: <?php
						foreach ($locations as $location)
						{
							?><code class="green"><a href="<?php echo $current.'&l[]='.urlencode($location->location_name); ?>"><?php echo $location->location_name; ?></a></code>&nbsp;<?php
						}
						?></span><?php
					}					
					?>
					<span class="item-meta"><?php echo __('From');?> <code><a href="<?php echo $current.'&s='.urlencode($service); ?>"><?php echo $service; ?></a></code> <?php echo __('by');?> <code><a href="<?php echo $current.'&a='.urlencode($item_author); ?>"><?php echo $item_author . '('.$item_author_count.')'; ?></a></code></span>
				</p>
				<p class="item-functions">
					<a href="javascript:showInfo(<?php echo $item->id; ?>);" class="ico-edit">Edit</a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="#" class="ico-user-03"><?php echo __('Discussions');?></a>&nbsp;&nbsp;&nbsp;&nbsp;
					<a href="#" class="ico-delete"><?php echo __('Sweep');?></a>
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