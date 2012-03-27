<hgroup class="user-title dashboard cf">
	<div class="center">
		<div class="user-summary col_9">
			<a class="avatar-wrap">
				<img src="<?php echo Swiftriver_Users::gravatar($account->user->email, 156); ?>" />
			</a>
			<h1><?php echo $account->user->name; ?></h1>
			<h2><?php echo $account->user->username; ?></h2>
		</div>
		<div class="follow-summary col_3">
			<p class="follow-count">
				<a href="#"><strong><?php echo count($followers); ?></strong> <?php echo __("following"); ?></a>, 
				<a href="#"><strong><?php echo count($following); ?></strong> <?php echo __("following"); ?></a>
			</p>
			<?php if ( ! $owner AND ! $anonymous): ?>
			<p class="button-score button-white follow">
				<a href="#" title="<?php echo __("Now Following"); ?>">
					<span class="icon"></span>
					<?php echo __("Follow"); ?>
				</a>
			</p>
			<?php endif;?>
		</div>
	</div>
</hgroup>

<?php if ($owner AND ! empty($active)): ?>
<nav class="page-navigation cf">
	<ul class="center">
		<li <?php if ($active == 'main') echo 'class="active"'; ?>>
			<a href="<?php echo URL::site().$account->account_path; ?>"><?php echo __("Dashboard"); ?></a>
		</li>
		<li <?php if ($active == 'settings') echo 'class="active"'; ?>>
			<a href="<?php echo URL::site().$account->account_path.'/settings'; ?>"><?php echo __("Account Settings"); ?></a>
		</li>

	</ul>
</nav>
<?php endif; ?>

<div id="content" class="user dashboard cf">
	<div class="center">
		<?php echo $sub_content; ?>
	</div>
</div>