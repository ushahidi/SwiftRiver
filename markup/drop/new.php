<?php
	$page_title = "Create a drop";
	$template_type = "drop";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_12">
				<h1><?php print $page_title; ?></h1>
			</div>
		</div>
	</hgroup>

	<article id="content" class="drop drop-full cf">
		<div class="center">
			<div class="drop drop-full col_9">
				<div class="base">
					<section class="drop-source cf">
						<p class="metadata">4:30 p.m. Jan. 13, 2012</p>
						<a href="/markup/user" class="avatar-wrap"><img src="/markup/images/content/avatar4.jpg" /></a>
						<div class="byline">
							<h2>Brandon Rosage</h2>
							<p class="drop-source-channel swiftriver"><a href="#"><span class="icon"></span>via SwiftRiver</a></p>
						</div>
					</section>
					<div class="drop-body">
						<label>
							<p class="field">Title/summary</p>
							<textarea rows="3"></textarea>
						</label>
					</div>
					<section class="drop-fullstory drop-sub">
						<label>
							<p class="field">Body</p>
							<textarea rows="10"></textarea>
						</label>
					</section>
					<div class="drop-actions cf">
						<ul class="move-drop">
							<li class="button-blue bucket"><a href="/markup/modal-bucket.php" class="modal-trigger"><span class="icon"></span>Add to bucket</a></li>
						</ul>
					</div>
				</div>
			</div>

			<div class="col_3">
				<section class="meta-data">
					<h3 class="arrow"><span class="icon"></span>Location <span class="button-blue button-small"><a href="/markup/modal-meta-location.php" class="modal-trigger">Edit</a></span></h3>
					<div class="meta-data-content">
						<p><a href="/markup/modal-meta-location.php" class="modal-trigger">Add a location.</a></p>
					</div>
				</section>

				<section class="meta-data">
					<h3 class="arrow"><span class="icon"></span>Media <span class="button-blue button-small"><a href="/markup/modal-meta-media.php" class="modal-trigger">Edit</a></span></h3>
					<div class="meta-data-content">
						<p><a href="/markup/modal-meta-media.php" class="modal-trigger">Add media.</a></p>
					</div>
				</section>

				<section class="meta-data">
					<h3 class="arrow"><span class="icon"></span>Links <span class="button-blue button-small"><a href="/markup/modal-meta-links.php" class="modal-trigger">Edit</a></span></h3>
					<div class="meta-data-content">
						<p><a href="/markup/modal-meta-links.php" class="modal-trigger">Add links.</a></p>
					</div>
				</section>

				<section class="meta-data">
					<h3 class="arrow"><span class="icon"></span>Tags <span class="button-blue button-small"><a href="/markup/modal-meta-tags.php" class="modal-trigger">Edit</a></span></h3>
					<div class="meta-data-content">
						<p><a href="/markup/modal-meta-tags.php" class="modal-trigger">Add tags.</a></p>
					</div>
				</section>
			</div>
			<div class="col_9">
				<div class="save-toolbar">
					<p class="button-blue"><a href="#">Save changes</a></p>
					<p class="button-blank"><a href="#">Cancel</a></p>
				</div>
			</div>
		</div>
	</article>

<div id="modal-container">
	<div class="modal-window"></div>
	<div class="modal-fade"></div>
</div>

<div id="confirmation-container">
	<div class="modal-window"></div>
	<div class="modal-fade"></div>
</div>

</body>
</html>