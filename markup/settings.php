<?php
	$page_title = "Summary";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<div class="col_9">		
				<h1>SwiftRiver <em>settings</em></h1>
			</div>
			<div class="col_3"></div>			
		</div>
	</hgroup>

	<div id="content">
		<div class="center body-tabs-container cf">

			<div id="filters" class="col_3">
				<ul class="body-tabs-menu filters-primary">
					<li class="active"><a href="#general">General</a></li>
					<li><a href="#users">Users</a></li>
					<li><a href="#plugins">Plugins</a></li>
				</ul>																
			</div>
					
			<div id="settings" class="body-tabs-window col_9">
			
				<!-- TAB: General -->
				<div id="general" class="active">
					<article class="base settings-category">
						<h1>Basics</h1>
						<div class="body-field">
							<h3 class="label">Website name</h3>
							<input type="text" value="SwiftRiver" />
						</div>
						<div class="body-field">
							<h3 class="label">Language</h3>
							<select>
								<option>English</option>
								<option>Spanish</option>
							</select>
						</div>
					</article>

					<article class="base settings-category">
						<h1>Access</h1>
						<div class="body-field">
							<label><input type="checkbox" /> Allow public registration</label>
						</div>
						<div class="body-field">
							<label><input type="checkbox" /> Allow anonymous access</label>
						</div>					
					</article>																			
				</div>

				<!-- TAB: users -->
				<div id="users">
					<article class="base settings-category">
						<h1>Users</h1>
						
						<ul class="view-table">
							<li class="user cf">
								<a href="/markup/_modals/settings-user.php" class="modal-trigger">
								<span class="remove icon-cancel"></span>
								<img src="https://si0.twimg.com/profile_images/2525445853/TweetLandPhoto_normal.jpg" class="avatar">Juliana Rotich
								</a>
							</li>
							<li class="user cf">
								<a href="/markup/_modals/settings-user.php" class="modal-trigger">
								<span class="remove icon-cancel"></span>
								<img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_normal.jpeg" class="avatar">Nathaniel Manning
								</a>
							</li>							
							<li class="add"><a href="/markup/_modals/add-user.php" class="modal-trigger">Add user</a></li>
						</ul>
					</article>			
				</div>
				
				<!-- TAB: Plugins -->
				<div id="plugins">
					<article class="base settings-category">
						<h1>Plugins</h1>

						<ul class="view-table">
							<li class="static cf">
								<span class="follow">Activate</span>
								Plugin 1
							</li>
							<li class="static cf">
								<span class="follow selected icon-checkmark"> On</span>
								Plugin 2
							</li>							
						</ul>

					</article>
				</div>
								
			</div>
		</div>
	</div>

<div id="modal-container">
	<div class="modal-window"></div>
	<div class="modal-window-secondary"></div>
</div>

</body>
</html>