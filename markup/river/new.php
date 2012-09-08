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
		</ul>
	</nav>

	<div id="content" class="settings cf">
		<div class="center">
			<div class="col_12">
				<article class="container base">
					<header class="cf">
						<div class="property-title col_12">
							<h1>What's your river about?</h1>
						</div>
					</header>
					<section class="property-parameters cf">
						<form>
						<div class="parameter">
							<div class="field">
								<input type="text" placeholder="Name your river" />
							</div>
						</div>
						<div class="save-toolbar col_12">
							<p class="button-blue"><a href="/markup/river/new2.php">Next</a></p>
						</div>
						</form>						
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