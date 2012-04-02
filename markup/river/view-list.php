<?php
	$page_title = "Ushahidi at SXSW";
	$template_type = "river-list";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1><?php print $page_title; ?></h1>
			</div>
			<div class="page-actions col_3">
				<h2 class="settings">
					<a href="/markup/river/settings-channels.php">
						<span class="icon"></span>
						River settings
					</a>
				</h2>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<div class="center">
			<div id="page-views" class="river touchcarousel col_9">
				<ul class="touchcarousel-container">
					<li class="touchcarousel-item"><a href="/markup/river">Drops</a></li>
					<li class="touchcarousel-item active"><a href="/markup/river/view-list.php">List</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/view-photos.php">Photos</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/view-map.php">Map</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/view-timeline.php">Timeline</a></li>
				</ul>
			</div>
			<div class="filter-actions col_3">
				<p class="button-blue button-small"><a href="/markup//river/filters.php" class="zoom-trigger">Filters</a></p>
			</div>
		</div>
	</nav>

	<div id="content" class="river list cf">
		<div class="center">
			<div class="col_12">
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
	</div>

<div id="zoom-container">
	<div class="modal-window"></div>
</div>

<div id="modal-container">
	<div class="modal-window"></div>
</div>

</body>
</html>