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
						<li class="add"><a href="#create-river" class="modal-transition">Create a new river</a></li>
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

			<!-- START: Create new river -->				
			<div id="create-river" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Create a new river</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-field">
							<h3 class="label">Name</h3>
							<input type="text" placeholder="Name your new river" />
						</div>
					</div>

					<div class="base modal-tabs-container">
						<h2 class="label">Open channels</h2>
						<ul class="modal-tabs-menu">
							<li><a href="#add-twitter"><span class="channel-icon icon-twitter"></span></a></li>
							<li><a href="#add-facebook"><span class="channel-icon icon-facebook"></span></a></li>
							<li><a href="#add-rss"><span class="channel-icon icon-rss"></span></a></li>
							<li><a href="#add-email"><span class="channel-icon icon-mail"></span></a></li>
						</ul>
						<div class="modal-tabs-window">
							<div class="active"></div>
							
							<!-- ADD Twitter -->
							<div id="add-twitter">
								<div class="modal-field modal-field-tabs-container">
									<ul class="modal-field-tabs-menu">
										<li class="active"><a href="#input-keywords">Keywords</a></li>
										<li><a href="#input-users">Users</a></li>
										<li><a href="#input-location">Location</a></li>
									</ul>
									<div class="modal-field-tabs-window">
										<div id="input-keywords" class="active">
											<a href="#" class="add-field"><span class="icon-plus"></span></a>									
											<input type="text" placeholder="Enter keywords, separated by commas" />
										</div>
										<div id="input-users">
											<a href="#" class="add-field"><span class="icon-plus"></span></a>									
											<input type="text" placeholder="Enter usernames, separated by commas" />
										</div>
										<div id="input-location">
											<a href="#" class="add-field"><span class="icon-plus"></span></a>									
											<input type="text" placeholder="Enter location" />
											<select style="display:block;">
												<option>within 100km</option>
												<option>within 1000km</option>
											</select>
										</div>																				
									</div>
									
									<!-- IF: Parameter added
									<div class="modal-field-parameter">									
										<select style="display:block;">
											<option>AND</option>
											<option>OR</option>
										</select>
										
										<input type="text" value="SXSW" />
									</div>								
									-->
								</div>
							</div>

							<!-- ADD Facebook -->
							<div id="add-facebook">
								<div class="modal-field">
									<h3 class="label">Facebook Page name</h3>
									<a href="#" class="add-field"><span class="icon-plus"></span></a>
									<input type="text" placeholder="Enter the name of a Facebook page" />
									<!-- IF: Parameter added
									<div class="modal-field-parameter">									
										<select style="display:block;">
											<option>AND</option>
											<option>OR</option>
										</select>
										
										<input type="text" value="SXSW" />
									</div>								
									-->
								</div>
							</div>
							
							<!-- ADD RSS -->
							<div id="add-rss">
								<div class="modal-field">
									<h3 class="label">RSS URL</h3>
									<a href="#" class="add-field"><span class="icon-plus"></span></a>
									<input type="text" placeholder="Enter the address to an RSS feed" />
								</div>
							</div>

							<!-- ADD EMAIL -->
							<div id="add-email">
								<div class="modal-field">
									<h3 class="label">Email address</h3>
									<input type="text" placeholder="Enter your full email address" />
								</div>
								<div class="modal-field">
									<h3 class="label">Password</h3>
									<input type="password" />
								</div>								
							</div>																					
						</div>
					</div>
					
					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-close">Create river</a>				
					</div>					
				</div>
			</div>		
		
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
					<h1><?php echo __("Create a new bucket"); ?></h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-field">
							<h3 class="label">Name</h3>
							<?php echo Form::input('bucket_name', '', array('placeholder' => __("Name your new bucket"))); ?>
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
	<div id="modal-viewport">
		<div class="modal-view">
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
		</div>
	</div>
</script>

<script type="text/template" id="system-message-template">
	<div class="center">
		<a href="#" class="system-message-close">
			<span class="icon-cancel"></span>
		</a>
		<p><%= message %></p>
	</div>
</script>