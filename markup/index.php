<?php
	$page_title = "Dashboard";
	$template_type = "dashboard";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="user-title dashboard cf">
		<div class="center">
			<div class="user-summary col_9">		
				<a class="avatar-wrap"><img src="/markup/images/content/avatar4.jpg" class="avatar" /></a>
				<h1>Brandon Rosage</h1>
				<h2 class="label">brosage</h2>
			</div>
			<div class="follow-summary col_3">
				<p class="follow-count"><a href="#"><strong>28</strong> followers</a>, <a href="#"><strong>18</strong> following</a></p>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<div id="page-views" class="col_12">
			<ul class="center">
				<li class="active"><a href="/">Dashboard</a></li>
				<li><a href="/markup/user/settings.php">Account settings</a></li>
			</ul>
		</div>
	</nav>

	<div id="content" class="user dashboard cf">
		<div class="center">
			<div class="col_9">
				<article id="primer" class="container base">
					<header class="cf">
						<div class="property-title col_6">
							<h1>Get started</h1>
						</div>
					</header>
					<section class="property-parameters cf">
						<div class="parameter primer-item learn">
							<h3><a href="#">Learn how SwiftRiver works</a></h3>
						</div>
						<div class="parameter primer-item create">
							<h3><a href="/markup/river/new.php">Create a river</a></h3>
						</div>
						<div class="parameter primer-item search">
							<h3><a href="/markup/modal-search.php" class="modal-trigger">Find stuff that interests you</a></h3>
						</div>
					</section>
				</article>

				<article class="container base">
					<header class="cf">
						<div class="property-title col_6">
							<h1>Activity</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter activity-item">
							<a class="avatar-wrap"><img src="/markup/images/content/avatar5.jpg" class="avatar" /></a>
							<div class="item-body">
								<h3><a href="/markup/user">Nat Manning</a> subscribed to the <a href="#">Reasons to hate the Patriots</a> river</h3>
								<p class="metadata">4:30 p.m. Jan. 13, 2012</p>
							</div>
						</div>
						<div class="parameter activity-item">
							<a class="avatar-wrap"><img src="/markup/images/content/avatar5.jpg" class="avatar" /></a>
							<div class="item-body">
								<h3><a href="/markup/user">Nat Manning</a> invited you to collaborate on the <a href="#">Batman Forever</a> bucket</h3>
								<p class="metadata">9:04 a.m. Feb. 28, 2012</p>
							</div>
							<ul class="button-actions">
								<li><a href="#">Accept</a></li>
								<li><a href="#">Reject</a></li>
							</ul>							
						</div>						
						<div class="parameter activity-item">
							<a class="avatar-wrap"><img src="/markup/images/content/avatar5.jpg" class="avatar" /></a>
							<div class="item-body">
								<h3><a href="/markup/user">Nat Manning</a> created the <a href="#">Top restaurants in Berkeley</a> bucket</h3>
								<p class="metadata">9:04 a.m. Feb. 28, 2012</p>
							</div>
						</div>
					</section>
				</article>

				<article class="container action-list base">
					<header class="cf">
						<div class="property-title col_6">
							<h1>Popular this week</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<ul class="button-actions">
								<li><span class="count"><strong>48</strong> new followers</span><a href="#"><i class="icon-checkmark"></i>Follow</a></li>
							</ul>
							<h3><a href="/markup/river">Ushahidi at SXSW</a></h3>
						</div>
						<div class="parameter">
							<ul class="button-actions">
								<li><span class="count"><strong>67</strong> new followers</span><a href="#" class="selected"><i class="icon-checkmark"></i><span class="nodisplay">Following</span></a></li>
							</ul>
							<h3><a href="/markup/river">Robotics</a></h3>
						</div>
					</section>
				</article>
			</div>

			<div class="col_3">
				<article class="container action-list base">
					<header class="cf">
						<div class="property-title col_12">
							<h1><a href="/user/rivers.php">Rivers</a></h1>
						</div>
					</header>
					<section class="property-parameters asset-list">
						<h2 class="category">Your rivers</h2>
						<div class="parameter">
							<h3><a href="/markup/river">Ushahidi at SXSW</a></h3>
						</div>
						
						<div class="parameter">
							<h3><a href="/markup/river">Robotics</a></h3>
						</div>

						<h2 class="category">Rivers you follow</h2>
						<div class="parameter">
							<h3><a href="/markup/river">Ushahidi at SXSW</a></h3>
						</div>
						<div class="parameter">
							<h3><a href="/markup/river">Robotics</a></h3>
						</div>
					</section>
				</article>

				<article class="container action-list base">
					<header class="cf">
						<div class="property-title col_6">
							<h1><a href="/user/buckets.php">Buckets</a></h1>
						</div>
					</header>
					<section class="property-parameters">
						<h2 class="category">Your buckets</h2>
						<div class="parameter">
							<h3><a href="/markup/river">Love for Ushahidi</a></h3>
						</div>
						<div class="parameter">
							<h3><a href="/markup/river">Top restaurants in Berkeley</a></h3>
						</div>

						<h2 class="category">Buckets you follow</h2>
						<div class="parameter">
							<h3><a href="/markup/river">Love for Ushahidi</a></h3>
						</div>
						<div class="parameter">
							<h3><a href="/markup/river">Top restaurants in Berkeley</a></h3>
						</div>
					</section>
				</article>
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