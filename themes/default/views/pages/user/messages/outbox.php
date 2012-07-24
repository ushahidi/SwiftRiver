<script type="text/javascript">
	$(function() {
		$('.messages tr').click(function(){
			window.location.href = '<?php echo $link_outbox; ?>/'+$(this).attr('id');
		});
	});
</script>
<div id="content" class="messages outbox cf" align="center">
	<hgroup class="page-title bucket-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1>Outbox</h1>
			</div>
			<div class="page-actions col_3">
				<h2 class="discussion">
					<a href="<?php echo $link_inbox; ?>">
						<span class="icon"></span>
						Inbox
					</a>
				</h2>
			</div>
		</div>
	</hgroup>
	<table>
		<tbody><?php foreach ($messages as $m): ?>
			<tr id="<?php echo $m->id; ?>" class="read">
				<td width="15%" align="left"><?php echo $m->recipient->name; ?></td>
				<td width="*" align="left">
					<span class="subject"><?php echo $m->subject; ?></span>
					<span class="details"> - <?php echo Text::limit_chars($m->message, 200, '...', TRUE); ?></span>
				</td>
				<td width="110px" align="right"><?php echo $m->relative_time(); ?></td>
			</tr><?php endforeach; ?>
		</tbody>
	</table>
</div>
