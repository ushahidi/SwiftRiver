<?php
	$page_title = "Emmanuel Kala";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>
	
	<hgroup class="page-title user-title cf">
		<div class="center">
			<div class="col_9">		
				<a class="avatar-wrap"><img src="http://www.ushahidi.com/uploads/people/team_Emmanuel-Kala.jpg" class="avatar" /></a>
				<h1><?php print $page_title; ?> <em>settings</em></h1>
				<h2 class="label">ekala</h2>
			</div>
			<div class="page-action col_3">
				<span class="follow-total"><a href="/markup/_modals/followers.php" class="modal-trigger"><strong>28</strong> followers</a>, <a href="#"><strong>18</strong> following</a></span>
			</div>
		</div>
	</hgroup>	

	<div id="content">
		<div class="center body-tabs-container cf">

			<div id="filters-trigger" class="col_12 cf">
				<a href="#" class="button button-primary filters-trigger"><i class="icon-menu"></i>More</a>
			</div>
			
			<section id="filters" class="col_3">
				<div class="modal-window">
					<div class="modal">		
						<div class="modal-title cf">
							<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
							<h1>Filters</h1>
						</div>

						<ul class="body-tabs-menu filters-primary">
							<li class="active"><a href="#account" class="modal-close">Account</a></li>
							<li><a href="#notifications" class="modal-close">Notifications</a></li>
							<li><a href="#services" class="modal-close">Services</a></li>
						</ul>
					</div>
				</div>														
			</section>
					
			<div id="settings" class="body-tabs-window col_9">
			
				<!-- TAB: Account -->
				<div id="account" class="active">
					<article class="base settings-category">
						<h1>Basics</h1>
						<div class="body-field">
							<h3 class="label">Name</h3>
							<input type="text" value="Brandon Rosage" />
						</div>
						<div class="body-field">
							<h3 class="label">Email</h3>
							<input type="text" value="brandon@ushahidi.com" />
						</div>
						<div class="body-field">
							<h3 class="label">Website</h3>
							<input type="text" value="http://brandonrosage.com" />
						</div>
						<div class="body-field">
							<h3 class="label">Gravatar Email <em>(Private)</em></h3>
							<input type="text" value="brandon.rosage@gmail.com" />
						</div>																							
					</article>

					<article class="base settings-category">
						<h1>Change password</h1>
						<div class="body-field">
							<h3 class="label">Old password</h3>
							<input type="password" />
						</div>
						<div class="body-field">
							<h3 class="label">New password</h3>
							<input type="password" />
						</div>
						<div class="body-field">
							<h3 class="label">Confirm new password</h3>
							<input type="password" />
						</div>
						<div class="settings-category-toolbar">
							<a href="#" class="button-submit button-primary modal-close">Update password</a>
							<a href="#" class="button-destruct button-secondary">I forgot my password</a>						
						</div>					
					</article>																			
				</div>

				<!-- TAB: Notifications -->
				<div id="notifications"></div>
				
				<!-- TAB: Connected services -->
				<div id="services">
					<article class="base settings-category">
						<h1>Services</h1>

						<ul class="view-table">
							<li class="static cf">
								<span class="remove icon-cancel"></span>
								<i class="channel-icon icon-twitter"></i>Twitter <em>brosage</em>
							</li>
							<li class="add"><a href="/markup/_modals/add-service.php" class="modal-trigger">Add service</a></li>
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

<div id="confirmation-container">
	<div class="modal-window"></div>
</div>

</body>
</html>