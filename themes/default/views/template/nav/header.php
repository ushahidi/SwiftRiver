<ul class="toolbar-menu">
	<?php if ($user AND $user->account->loaded()): ?>
	<li class="search">
		<a href="<?php echo URL::site('search/main'); ?>" class="modal-trigger" title="<?php echo __("Search"); ?>">
			<span class="icon"></span>
			<span class="label"><?php echo __("Search"); ?></span>
		</a>
	</li>
	<?php endif; ?>
	<!-- hide parts of the header menu in the login page and for non registered users -->
	<?php if ($user AND ! $anonymous): ?>
		<?php if ($user->account->loaded()): ?>
			<li class="create">
				<a href="<?php echo URL::site().$account->account_path.'/create'; ?>" class="modal-trigger" title="<?php echo __("Create"); ?>">
					<span class="icon"></span>
					<span class="label"><?php echo __("Create"); ?></span>
				</a>
			</li>
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
					<li>
						<a href="<?php echo URL::site().$user->account->account_path; ?>">
							<?php echo __('Dashboard');?>
						</a>
					</li>
					<li>
						<a href="<?php echo URL::site().$account->account_path.'/rivers'; ?>">
							<?php echo __("Rivers"); ?>
						</a>
					</li>
					<li>
						<a href="<?php echo URL::site().$account->account_path.'/buckets'; ?>">
							<?php echo __("Buckets"); ?>
						</a>
					</li>
					<li class="group">
						<a href="<?php echo URL::site().$account->account_path.'/settings'; ?>">
							<?php echo __("Account settings"); ?>
						</a>
					</li>
					<?php if ($admin): ?>
					<li>
						<a href="<?php echo URL::site().'settings/main'; ?>">
							<?php echo __("Website Settings"); ?>
						</a>
					</li>
					<?php endif; ?>
					<li>
						<a href="<?php echo URL::site().'login/done'; ?>">
							<em><?php echo __('Log out');?></em>
						</a>
					</li>
				</ul>
			</li>
		<?php endif; ?>
	<?php elseif ($controller != 'login'): ?>
	<li class="login">
		<a href="<?php echo URL::site('login'); ?>" class="modal-trigger">
			<span class="label"><?php echo __("Log in"); ?></span>
		</a>
	</li>
	<?php endif; ?>
</ul>