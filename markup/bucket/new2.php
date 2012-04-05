<?php
	$page_title = "Create a bucket";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title bucket-title cf">
		<div class="center">
			<div class="page-h1 col_12">
				<h1><?php print $page_title; ?></h1>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li><a href="/markup/bucket/new.php">1. Name your bucket</a></li>
			<li class="active"><a href="/markup/bucket/new2.php">2. Invite collaborators</a></li>
			<li><a href="/markup/bucket/">3. View your bucket</a></li>
		</ul>
	</nav>

	<div id="content" class="settings channels cf">
		<div class="center">
			<div class="col_12">
				<div class="settings-toolbar">
					<p class="button-blue button-small create"><a href="/markup/modal-collaborators.php" class="modal-trigger"><span class="icon"></span>Add collaborator</a></p>
				</div>

				<div class="alert-message blue">
					<p><strong>Invite someone, or go it alone.</strong> To collaboratively manage the contents of your bucket, select the "Add collaborator" button above. Or just skip this step and start working.</p>
				</div>

				<!-- MARKUP FOR COLLABORATORS //	
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
				// END MARKUP -->

				<div class="settings-toolbar">
					<p class="button-blue button-big"><a href="/markup/bucket/new3.php">Next</a></p>
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