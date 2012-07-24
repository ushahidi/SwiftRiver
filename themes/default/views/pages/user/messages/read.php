<div id="content" class="messages message cf" align="center">
	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1><?php echo $message->subject ?></h1>
			</div>
			<div class="page-actions col_3">
				<h2 class="back">
					<a href="<?php echo $link_back; ?>">
						<span class="icon"></span>
						Back to <?php echo $location; ?>
					</a>
				</h2>
			</div>
		</div>
	</hgroup>
	<div class="message-content">
		<p class="meta">
			<a href="<?php echo URL::site($message->sender->username); ?>" title="View <?php echo $message->sender->name; ?>'s Profile">
				<img src="<?php echo Swiftriver_Users::gravatar($message->sender->email, 40) ?>" />
				<span><?php echo $message->sender->name; ?></span>
			</a> to 
			<a href="<?php echo URL::site($message->recipient->username); ?>" title="View <?php echo $message->recipient->name; ?>'s Profile">
				<img src="<?php echo Swiftriver_Users::gravatar($message->recipient->email, 40) ?>" />
				<span><?php echo $message->recipient->name; ?></span>
			</a>
		</p>
		<p class="timestamp">
			<?php echo date('F jS, Y, h:m A', strtotime($message->timestamp)); ?> (<?php echo $message->relative_time(); ?>)
		</p>
		<p class="message-body">
			<?php echo nl2br($message->message); ?>
		</p><?php if ($location == 'Inbox'): ?>
		<p class="button-reply button-blue">
			<a href="<?php echo $link_create; ?>?r=<?php echo urlencode($message->sender->username); ?>&s=<?php echo urlencode('Re: '.$message->subject); ?>">Reply</a>
		</p><?php endif; ?>
	</div>
</div>

<!--<a href="account-path-URL::site(path)" class="avatar-wrap" title="username">
	<img src="collaboratoravatar" />
</a>-->
