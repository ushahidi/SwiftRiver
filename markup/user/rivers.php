<?php
	$page_title = "Brandon Rosage";
	$template_type = "user";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="user-title cf">
		<div class="center">
			<div class="user-summary col_9">		
				<a class="avatar-wrap"><img src="/markup/images/content/avatar4.jpg" class="avatar" /></a>
				<h1>Brandon Rosage</h1>
				<h2>brosage</h2>
			</div>
			<div class="follow-summary col_3">
				<p class="follow-count"><a href="#"><strong>28</strong> followers</a>, <a href="#"><strong>18</strong> following</a></p>
				<p class="button-score button-white follow"><a href="#"><span class="icon"></span>Follow</a></p>
			</div>
		</div>
	</hgroup>

	<div id="content" class="user cf">
		<div class="center">
			<div class="col_12">
				<article class="container action-list base">
					<header class="cf">
						<div class="property-title">
							<h1>Your rivers</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<div class="actions">
								<p class="follow-count"><strong>67</strong> followers</p>
								<p class="remove-small"><a href="/markup/modal-remove.php" class="modal-trigger"><span class="icon"></span><span class="nodisplay">Remove</span></a></p>
							</div>
							<h2><a href="/markup/river">Ushahidi at SXSW</a></h2>
						</div>
						<div class="parameter">
							<div class="actions">
								<p class="follow-count"><strong>67</strong> followers</p>
								<p class="remove-small"><a href="/markup/modal-remove.php" class="modal-trigger"><span class="icon"></span><span class="nodisplay">Remove</span></a></p>
							</div>
							<h2><a href="/markup/river">Robotics</a></h2>
						</div>
					</section>
				</article>

				<article class="container action-list base">
					<header class="cf">
						<div class="property-title">
							<h1>Rivers you follow</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<div class="actions">
								<p class="follow-count"><strong>67</strong> followers</p>
								<p class="button-white follow selected"><a href="#" title="no longer following"><span class="icon"></span><span class="nodisplay">Following</span></a></p>
							</div>
							<h2><a href="/markup/river">Ushahidi at SXSW</a></h2>
						</div>
						<div class="parameter">
							<div class="actions">
								<p class="follow-count"><strong>67</strong> followers</p>
								<p class="button-white follow selected"><a href="#" title="no longer following"><span class="icon"></span><span class="nodisplay">Following</span></a></p>
							</div>
							<h2><a href="/markup/river">Robotics</a></h2>
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

<div id="confirmation-container">
	<div class="modal-window"></div>
	<div class="modal-fade"></div>
</div>

</body>
</html>