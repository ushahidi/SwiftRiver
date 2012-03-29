<?php
	$page_title = "Ushahidi press coverage";
	$template_type = "settings";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title bucket-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1><?php print $page_title; ?> <em>settings</em></h1>
			</div>
			<div class="page-actions col_3">
				<h2 class="back">
					<a href="/markup/bucket/">
						<span class="icon"></span>
						Return to bucket
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

	<nav class="page-navigation cf">
		<ul class="center">
			<li><a href="/markup/bucket/settings-filters.php">Filters</a></li>
			<li class="active"><a href="/markup/bucket/settings-collaborators.php">Collaborators</a></li>
			<li><a href="/markup/bucket/settings-display.php">Display</a></li>
			<li><a href="/markup/bucket/settings-permissions.php">Permissions</a></li>
		</ul>
	</nav>

	<div id="content" class="settings collaborators cf">
		<div class="center">
			<div class="col_12">
				<div class="settings-toolbar">
					<p class="button-blue button-small create"><a href="/markup/modal-collaborators.php" class="modal-trigger"><span class="icon"></span>Add collaborator</a></p>
				</div>
				<article class="container base">
					<header class="cf">
						<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay">Remove</span></a>
						<div class="property-title">
							<a href="#" class="avatar-wrap"><img src="/markup/images/content/avatar3.png" /></a>
							<h1>Nathaniel Manning</h1>
						</div>
					</header>
				</article>
	
				<article class="container base">
					<header class="cf">
						<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay">Remove</span></a>
						<div class="property-title">
							<a href="#" class="avatar-wrap"><img src="/markup/images/content/avatar2.png" /></a>
							<h1>Juliana Rotich</h1>
						</div>
					</header>
				</article>
			</div>
		</div>
	</div>

<div id="modal-container">
	<div class="modal-window"></div>
	<div class="modal-fade"></div>
</div>

</body>
</html>