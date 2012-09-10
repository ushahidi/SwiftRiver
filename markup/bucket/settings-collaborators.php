<?php
	$page_title = "Ushahidi press coverage";
	$template_type = "settings";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title bucket-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1><a href="/markup/bucket/"><?php print $page_title; ?></a> <em>settings</em></h1>
			</div>
			<div class="page-action col_3">
				<span class="button-white"><a href="/markup/bucket/">Return to bucket</a></span>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<div class="center">
			<div id="page-views" class="settings touchcarousel col_12">
				<ul class="touchcarousel-container">
					<li class="touchcarousel-item active"><a href="/markup/bucket/settings-collaborators.php">Collaborators</a></li>
					<li class="touchcarousel-item"><a href="/markup/bucket/settings-options.php">Options</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div id="content" class="settings collaborators cf">
		<div class="center">
			<div class="col_12">
				<div class="button-actions">
					<span><a href="/markup/modal-collaborators.php" class="modal-trigger"><i class="icon-users"></i>Add collaborator</a></span>
				</div>			
			
				<article class="container base">
					<header class="cf">
						<div class="property-title col_8">
							<a href="#" class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/2480249545/5k18ycibrx45r7g3v4pb_reasonably_small.jpeg" /></a>
							<h1><a href="#">Juliana Rotich</a></h1>
						</div>
						<div class="button-actions col_4">
							<span class="popover"><a href="#" class="popover-trigger"><span class="icon-remove"></span><span class="nodisplay">Remove</span></a>
								<ul class="popover-window popover-prompt base">
									<li class="destruct"><a href="#">Remove collaborator from river</a></li>
								</ul>							
							</span>
						</div>						
					</header>				
				</article>
	
				<article class="container base">
					<header class="cf">
						<div class="property-title col_8">
							<a href="#" class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_reasonably_small.jpeg" /></a>
							<h1><a href="#">Nathaniel Manning</a></h1>
						</div>
						<div class="button-actions col_4">
							<span class="popover"><a href="#" class="popover-trigger"><span class="icon-remove"></span><span class="nodisplay">Remove</span></a>
								<ul class="popover-window popover-prompt base">
									<li class="destruct"><a href="#">Remove collaborator from river</a></li>
								</ul>							
							</span>							
						</div>						
					</header>				
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