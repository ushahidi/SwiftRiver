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
					<li class="touchcarousel-item"><a href="/markup/river/settings-collaborators.php">Collaborators</a></li>
					<li class="touchcarousel-item active"><a href="/markup/river/settings-display.php">Display</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/settings-permissions.php">Permissions</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div id="content" class="settings cf">
		<div class="center">
			<div class="col_12">
				<article class="container base">
					<header class="cf">
						<div class="property-title col_12">
							<h1>Name</h1>
						</div>
					</header>
					<section class="property-parameters cf">
						<div class="parameter">
							<div class="field">
								<p class="field-label">Display name</p>
								<input type="text" value="<?php print $page_title; ?>" />
							</div>
						</div>
						<div class="parameter">
							<div class="field">
								<p class="field-label">URL</p>
								<input type="text" value="ushahidi-at-sxsw" name="river_url" />
							</div>
						</div>
					</section>
				</article>

				<article class="container base">
					<header class="cf">
						<div class="property-title col_12">
							<h1>Default view</h1>
						</div>
					</header>
					<section class="property-parameters cf">
						<div class="parameter">
							<div class="field">
								<select>
									<option>Drops</option>
									<option>List</option>
									<option>Photos</option>
									<option>Timeline</option>
								</select>
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