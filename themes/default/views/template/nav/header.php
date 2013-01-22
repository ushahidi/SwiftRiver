<ul class="global-menu">
	<li class="home"><a href="/"><span class="icon-home"></span></a></li>
	<li class="search"><a href="/markup/modal-search.php" class="modal-trigger"><span class="icon-search"></span></a></li>
</ul>
</div>

<div class="col_8">
	<ul class="user-menu">	
		<!-- hide parts of the header menu in the login page and for non registered users -->
		<?php if ($user AND ! $anonymous): ?>
				<li class="rivers"><a href="#"><span class="icon-river"></span><span class="label"><?php echo __("Rivers"); ?></span></a></li>
				<li class="bucket"><a href="#"><span class="icon-bucket"></span><span class="label"><?php echo __("Buckets"); ?></span></a></li>
				<li class="user popover">
					<a href="#" class="popover-trigger">
						<span class="icon-arrow-down"></span>
						<span class="avatar-wrap">
							<?php if ($num_notifications): ?>
								<span class="notification"><?php echo $num_notifications; ?></span>
							<?php endif ?>
							<img src="<?php echo Swiftriver_Users::gravatar('', 80); ?>" />
						</span>
						<span class="nodisplay">Account Name</span>
					</a>
					<ul class="popover-window base header-toolbar">
						<li>
							<a href="#">
								<?php echo __('Your Activity');?><?php if ($num_notifications) echo ' ('.$num_notifications.')'; ?>
							</a>
						</li>
						<li class="group">
							<a href="#">
								<?php echo __("Account Settings"); ?>
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
								<em><?php echo __('Log Out');?></em>
							</a>
						</li>
					</ul>
				</li>
		<?php elseif ($controller != 'login'): ?>
			<li class="login">
				<a href="<?php echo URL::site('login'); ?>" class="modal-trigger">
					<span class="icon-login"></span><span class="label">Log in</span>
				</a>
			</li>
		<?php endif; ?>
	</ul>
</div>

<?php if ($user): ?>
<script type="text/template" id="header-rivers-modal-template">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1><span class="icon-river"></span>Rivers</h1>
		</div>
		<div class="page-action col_3">
			<h2 class="close">
				<span class="button-white"><a href="#"><i class="icon-cancel"></i>Close</a></span>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body link-list" style="display:none">
		<p class="category own-title" style="display:none">Your rivers</p>
		<ul class="own">
		</ul>

		<p class="category collaborating-title" style="display:none">Rivers you collaborate on</p>
		<ul class="collaborating">
		</ul>

		<p class="category following-title" style="display:none">Rivers you follow</p>
		<ul class="following">
		</ul>
	</div>

	<div class="modal-body create-new">
		<p class="button-blue"><a href="#">Create a new river</a></p>
	</div>
</script>

<script type="text/template" id="header-buckets-modal-template">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1><span class="icon-bucket"></span>Buckets</h1>
		</div>
		<div class="page-action col_3">
			<h2 class="close">
				<span class="button-white"><a href="#"><i class="icon-cancel"></i>Close</a></span>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body link-list" style="display:none">
		<p class="category own-title" style="display:none">Your buckets</p>
		<ul class="own">
		</ul>

		<p class="category collaborating-title" style="display:none">Buckets you collaborate on</p>
		<ul class="collaborating">
		</ul>

		<p class="category following-title" style="display:none">Buckets you follow</p>
		<ul class="following">
		</ul>
	</div>

	<div class="modal-body create-new">
		<form>
			<h3>Create a new bucket</h3>
			<div class="field">
				<input type="text" placeholder="Name your new bucket" class="name" name="new_bucket" />
			</div>
			<div class="save-toolbar">
				<p class="button-blue"><a href="#">Save</a></p>
				<p class="button-blank cancel"><a href="#">Cancel</a></p>
			</div>			
		</form>
	</div>
</script>

<script type="text/template" id="header-asset-template">
	<span class="button-actions"><a href="#"><span class="icon-remove"></span></a></span><a href="<%= url %>"><%= display_name %></a>
</script>
<?php endif; ?>

<script type="text/template" id="confirm-window-template">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1><%= message %></h1>
		</div>
		<div class="page-action col_3">
			<h2 class="close">
				<span class="button-white"><a href="#"><i class="icon-cancel"></i>Close</a></span>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body">
		<div class="settings-toolbar">
			<p class="button-blue"><a href="#">Yes</a></p>
			<p class="button-blank close"><a href="#">Nope, nevermind</a></p>
		</div>
	</div>
</script>
