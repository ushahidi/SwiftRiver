<hgroup class="user-title <?php if ($owner) { echo 'dashboard'; }  ?> cf">
	<div class="center">
		<div class="user-summary col_9">
			<a class="avatar-wrap" href="<?php echo URL::site().$account->account_path; ?>">
				<img src="<?php echo Swiftriver_Users::gravatar($account->user->email, 131); ?>" class="avatar"/>
			</a>
			<h1><?php echo $account->user->name; ?></h1>
			<h2 class="label"><?php echo $account->account_path; ?></h2>
		</div>
		<div id="follow_section" class="follow-summary col_3">
			<p class="follow-count">
				<a id="follower_count" href="<?php echo URL::site().$account->account_path.'/followers'; ?>">
					<strong><?php echo count($followers); ?></strong> <?php echo __("followers"); ?>
				</a>, 
				<a id="following_count" href="<?php echo URL::site().$account->account_path.'/following'; ?>">
					<strong><?php echo count($following); ?></strong> <?php echo __("following"); ?>
				</a>
			</p>
			
			<div id="follow_button">
			</div>
			
			<?php if ( ! $owner AND ! $anonymous) {
				echo $follow_button;
			} ?>
		</div>
	</div>
</hgroup>

<?php if ($owner AND ! empty($active)): ?>
<nav class="page-navigation cf">
	<ul class="center">
		<?php foreach ($nav as $item): ?>
		<li id="<?php echo $item['id']; ?>" class="<?php echo $item['id'] == $active ? 'active' : ''; ?>">
			<a href="<?php echo URL::site($account->account_path.$item['url']) ?>">
				<?php echo $item['label'];?>
			</a>
		</li>
		<?php endforeach; ?>
	</ul>
</nav>
<?php endif; ?>

<div id="content" class="user <?php echo $view_type ;?> cf">
	<div class="center">
		<?php echo $sub_content; ?>
	</div>
</div>