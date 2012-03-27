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
		<ul class="center">
			<li class="active"><a href="/">Dashboard</a></li>
			<li><a href="/user/settings.php">Account settings</a></li>
		</ul>
	</nav>

	<div id="content" class="user dashboard cf">
		<div class="center">
			<div class="col_9">
				<article class="container base">
					<header class="cf">
						<div class="property-title">
							<h1>Activity</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter activity-item cf">
							<a class="avatar-wrap"><img src="/markup/images/content/avatar5.jpg" class="avatar" /></a>
							<div class="item-body">
								<h2><a href="/user">Nat Manning</a> subscribed to the <a href="#">Reasons to hate the Patriots</a> river</h2>
								<p class="metadata">4:30 p.m. Jan. 13, 2012</p>
							</div>
						</div>
						<div class="parameter activity-item cf">
							<a class="avatar-wrap"><img src="/markup/images/content/avatar5.jpg" class="avatar" /></a>
							<div class="item-body">
								<h2><a href="/user">Nat Manning</a> created the <a href="#">Top restaurants in Berkeley</a> bucket</h2>
								<p class="metadata">9:04 a.m. Feb. 28, 2012</p>
							</div>
						</div>
						<div class="parameter activity-item cf">
							<a class="avatar-wrap"><img src="/markup/images/content/avatar5.jpg" class="avatar" /></a>
							<div class="item-body">
								<div class="actions">
									<ul class="dual-buttons">
										<li class="button-white no-icon"><a href="#">Reject</a></li>
										<li class="button-white no-icon"><a href="#">Approve</a></li>
									</ul>
								</div>
								<h2><a href="/user">Nat Manning</a> invited you to collaborate on the <a href="#">Batman Forever</a> bucket</h2>
								<p class="metadata">9:04 a.m. Feb. 28, 2012</p>
							</div>
						</div>
					</section>
				</article>

				<article class="container action-list base">
					<header class="cf">
						<div class="property-title">
							<h1>Popular this week</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<div class="actions">
								<p class="follow-count"><strong>48</strong> new followers</p>
								<p class="button-white follow"><a href="#" title="now following"><span class="icon"></span><span class="nodisplay">Follow</span></a></p>
							</div>
							<h2><a href="/markup/river">Ushahidi at SXSW</a></h2>
						</div>
						<div class="parameter">
							<div class="actions">
								<p class="follow-count"><strong>67</strong> new followers</p>
								<p class="button-white follow selected"><a href="#" title="no longer following"><span class="icon"></span><span class="nodisplay">Follow</span></a></p>
							</div>
							<h2><a href="/markup/river">Robotics</a></h2>
						</div>
					</section>
				</article>
			</div>

			<div class="col_3">
				<article class="container action-list base">
					<header class="cf">
						<div class="property-title">
							<h1><a href="/user/rivers.php">Rivers</a></h1>
						</div>
					</header>
					<section class="property-parameters">
						<p class="category">Your rivers</p>
						<div class="parameter">
							<p class="button-white follow"><a href="#" title="now following"><span class="icon"></span><span class="nodisplay">Follow</span></a></p>
							<h2><a href="/markup/river">Ushahidi at SXSW</a></h2>
						</div>
						<div class="parameter">
							<p class="button-white follow selected"><a href="#" title="no longer following"><span class="icon"></span><span class="nodisplay">Following</span></a></p>
							<h2><a href="/markup/river">Robotics</a></h2>
						</div>

						<p class="category">Rivers you follow</p>
						<div class="parameter">
							<p class="button-white follow"><a href="#" title="now following"><span class="icon"></span><span class="nodisplay">Follow</span></a></p>
							<h2><a href="/markup/river">Ushahidi at SXSW</a></h2>
						</div>
						<div class="parameter">
							<p class="button-white follow selected"><a href="#" title="no longer following"><span class="icon"></span><span class="nodisplay">Following</span></a></p>
							<h2><a href="/markup/river">Robotics</a></h2>
						</div>
					</section>
				</article>

				<article class="container action-list base">
					<header class="cf">
						<div class="property-title">
							<h1><a href="/user/buckets.php">Buckets</a></h1>
						</div>
					</header>
					<section class="property-parameters">
						<p class="category">Your buckets</p>
						<div class="parameter">
							<p class="button-white follow"><a href="#" title="now following"><span class="icon"></span><span class="nodisplay">Follow</span></a></p>
							<h2><a href="/markup/river">Love for Ushahidi</a></h2>
						</div>
						<div class="parameter">
							<p class="button-white follow selected" title="no longer following"><a href="#"><span class="icon"></span><span class="nodisplay">Following</span></a></p>
							<h2><a href="/markup/river">Top restaurants in Berkeley</a></h2>
						</div>

						<p class="category">Buckets you follow</p>
						<div class="parameter">
							<p class="button-white follow"><a href="#" title="now following"><span class="icon"></span><span class="nodisplay">Follow</span></a></p>
							<h2><a href="/markup/river">Love for Ushahidi</a></h2>
						</div>
						<div class="parameter">
							<p class="button-white follow selected" title="no longer following"><a href="#"><span class="icon"></span><span class="nodisplay">Following</span></a></p>
							<h2><a href="/markup/river">Top restaurants in Berkeley</a></h2>
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