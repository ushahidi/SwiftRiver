<?php
	$page_title = "Search results";
	$template_type = "masonry";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1><?php print $page_title; ?> <em>keyword</em></h1>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<div class="center">
			<div id="page-views" class="river touchcarousel col_12">
				<ul class="touchcarousel-container">
					<li class="touchcarousel-item"><a href="/markup/search/drops.php">Drops</a></li>
					<li class="touchcarousel-item active"><a href="/markup/search/rivers.php">Rivers</a></li>
					<li class="touchcarousel-item"><a href="/markup/search/buckets.php">Buckets</a></li>
					<li class="touchcarousel-item"><a href="/markup/search/users.php">Users</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div id="content" class="cf">
		<div class="center">
			<div class="col_12">
				<article class="container base">
					<header class="cf">
						<div class="actions">
							<p class="follow-count"><strong>48</strong> followers</p>
							<p class="button-white follow"><a href="#" title="now following"><span class="icon"></span><span class="nodisplay">Follow</span></a></p>
						</div>
						<div class="property-title">
							<h1><a href="/markup/river/">Ushahidi at SXSW</a></h1>
						</div>
					</header>
				</article>
	
				<article class="container base">
					<header class="cf">
						<div class="actions">
							<p class="follow-count"><strong>17</strong> followers</p>
							<p class="button-white follow selected"><a href="#" title="no longer following"><span class="icon"></span><span class="nodisplay">Following</span></a></p>
						</div>
						<div class="property-title">
							<h1><a href="/markup/river/">Bandit River</a></h1>
						</div>
					</header>
				</article>
			</div>
		</div>
	</div>

<div id="modal-container">
	<div class="modal-window"></div>
</div>

</body>
</html>