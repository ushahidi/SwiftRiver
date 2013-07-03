<ul class="global-menu">
	<li class="home"><a href="<?php echo $dashboard_url; ?>"><span class="icon-home"></span></a></li>
	<li class=""><a href="<?php echo URL::site('search'); ?>"><span class="icon-search"></span></a></li>
</ul>
</div>

<div class="col_12">
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
						<img src="<?php echo Swiftriver_Users::gravatar($user['owner']['email'], 80); ?>" />
					</span>
					<span class="nodisplay">Account Name</span>
				</a>
				<ul class="popover-window base header-toolbar">
					<li>
						<a href="<?php echo URL::site($user['account_path']); ?>">
							<?php echo __('Your Activity');?><?php if ($num_notifications) echo ' ('.$num_notifications.')'; ?>
						</a>
					</li>
					<li class="group">
						<?php echo HTML::anchor(URL::site($user['account_path'].'/settings', TRUE), __('Account Settings')); ?>
					</li>
					<?php if ($admin): ?>
						<li>
							<a href="<?php echo URL::site('settings/main', TRUE); ?>">
								<?php echo __("Website Settings"); ?>
							</a>
						</li>
					<?php endif; ?>
					<li>
						<a href="<?php echo URL::site('login/done'); ?>">
							<em><?php echo __('Log Out');?></em>
						</a>
					</li>
				</ul>
			</li>
		<?php endif; ?>
	</ul>
</div>


<?php if ($user): ?>
	<script type="text/template" id="header-rivers-modal-template">
		<div class="modal-title cf">
			<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
			<h1><i class="icon-river"></i>Rivers</h1>
		</div>

		<div class="modal-body">
			<div class="base">
				<h2 class="label own-title" style="display:none"><?php echo __("Managing"); ?></h2>
				<ul class="view-table own">
					<li class="add"><a href="#create-river" class="modal-transition"><?php echo __("Create a new river"); ?></a></li>
				</ul>
			</div>
			<div class="base">
				<h2 class="label collaborating-title" style="display:none"><?php echo __("Collaborating"); ?></h2>
				<ul class="view-table collaborating">
				</ul>
			</div>
			<div class="base">
				<h2 class="label following-title" style="display:none"><?php echo __("Following"); ?></h2>
				<ul class="view-table following">
				</ul>
			</div>
		</div>
	</script>

	<script type="text/template" id="create-river-modal-template">
		<div class="modal-title cf">
			<% if(closable) { %>
			<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
			<% } else { %>
			<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
			<% } %>
			<h1><?php echo __("Create a new river"); ?></h1>
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

	<script type="text/template" id="header-buckets-modal-template">
		<div class="modal-title cf">
			<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
			<h1><i class="icon-bucket"></i>Buckets</h1>
		</div>
	
		<div class="modal-body">
			<div class="base">
				<h2 class="label own-title" style="display:none"><?php echo __("Managing"); ?></h2>
				<ul class="view-table own">
					<li class="add"><a href="#create-bucket" class="modal-transition"><?php echo __("Create a new bucket"); ?></a></li>
				</ul>
			</div>
			<div class="base">
				<h2 class="label collaborating-title" style="display:none"><?php echo __("Collaborating"); ?></h2>
				<ul class="view-table collaborating">
				</ul>
			</div>
			<div class="base">
				<h2 class="label following-title" style="display:none"><?php echo __("Following"); ?></h2>
				<ul class="view-table following">
				</ul>
			</div>
		</div>
	</script>

	<script type="text/template" id="create-bucket-modal-template">
		<div class="modal-title cf">
			<% if(closable) { %>
			<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
			<% } else { %>
			<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
			<% } %>
			<h1><?php echo __("Create a new bucket"); ?></h1>
		</div>
	
		<div class="modal-body">
			<?php echo Form::open(); ?>
			<div class="base">
				<div class="modal-field">
					<h3 class="label"><?php echo __("Name"); ?></h3>
					<?php echo Form::input('bucket_name', '', array('placeholder' => __("Name your new bucket"), 'id' => 'bucket_name')); ?>
				</div>
			</div>
			<div class="modal-toolbar">
				<a href="#" class="button-submit button-primary modal-close"><?php echo __("Create bucket"); ?></a>
			</div>
			<?php echo Form::close(); ?>
		</div>
	</script>

	<script type="text/template" id="header-asset-template">
		<a href="<%= url %>"><%= display_name %></a>
	</script>
<?php endif; ?>

<script type="text/template" id="confirm-window-template">
	<div class="modal-title cf">
		<a href="#" class="modal-close button-white">
			<i class="icon-cancel"></i>
			<?php echo __("Close"); ?>
		</a>
		<h2><%= message %></h2>
	</div>

	<div class="modal-body">
		<div class="settings-category-toolbar">
			<a href="#" class="button-submit button-primary modal-close"><?php echo __("Yes"); ?></a>
			<a href="#" class="button-destruct button-secondary modal-close"><?php echo __("Nope, nevermind"); ?></a>
		</div>
	</div>
</script>