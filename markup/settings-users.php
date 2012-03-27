<?php
	$page_title = "SwiftRiver";
	$template_type = "settings";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="app-title cf">
		<div class="center">
			<div class="col_12">		
				<h1>Website name <em>settings</em></h1>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li><a href="/settings.php">General settings</a></li>
			<li class="active"><a href="/settings-users.php">Users</a></li>
			<li><a href="/settings-plugins.php">Plugins</a></li>
		</ul>
	</nav>

	<div id="content" class="settings cf">
		<div class="center">
			<div class="col_12">
				<div class="settings-toolbar">
					<p class="button-blue create"><a href="/markup/modal-collaborators.php" class="modal-trigger"><span class="icon"></span>Add user</a></p>
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