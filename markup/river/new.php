<?php
	$page_title = "Create a river";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_12">
				<h1><?php print $page_title; ?></h1>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li class="active"><a href="/markup/river/new.php">1. Name your river</a></li>
			<li><a href="/markup/river/new2.php">2. Open channels</a></li>
			<li><a href="/markup/river/new3.php">3. View your river</a></li>
		</ul>
	</nav>

	<div id="content" class="settings cf">
		<div class="center">
			<div class="col_12">
				<article class="container base">
					<header class="cf">
						<div class="property-title">
							<h1>Name</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="river_name">
								<p class="field">Display name</p>
								<input type="text" value="<?php print $page_title; ?>" name="river_name" />
							</label>
						</div>
						<div class="parameter">
							<label for="river_url">
								<p class="field">URL</p>
								<input type="text" value="ushahidi-at-sxsw" name="river_url" />
							</label>
						</div>
					</section>
				</article>

				<div class="settings-toolbar">
					<p class="button-blue button-big"><a href="/markup/river/new2.php">Next</a></p>
				</div>
			</div>
		</div>
	</div>

<div id="modal-container">
	<div class="modal-window"></div>
	<div class="modal-fade"></div>
</div>

</body>
</html>