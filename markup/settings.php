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
			<li class="active"><a href="/settings.php">General settings</a></li>
			<li><a href="/settings-users.php">Users</a></li>
			<li><a href="/settings-plugins.php">Plugins</a></li>
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
							<label for="website_name">
								<p class="field">Website name</p>
								<input type="text" name="website_name" placeholder="Enter name" />
							</label>
						</div>
					</section>
				</article>

				<article class="container base">
					<header class="cf">
						<div class="property-title">
							<h1>Language</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<select>
								<option>English</option>
								<option>other</option>
							</select>
						</div>
					</section>
				</article>
	
				<article class="container base">
					<header class="cf">
						<div class="property-title">
							<h1>Access</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="public_registration">
								<input type="checkbox" name="public_registration" />
								Allow public registration
							</label>
							<label for="anonymous">
								<input type="checkbox" name="anonymous" />
								Allow anonymous access
							</label>
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