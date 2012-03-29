<?php
	$page_title = "Ushahidi press coverage";
	$template_type = "masonry";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

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

	<section class="bucket-rundown cf">
		<div class="center">
			<!--div class="rundown-totals col_3">
				<ul>
					<li><strong>88</strong> drops</li>
					<li><a href="/markup/bucket/followers.php"><strong>17</strong> followers</a></li>
				</ul>
			</div-->
			<div class="rundown-collaborators col_9">
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
			<li><a href="/markup/bucket">Drops</a></li>
			<li class="active"><a href="/markup/bucket/view-list.php">List</a></li>
			<li><a href="/markup/bucket/view-photos.php">Photos</a></li>
			<li><a href="/markup/bucket/view-map.php">Map</a></li>
			<li><a href="/markup/bucket/view-timeline.php">Timeline</a></li>
		</ul>
	</nav>

	<div id="content" class="river list cf">
		<div class="center">
			<article class="drop base cf">
				<div class="drop-content">
					<div class="drop-body">
						<h1><a href="/markup/drop/" class="zoom-trigger">Saluting @chiefkariuki and what he's doing for Lanet Umoja Location via Twitter. You restore hope in our leadership sir! cc @ushahidi</a></h1>
						<p class="metadata discussion">4:30 p.m. Jan. 13, 2012 <a href="#"><span class="icon"></span><strong>3</strong> comments</a></p>
					</div>
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/images/content/avatar1.png" /></a>
						<div class="byline">
							<h2>Nanjira Sambuli</h2>
							<p class="drop-source-channel twitter"><a href="#"><span class="icon"></span>via Twitter</a></p>
						</div>
					</section>
				</div>
				<div class="drop-actions stacked cf">
					<ul class="dual-buttons move-drop">
						<li class="button-blue share"><a href="/markup/modal-share.php" class="modal-trigger"><span class="icon"></span></a></li>
						<li class="button-blue bucket"><a href="/markup/modal-bucket.php" class="modal-trigger"><span class="icon"></span></a></li>
					</ul>
					<ul class="dual-buttons score-drop">
						<li class="button-white like"><a href="#"><span class="icon"></span></a></li>
						<li class="button-white dislike"><a href="#"><span class="icon"></span></a></li>
					</ul>
				</div>
			</article>

			<article class="drop base cf">
				<div class="drop-content">
					<div class="drop-body">
						<a href="/markup/drop" class="drop-image-wrap zoom-trigger"><img src="/markup/images/content/drop-image.png" class="drop-image" /></a>
						<h1><a href="/markup/drop/" class="zoom-trigger">Saluting @chiefkariuki and what he's doing for Lanet Umoja Location via Twitter. You restore hope in our leadership sir! cc @ushahidi</a></h1>
						<p class="metadata discussion">4:30 p.m. Jan. 13, 2012 <a href="#"><span class="icon"></span><strong>3</strong> comments</a></p>
					</div>
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/images/content/avatar1.png" /></a>
						<div class="byline">
							<h2>Nanjira Sambuli</h2>
							<p class="drop-source-channel twitter"><a href="#"><span class="icon"></span>via Twitter</a></p>
						</div>
					</section>
				</div>
				<div class="drop-actions stacked cf">
					<ul class="dual-buttons move-drop">
						<li class="button-blue share"><a href="/markup/modal-share.php" class="modal-trigger"><span class="icon"></span></a></li>
						<li class="button-blue bucket"><a href="/markup/modal-bucket.php" class="modal-trigger"><span class="icon"></span></a></li>
					</ul>
					<ul class="dual-buttons score-drop">
						<li class="button-white like"><a href="#"><span class="icon"></span></a></li>
						<li class="button-white dislike"><a href="#"><span class="icon"></span></a></li>
					</ul>
				</div>
			</article>

		</div>
	</div>

<div id="zoom-container">
	<div class="modal-window"></div>
</div>

<div id="modal-container">
	<div class="modal-window"></div>
</div>

</body>
</html>