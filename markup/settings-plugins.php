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
			<li><a href="/settings.php">General settings</a></li>
			<li><a href="/settings-users.php">Users</a></li>
			<li class="active"><a href="/settings-plugins.php">Plugins</a></li>
		</ul>
	</nav>

	<div id="content" class="settings cf">
		<div class="center">
			<div class="col_12">
				<div class="settings-toolbar">
					<p class="button-blue create"><a href="/markup/modal-channels.php" class="modal-trigger"><span class="icon"></span>Add plugin</a></p>
				</div>
	
				<!-- ALTERNATE MESSAGE WHEN THERE ARE NO CHANNELS //
				<div class="alert-message blue">
					<p><strong>No more channels.</strong> You can flow new channels into your river by selecting the "Add channel" button above.</p>
				</div>
				// END MESSAGE -->
	
				<article class="container base">
					<header class="cf">
						<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay">Remove</span></a>
						<div class="property-title">
							<h1>Plugin 1</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="parameter">
								<p class="field">Parameter</p>
								<input type="text" name="parameter" placeholder="" />
								<p class="remove-small actions"><span class="icon"></span><span class="nodisplay">Remove</span></p>
							</label>
						</div>
					</section>
				</article>
	
				<article class="container base">
					<header class="cf">
						<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay">Remove</span></a>
						<div class="property-title">
							<h1>Plugin 2</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="parameter">
								<p class="field">Parameter</p>
								<input type="text" name="parameter" placeholder="" />
								<p class="remove-small actions"><span class="icon"></span><span class="nodisplay">Remove</span></p>
							</label>
						</div>
						<div class="parameter">
							<label for="parameter">
								<p class="field">Parameter</p>
								<input type="text" name="parameter" placeholder="" />
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