<ul class="toolbar-menu">
	<li class="search"><a href="/modal-search.php" class="modal-trigger"><span class="icon"></span><span class="label">Search</span></a></li>
	<li class="create"><a href="/modal-create.php" class="modal-trigger"><span class="icon"></span><span class="label">Create</span></a></li>
	<?php if ($user AND ! $anonymous): ?>
	<li class="user popover">
		<a href="#" class="popover-trigger">
			<span class="dropdown-arrow"></span>
			<span class="avatar-wrap">
				<?php if ($num_notifications): ?>
				<span class="notification"><?php echo $num_notifications; ?></span>
				<?php endif ?>
				<img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" />
			</span>
			<span class="nodisplay"><?php echo $user->name; ?></span>
		</a>
		<ul class="popover-window base header-toolbar">
			<li><a href="#">Rivers</a></li>
			<li><a href="#">Buckets</a></li>
			<li class="group"><a href="<?php echo URL::site().$user->account->account_path; ?>"><?php echo __('Profile');?></a></li>
			<li><a href="#">Account settings</a></li>
			<li><a href="<?php echo URL::site().'login/done'; ?>"><em><?php echo __('Log out');?></em></a></li>
		</ul>
	</li>
	<?php else: ?>			
	<?php echo Form::open('login'); ?>
	<li class="user">
		<a href="#" class="modal-trigger"><span class="label"><?php echo __('Log in');?></span></a>
	<?php endif; ?>
	</li>	
</ul>

<script type="text/template" id="header-bucket-template">
<a href="<%= bucket_url %>"><%= bucket_name %></a>
</script> 