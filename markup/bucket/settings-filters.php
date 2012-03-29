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
			<li class="active"><a href="/markup/bucket/settings-filters.php">Filters</a></li>
			<li><a href="/markup/bucket/settings-collaborators.php">Collaborators</a></li>
			<li><a href="/markup/bucket/settings-display.php">Display</a></li>
			<li><a href="/markup/bucket/settings-permissions.php">Permissions</a></li>
		</ul>
	</nav>

	<div id="content" class="settings channels cf">
		<div class="center">
			<div class="col_12">
				<div class="settings-toolbar">
					<p class="button-blue button-small create"><a href="/markup/modal-filters.php" class="modal-trigger"><span class="icon"></span>Add filter</a></p>
				</div>
	
				<!-- ALTERNATE MESSAGE WHEN THERE ARE NO FILTERS //
				<div class="empty">
					<p><strong>No filters, yet.</strong> You can filter your river by selecting the "Add filter" button above.</p>
				</div>
				// END MESSAGE -->
	
				<article class="container base">
					<header class="cf">
						<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay">Remove</span></a>
						<div class="property-title">
							<h1>Date</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
								<input type="date" name="date_range-start" />
								<span class="combine">to</span>
								<input type="date" name="date_range-end" />
							</label>
						</div>
					</section>
				</article>
	
				<select class="boolean-operator">
					<option>and</option>
					<option>or</option>
				</select>
	
				<article class="container base">
					<header class="cf">
						<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay">Remove</span></a>
						<div class="property-title">
							<h1>Keyword</h1>
							<p class="button-white add add-parameter"><a href="#"><span class="icon"></span>Add keyword</a></p>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="twitter_keyword">
								<p class="field">Keyword</p>
								<input type="text" name="twitter_keyword" />
								<p class="remove-small actions"><span class="icon"></span><span class="nodisplay">Remove</span></p>
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