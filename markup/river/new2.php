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
			<li><a href="/markup/river/new.php">1. Name your river</a></li>
			<li class="active"><a href="/markup/river/new2.php">2. Open channels</a></li>
			<li><a href="/markup/river/new3.php">3. View your river</a></li>
		</ul>
	</nav>

	<div id="content" class="settings channels cf">
		<div class="center">
			<div class="col_12">
				<div class="settings-toolbar">
					<p class="button-blue create button-small"><a href="/markup/modal-channels.php" class="modal-trigger"><span class="icon"></span>Add channel</a></p>
				</div>

				<div class="alert-message blue">
					<p><strong>Open at least one channel.</strong> You can flow new channels into your river by selecting the "Add channel" button above.</p>
				</div>

				<!-- MARKUP FOR OPEN CHANNELS //	
				<article class="container base">
					<header class="cf">
						<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay">Remove</span></a>
						<div class="property-title">
							<a href="#" class="avatar-wrap"><img src="/markup/images/channel-twitter.gif" /></a>
							<h1>Twitter</h1>
							<div class="popover add-parameter">
								<p class="button-white add"><a href="#" class="popover-trigger"><span class="icon"></span>Add parameter</a></p>
								<ul class="popover-window base">
									<li><a href="#">Keyword</a></li>
									<li><a href="#">User</a></li>
								</ul>
							</div>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="twitter_keyword">
								<p class="field">Keyword</p>
								<input type="text" name="twitter_keyword" placeholder="Enter keyword" />
								<p class="remove-small actions"><span class="icon"></span><span class="nodisplay">Remove</span></p>
							</label>
						</div>
					</section>
				</article>
				// END MARKUP -->

				<div class="settings-toolbar">
					<p class="button-blue button-big"><a href="/markup/river/new3.php">Next</a></p>
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