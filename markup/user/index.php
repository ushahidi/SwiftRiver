<?php
	$page_title = "Nathaniel Manning";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>
	
	<hgroup class="page-title user-title cf">
		<div class="center">
			<div class="col_9">		
				<a class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_bigger.jpeg" class="avatar" /></a>
				<h1><?php print $page_title; ?></h1>
				<h2 class="label">natpmanning</h2>
			</div>
			<div class="page-action col_3">
				<span class="follow-total"><a href="#"><strong>28</strong> followers</a>, <a href="#"><strong>18</strong> following</a></span>
				<span class="button-follow"><a href="#" class="button-primary selected"><i class="icon-checkmark"></i>Following</a></span>
			</div>
		</div>
	</hgroup>	
	
	<nav class="page-navigation cf">
		<div id="page-views" class="col_12">
			<ul class="center">
				<li class="active"><a href="/markup/user/">Activity</a></li>
				<li><a href="/markup/user/content.php">Content</a></li>
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
							<li class="active"><a href="#">Everything</a></li>
							<li><a href="#">Discussion</a></li>
						</ul>				
					</div>
				</div>
			</section>

			<div class="col_9">
				<div id="news-feed" class="container base">
					<!-- NO RESULTS: Clear filters
					<div class="null-message">
						<h2>No rivers or buckets to show.</h2>
						<p>Clear active filters.</p>
					</div>
					--->
					
					<article class="news-feed-item cf">
						<div class="item-type">
							<span class="icon-comment"></span>
						</div>
						<div class="item-summary">
							<span class="timestamp">42 minutes ago</span>
							<h2><a href="#">Nathaniel Manning</a> commented on bucket <a href="#">Detroit Tigers hot stove</a></h2>
							<div class="item-sample">
								<a href="#" class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_bigger.jpeg" /></a>
								<div class="item-sample-body">
									<p>Pork andouille tail, jowl ball tip jerky turkey bacon tongue flank strip steak swine pork chop. Sirloin chuck chicken spare ribs kielbasa tenderloin swine sausage leberkas cow.</p>
								</div>
							</div>
						</div>
					</article>

					<article class="news-feed-item cf">
						<div class="item-type">
							<span class="icon-comment"></span>
						</div>
						<div class="item-summary">
							<span class="timestamp">1 day ago</span>
							<h2><a href="#">Nathaniel Manning</a> commented on bucket <a href="#">Web design and development</a></h2>
							<div class="item-sample">
								<a href="#" class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_bigger.jpeg" /></a>
								<div class="item-sample-body">
									<p>Short loin meatball pork loin leberkas venison pork belly tri-tip short ribs ground round ribeye. Tail pastrami shankle pancetta pork belly ball tip, filet mignon shank.</p>
								</div>
							</div>
						</div>
					</article>
					
					<article class="news-feed-item cf">
						<div class="item-type">
							<span class="icon-bucket"></span>
						</div>
						<div class="item-summary">
							<span class="timestamp">2 days ago</span>
							<h2><a href="#">Nathaniel Manning</a> accepted an invitation to collaborate on bucket <a href="#">Skyfall</a></h2>
							<div class="item-sample">
								<div class="item-sample-body">
									<p>Tail pastrami shankle pancetta pork belly ball tip, filet mignon shank.</p>
								</div>
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

<div id="confirmation-container">
	<div class="modal-window"></div>
</div>

</body>
</html>