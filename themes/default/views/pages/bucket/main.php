<hgroup class="page-title bucket-title cf">
	<div class="center">
		<div class="page-h1 col_9">
			<h1><?php print $page_title; ?></h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="settings">
				<a href="<?php echo $settings_url; ?>">
					<span class="icon"></span>
					Bucket settings
				</a>
			</h2>
			<h2 class="discussion">
				<a href="/markup/bucket/discussion.php">
					<span class="icon"></span>
					Discussion
				</a>
			</h2>
		</div>
	</div>
</hgroup>

<section class="rundown bucket cf">
	<div class="center">
		<!--div class="rundown-totals col_3">
			<ul>
				<li><strong>88</strong> drops</li>
				<li><a href="/markup/bucket/followers.php"><strong>17</strong> followers</a></li>
			</ul>
		</div-->
		<?php if ( ! empty($collaborators)): ?>
		<div class="rundown-people col_9">
			<h2>Collaborators on this bucket</h2>
			<ul>
				<?php foreach ($collaborators as $collaborator): ?>
					<?php if ($collaborator['collaborator_active'] == 1): ?>
						<li><a href="<?php echo URL::site().$collaborator['account_path'] ?>" class="avatar-wrap" title="<?php echo $collaborator['name']; ?>"><img src="<?php echo $collaborator['avatar']; ?>" /></a></li>
					<?php endif; ?>
				<?php endforeach;?>
			</ul>
		</div>
		<?php endif; ?>
	</div>
</section>

<nav class="page-navigation cf">
	<ul class="center">
		<li id="drops-navigation-link"><a onclick="appRouter.navigate('/drops', {trigger: true}); return false;" href="#">Drops</a></li>
		<li id="list-navigation-link"><a onclick="appRouter.navigate('/list', {trigger: true}); return false;" href="#">List</a></li>
		<li><a href="#">Photos</a></li>
		<li><a href="#">Map</a></li>
		<li><a href="#">Timeline</a></li>
	</ul>
</nav>

<?php echo $droplets_view; ?>