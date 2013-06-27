<hgroup class="page-title user-title cf">
	<div class="center">
		<div class="col_9">
			<a class="avatar-wrap" href="#">
				<img src="<?php echo $account['owner']['avatar']; ?>" class="avatar"/>
			</a>
			<h1><?php echo $account['owner']['name']; ?></h1>
			<h2 class="label"><?php echo $account['account_path']; ?></h2>
		</div>
		<div class="page-action col_3">
			<span class="follow-total">
				<a href="#" class="modal-trigger"><strong><?php echo $account['follower_count']; ?></strong> followers</a>, 
				<a href="#"><strong><?php echo $account['following_count']; ?></strong> following</a>
			</span>
			<?php if (isset($follow_button)): ?>
			<span class='button-follow' id="follow-button">
				<?php echo $follow_button; ?>
			</span>
			<?php endif; ?>
		</div>
	</div>
</hgroup>

<?php if ($show_navigation): ?>
<nav class="page-navigation cf">
	<div class="center">
		<ul class="col_12">
			<?php foreach ($nav as $item): ?>
				<li id="<?php echo $item['id']; ?>" class="<?php echo $item['id'] == $active ? 'active' : ''; ?>">
					<a href="<?php echo URL::site($account['account_path'].$item['url']); ?>">
						<?php echo $item['label'];?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</nav>
<?php endif; ?>

<div id="content" class="cf">
	<div class="center">
		<?php echo $sub_content; ?>
	</div>
</div>