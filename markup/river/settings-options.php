<?php
	$page_title = "Ushahidi at SXSW";
	$template_type = "settings";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1><a href="/markup/river/"><?php print $page_title; ?></a> <em>settings</em></h1>
			</div>
			<div class="page-action col_3">
				<span class="button-white"><a href="/markup/river/">Return to river</a></span>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<div class="center">
			<div id="page-views" class="settings touchcarousel col_12">
				<ul class="touchcarousel-container">
					<li class="touchcarousel-item"><a href="/markup/river/settings-channels.php">Flow</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/settings-collaborators.php">Collaborators</a></li>
					<li class="touchcarousel-item active"><a href="/markup/river/settings-display.php">Options</a></li>
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
						<div class="save-toolbar col_12">
							<p class="button-blue"><a href="#">Save changes</a></p>
							<p class="button-blank cancel"><a href="#">Cancel</a></p>
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
						<div class="save-toolbar col_12">
							<p class="button-blue"><a href="#">Save changes</a></p>
							<p class="button-blank cancel"><a href="#">Cancel</a></p>
						</div>	
					</section>
				</article>

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

				<span class="view-results button-white destruct"><a href="#">Delete this river</a></span>				
								
			</div>
		</div>
	</div>
	
<footer id="global-footer"></footer>	

<div id="modal-container">
	<div class="modal-window"></div>
	<div class="modal-fade"></div>
</div>

</body>
</html>