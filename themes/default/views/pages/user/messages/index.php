<script type="text/javascript">
	$(function() {
		$('.messages.inbox tr').click(function(){
			window.location.href = '<?php echo $link_inbox; ?>/'+$(this).attr('id');
		});
		$('.messages.outbox tr').click(function(){
			window.location.href = '<?php echo $link_outbox; ?>/'+$(this).attr('id');
		});
	});
</script><?php if (count($inbox) > 0): ?>
<div id="content" class="messages inbox cf" align="center">
	<hgroup class="page-title bucket-title cf">
		<a href="<?php echo $link_inbox; ?>"><h1>Inbox</h1></a>
		<h2 class="discussion">
			<a href="<?php echo $link_inbox; ?>">
				<span class="icon"></span>
				View More
			</a>
		</h2>
		<h2 class="add">
			<a href="<?php echo $link_create; ?>">
				<span class="icon"></span>
				Write new message
			</a>
		</h2>
	</hgroup>
	<table>
		<tbody><?php foreach ($inbox as $m): ?>
			<tr id="<?php echo $m->id; ?>" class="<?php echo $m->read ? 'read' : 'unread'; ?>">
				<td width="15%" align="left"><?php echo $m->sender->name; ?></td>
				<td width="*" align="left">
					<span class="subject"><?php echo $m->subject; ?></span>
					<span class="details"> - <?php echo Text::limit_chars($m->message, 200, '...', TRUE); ?></span>
				</td>
				<td width="110px" align="right"><?php echo $m->relative_time(); ?></td>
			</tr><?php endforeach; ?>
		</tbody>
	</table>
</div><?php else: ?>
<article class="container base">
	<div class="alert-message blue">
		<p>
			<strong>Empty inbox</strong>
			There are no messages in your inbox. 
			<a href="<?php echo $link_create; ?>">Write new message</a>
		</p>
	</div>
</article><?php endif; ?><?php if (count($outbox) > 0): ?>
<div id="content" class="messages outbox cf" align="center">
	<hgroup class="page-title bucket-title cf">
		<h1>Outbox</h1>
		<h2 class="discussion">
			<a href="<?php echo $link_outbox; ?>">
				<span class="icon"></span>
				View More
			</a>
		</h2>
	</hgroup>
	<table>
		<tbody><?php foreach ($outbox as $m): ?>
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
</div><?php endif; ?>
