<?php
	$page_title = "Ushahidi at SXSW";
	$template_type = "settings";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_12">
				<h1><a href="/markup/river/"><?php print $page_title; ?></a> <em>settings</em></h1>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<div class="center">
			<div id="page-views" class="settings touchcarousel col_12">
				<ul class="touchcarousel-container">
					<li class="touchcarousel-item"><a href="/markup/river/settings-channels.php">Channels</a></li>
					<li class="touchcarousel-item active"><a href="/markup/river/settings-collaborators.php">Collaborators</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/settings-display.php">Display</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/settings-permissions.php">Permissions</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div id="content" class="settings collaborators cf">
		<div class="center">
			<div class="col_12">
				<article class="container base">
					<header class="cf">
						<div class="property-title col_8">
							<a href="#" class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/2480249545/5k18ycibrx45r7g3v4pb_reasonably_small.jpeg" /></a>
							<h1><a href="#">Juliana Rotich</a></h1>
						</div>
						<div class="button-actions col_4">
							<a href="#"><span class="icon-remove"></span><span class="nodisplay">Remove</span></a>
						</div>						
					</header>				
				</article>
	
				<article class="container base">
					<header class="cf">
						<div class="property-title col_8">
							<a href="#" class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_reasonably_small.jpeg" /></a>
							<h1><a href="#">Nathaniel Manning</a></h1>
						</div>
						<div class="button-actions col_4">
							<a href="#"><span class="icon-remove"></span><span class="nodisplay">Remove</span></a>
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