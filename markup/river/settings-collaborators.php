<?php
	$page_title = "Ushahidi at SXSW";
	$template_type = "settings";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<h1><?php print $page_title; ?> <em>settings</em></h1>
			<h2 class="back label">
				<a href="/markup/river">
					<span class="icon"></span>
					<span class="label">Return to river</span>
				</a>
			</h2>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li><a href="/markup/river/settings-filters.php">Filters</a></li>
			<li><a href="/markup/river/settings-channels.php">Channels</a></li>
			<li class="active"><a href="/markup/river/settings-collaborators.php">Collaborators</a></li>
			<li><a href="/markup/river/settings-display.php">Display</a></li>
			<li><a href="/markup/river/settings-permissions.php">Permissions</a></li>
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