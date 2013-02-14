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

<script type="text/template" id="create-river-modal-template">
	<div class="modal-title cf">
		<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
		<h1>Create a new river</h1>
	</div>
				
	<div class="modal-body">
		<?php echo Form::open() ?>
			<div class="base">
				<div class="modal-field">
					<h3 class="label">Name</h3>
					<input type="text" placeholder="Give your new river a descriptive name" name="river_name" />
				</div>
				<div class="modal-field">
					<h3 class="label">Description (Optional)</h3>
					<input type="text" placeholder="What is the river about?" name="river_description" />
				</div>
				<div class="modal-field">
					<h3 class="label">Who can view the river?</h3>
					<select name="public">
						<option value="0">Just Me</option>
						<option value="1">Everyone</option>
					</select>
				
				</div>
			</div>
					
			<div class="modal-toolbar">
				<a href="#" class="button-submit button-primary"><span>Create the river</span></a>				
			</div>					
		<?php echo Form::close(); ?>
	</div>
</script>

<?php if ($user): ?>
<script type="text/template" id="header-rivers-modal-template">
<article class="modal">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1><i class="icon-river"></i>Rivers</h1>
			</div>
			
			<div class="modal-body">			
				<div class="base">
					<h2 class="label own-title" style="display:none">Managing</h2>
					<ul class="view-table own">
						<li class="add"><a href="#create-river">Create a new river</a></li>
					</ul>													
				</div>
				<div class="base">
					<h2 class="label following-title" style="display:none">Following</h2>
					<ul class="view-table following">
					</ul>													
				</div>
			</div>

		</div>
		<div id="modal-secondary" class="modal-view">	
		
		</div>
	</div>
</article>
</script>

<script type="text/template" id="header-buckets-modal-template">
<article class="modal">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1><i class="icon-bucket"></i>Buckets</h1>
			</div>
			
			<div class="modal-body">			
				<div class="base">
					<h2 class="label own-title" style="display:none">Managing</h2>
					<ul class="view-table own">
						<li class="add"><a href="#create-bucket" class="modal-transition">Create a new bucket</a></li>
					</ul>													
				</div>
				<div class="base">
					<h2 class="label following-title" style="display:none">Following</h2>
					<ul class="view-table following">
					</ul>													
				</div>
			</div>

		</div>
		<div id="modal-secondary" class="modal-view">

			<!-- START: Create new bucket -->				
			<div id="create-bucket" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Create a new bucket</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-field">
							<h3 class="label">Name</h3>
							<input type="text" placeholder="Name your new bucket" />
						</div>
					</div>
					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-close">Create bucket</a>				
					</div>					
				</div>
			</div>		
		
		</div>
	</div>
</article>
</script>

<script type="text/template" id="header-asset-template">
	<a href="<%= url %>"><%= display_name %></a>
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
