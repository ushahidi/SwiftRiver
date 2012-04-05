<?php
	$page_title = "Ushahidi at SXSW";
	$template_type = "settings";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1><?php print $page_title; ?> <em>settings</em></h1>
			</div>
			<div class="page-actions col_3">
				<h2 class="back">
					<a href="/markup/river/">
						<span class="icon"></span>
						Return to river
					</a>
				</h2>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<div class="center">
			<div id="page-views" class="settings touchcarousel col_12">
				<ul class="touchcarousel-container">
					<li class="touchcarousel-item active"><a href="/markup/river/settings-channels.php">Channels</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/settings-collaborators.php">Collaborators</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/settings-display.php">Display</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/settings-permissions.php">Permissions</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div id="content" class="settings channels cf">
		<div class="center">
			<div class="col_12">
				<div class="settings-toolbar">
					<p class="button-blue button-small create"><a href="/markup/modal-channels.php" class="modal-trigger"><span class="icon"></span>Add channel</a></p>
				</div>
	
				<!-- ALTERNATE MESSAGE WHEN THERE ARE NO CHANNELS //
				<div class="alert-message blue">
					<p><strong>No more channels.</strong> You can flow new channels into your river by selecting the "Add channel" button above.</p>
				</div>
				// END MESSAGE -->
	
				<article class="container base">
					<header class="cf">
						<a href="/markup/modal-prompt.php" class="remove-large modal-trigger"><span class="icon"></span><span class="nodisplay">Remove</span></a>
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
	
				<article class="container base">
					<header class="cf">
						<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay">Remove</span></a>
						<div class="property-title">
							<a href="#" class="avatar-wrap"><img src="/markup/images/channel-rss.gif" /></a>
							<h1>RSS</h1>
							<p class="button-white add add-parameter"><a href="#"><span class="icon"></span>Add feed</a></p>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="rss_url">
								<p class="field">Feed URL</p>
								<p class="title">Mashable News headlines</p>
								<input type="text" name="rss_url" placeholder="Enter the RSS feed's URL" />
								<p class="remove-small actions"><span class="icon"></span><span class="nodisplay">Remove</span></p>
							</label>
						</div>
						<div class="parameter">
							<label for="rss_url">
								<p class="field">Feed URL</p>
								<input type="text" name="rss_url" placeholder="Enter the RSS feed's URL" />
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