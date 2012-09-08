<?php
	$page_title = "Ushahidi press coverage";
	$template_type = "settings";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title bucket-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1><a href="/markup/bucket/"><?php print $page_title; ?></a> <em>settings</em></h1>
			</div>
			<div class="page-action col_3">
				<span class="button-white"><a href="/markup/bucket/">Return to bucket</a></span>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<div class="center">
			<div id="page-views" class="settings touchcarousel col_12">
				<ul class="touchcarousel-container">
					<li class="touchcarousel-item"><a href="/markup/bucket/settings-collaborators.php">Collaborators</a></li>
					<li class="touchcarousel-item"><a href="/markup/bucket/settings-display.php">Display</a></li>
					<li class="touchcarousel-item active"><a href="/markup/bucket/settings-permissions.php">Permissions</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div id="content" class="settings channels cf">
		<div class="center">
			<div class="col_12">
				<article class="container base">
					<header class="cf">
						<div class="property-title col_12">
							<h1>Who can view this river?</h1>
						</div>
					</header>
					<section class="property-parameters cf">
						<div class="parameter">
							<div class="field">
								<select>
									<option>Public</option>
									<option>Only collaborators</option>
								</select>
							</div>
							<div class="save-toolbar">
								<p class="button-blue"><a href="#">Save changes</a></p>
								<p class="button-blank cancel"><a href="#">Cancel</a></p>
							</div>	
						</div>
					</section>
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