<hgroup class="page-title bucket-title cf">
	<div class="center">
		<div class="page-h1 col_9">
			<h1><?php print $page_title; ?></h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="settings">
				<a href="/markup/bucket/settings-filters.php">
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
		<div class="rundown-people col_9">
			<h2>Collaborators on this bucket</h2>
			<ul>
				<li><a href="#" class="avatar-wrap"><img src="/markup/images/content/avatar1.png" /></a></li>
				<li><a href="#" class="avatar-wrap"><img src="/markup/images/content/avatar2.png" /></a></li>
			</ul>
		</div>
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