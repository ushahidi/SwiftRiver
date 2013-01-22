<hgroup class="page-title user-title cf">
	<div class="center">
		<div class="col_9">
			<a class="avatar-wrap" href="#">
				<img src="<?php echo Swiftriver_Users::gravatar($account['owner']['email'], 131); ?>" class="avatar"/>
			</a>
			<h1><?php echo $account['owner']['name']; ?></h1>
			<h2 class="label"><?php echo $account['account_path']; ?></h2>
		</div>
		<div class="page-action col_3">
			<span class="follow-total">
				<a href="#" class="modal-trigger"><strong><?php echo $account['follower_count']; ?></strong> followers</a>, 
				<a href="#"><strong><?php echo $account['following_count']; ?></strong> following</a>
			</span>
		</div>
	</div>
</hgroup>

<nav class="page-navigation cf">
	<div class="center">
		<ul class="col_12">
			<li class="active"><a href="#">Activity</a></li>
			<li><a href="#">Content</a></li>
		</ul>
	</div>
</nav>

<div id="content" class="cf">
	<div class="center">
		<?php echo $sub_content; ?>
	</div>
</div>