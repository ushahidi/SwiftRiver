<?php
	$page_title = "SwiftRiver press coverage";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<div class="col_9">
				<h1><a href="/markup/river"><?php print $page_title; ?></a> <em>settings</em></h1>
			</div>
			<div class="page-action col_3">
				<a href="/markup/bucket" class="button button-white">Return to bucket</a>
				<a href="#" class="button button-primary filters-trigger"><i class="icon-menu"></i>More</a>
			</div>
		</div>
	</hgroup>

	<div id="content" class="river drops cf">
		<div class="center body-tabs-container">

			<section id="filters" class="col_3">
				<div class="modal-window">
					<div class="modal">		
						<div class="modal-title cf">
							<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
							<h1>Filters</h1>
						</div>
						
						<ul class="body-tabs-menu filters-primary">
							<li class="active"><a href="#options" class="modal-close">Options</a></li>
							<li><a href="#collaborators" class="modal-close">Collaborators</a></li>
							<li><a href="#analytics" class="modal-close">Analytics</a></li>
						</ul>
					</div>
				</div>															
			</section>
					
			<div id="settings" class="body-tabs-window col_9">
			
				<!-- TAB: Options -->
				<div id="options" class="active">
					<article class="base settings-category">
						<h1>Basics</h1>
						<div class="body-field">
							<h3 class="label">Bucket name</h3>
							<input type="text" value="<?php print $page_title; ?>" />
						</div>
						<div class="body-field">
							<h3 class="label">Bucket URL</h3>
							<span class="domain">http://swiftapp.com/</span><input type="text" value="ushahidi-at-sxsw" class="domain" />
						</div>					
					</article>

					<article class="base settings-category">
						<h1>Display</h1>
						<div class="body-field">
							<h3 class="label">Default view</h3>
							<select>
								<option>Drops</option>
								<option>List</option>
								<option>Photos</option>								
								<option>Map</option>
							</select>
						</div>					
						<div class="body-field">
							<h3 class="label">Who can view this bucket</h3>
							<select>
								<option>Public</option>								
								<option>Only collaborators</option>
							</select>
						</div>
					</article>																				
				</div>

				<!-- TAB: Collaborators -->
				<div id="collaborators">
					<article class="base settings-category">
						<h1>Collaborators</h1>

						<ul class="view-table">
							<li class="static user cf">
								<span class="remove icon-cancel"></span>
								<img src="https://si0.twimg.com/profile_images/2525445853/TweetLandPhoto_normal.jpg" class="avatar">Juliana Rotich
							</li>
							<li class="static user cf">
								<span class="remove icon-cancel"></span>
								<img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_normal.jpeg" class="avatar">Nathaniel Manning
							</li>
							<li class="add"><a href="/markup/_modals/add-collaborator.php" class="modal-trigger">Add collaborator</a></li>
						</ul>

					</article>
				</div>

				<!-- TAB: Analytics -->
				<div id="analytics">
					<article class="base settings-category">
						<h1>Stuff</h1>

						<ul class="view-table">
							<li class="static user cf">
								<span class="remove icon-cancel"></span>
								<img src="https://si0.twimg.com/profile_images/2525445853/TweetLandPhoto_normal.jpg" class="avatar">Juliana Rotich
							</li>
							<li class="static user cf">
								<span class="remove icon-cancel"></span>
								<img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_normal.jpeg" class="avatar">Nathaniel Manning
							</li>
							<li class="add"><a href="/markup/_modals/add-collaborator.php" class="modal-trigger">Add collaborator</a></li>
						</ul>

					</article>
				</div>
								
			</div>
		</div>
	</div>

<div id="modal-container">
	<div class="modal-window"></div>
</div>

</body>
</html>