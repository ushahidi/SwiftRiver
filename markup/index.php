<?php
	$page_title = "Emmanuel Kala";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title user-title cf">
		<div class="center">
			<div class="col_9">
				<a class="avatar-wrap"><img src="http://www.ushahidi.com/uploads/people/team_Emmanuel-Kala.jpg" class="avatar" /></a>
				<h1><?php print $page_title; ?></h1>
				<h2 class="label">ekala</h2>
			</div>
			<div class="page-action col_3">
				<span class="follow-total"><a href="/markup/_modals/followers.php" class="modal-trigger"><strong>28</strong> followers</a>, <a href="#"><strong>18</strong> following</a></span>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<div class="center">
			<ul class="col_12">
				<li class="active"><a href="/markup/">Activity</a></li>
				<li><a href="/markup/home/content.php">Content</a></li>
			</ul>
		</div>
	</nav>

	<div id="content" class="cf">
		<div class="center">
			<div id="filters-trigger" class="col_12 cf">
				<a href="#" class="button button-primary filters-trigger"><i class="icon-filter"></i>Filters</a>
			</div>

			<section id="filters" class="col_3">
				<div class="modal-window">
					<div class="modal">
						<div class="modal-title cf">
							<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
							<h1>Filters</h1>
						</div>

						<ul class="filters-primary">
							<li class="active"><a href="#" class="modal-close">Everything</a></li>
							<li><a href="#" class="modal-close">Pending</a></li>
							<li><a href="#" class="modal-close">Discussion</a></li>
						</ul>
					</div>
				</div>
			</section>

			<div class="col_9">
				<div id="news-feed" class="container base">
					<!--// IF: No activity //
					<article class="stream-message cf">
						<h1>This is where you'll see the latest from people, rivers and buckets you follow.</h1>

						<div class="search-box">
							<h2>Find people, rivers or buckets</h2>
							<input type="text" placeholder="e.g. Detroit Tigers" />
							<a href="#" class="button-submit"><span class="icon-search"></span></a>
						</div>

						<div>
							<h2>Popular rivers</h2>
							<ul class="view-table">
								<li><a href="/markup/river">Ushahidi at SXSW</a></li>
								<li><a href="#">River 2</a></li>
							</ul>

							<h2>Popular people</h2>
							<ul class="view-table">
								<li class="user cf">
									<a href="#">
									<img src="https://si0.twimg.com/profile_images/2525445853/TweetLandPhoto_normal.jpg" class="avatar">
									<span class="label">Juliana Rotich</span>
									</a>
								</li>
							</ul>
						</div>
					</article>
					// ENDIF //-->

					<!--// IF: No results //
					<article class="news-feed-item cf">
						<div class="item-summary">
							<h2>No activity to show.</h2>
						</div>
					</article>
					// ENDIF //-->

					<article class="news-feed-item cf">
						<div class="item-type">
							<span class="icon-comment"></span>
						</div>
						<div class="item-summary">
							<span class="timestamp">42 minutes ago</span>
							<h2><a href="#">Juliana Rotich</a> commented on bucket <a href="#">Kenya election hate speech</a></h2>
							<div class="item-sample">
								<a href="#" class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/2525445853/TweetLandPhoto_reasonably_small.jpg" /></a>
								<div class="item-sample-body">
									<p>Pork andouille tail, jowl ball tip jerky turkey bacon tongue flank strip steak swine pork chop. Sirloin chuck chicken spare ribs kielbasa tenderloin swine sausage leberkas cow.</p>
								</div>
							</div>
						</div>
					</article>

					<article class="news-feed-item cf">
						<div class="item-type">
							<span class="icon-river"></span>
						</div>
						<div class="item-summary">
							<span class="timestamp">3 hours ago</span>
							<h2><a href="/markup/river">Open Source software</a> added <strong>60 new drops</strong></h2>
							<div class="item-sample">
								<span class="quota-meter"><span class="quota-meter-capacity"><span class="quota-total" style="width:40%;"></span></span>40% full</span>
							</div>
						</div>
					</article>

					<article class="news-feed-item cf">
						<div class="item-type">
							<span class="icon-comment"></span>
						</div>
						<div class="item-summary">
							<span class="timestamp">1 day ago</span>
							<h2><a href="#">Rob Baker</a> commented on bucket <a href="#">Web development</a></h2>
							<div class="item-sample">
								<a href="#" class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/899724610/profile-me2_normal.jpg" /></a>
								<div class="item-sample-body">
									<p>Short loin meatball pork loin leberkas venison pork belly tri-tip short ribs ground round ribeye. Tail pastrami shankle pancetta pork belly ball tip, filet mignon shank.</p>
								</div>
							</div>
						</div>
					</article>

					<article class="pending news-feed-item cf">
						<div class="item-type">
							<span class="icon-bucket"></span>
						</div>
						<div class="item-summary">
							<span class="timestamp">2 days ago</span>
							<h2><a href="#">Linda Kamau</a> invited you to collaborate on bucket <a href="#">Skyfall</a></h2>
							<div class="item-actions">
								<a href="#" class="button-white"><i class="icon-checkmark"></i>Accept</a>
								<a href="#" class="button-white">Ignore</a>
							</div>
						</div>
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