<?php
	$page_title = "SwiftRiver press coverage";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<!-- SYSTEM MESSAGE //
	<article id="system-message">
		<div class="center">
			<a href="#" class="system-message-close"><span class="icon-cancel"></span></a>
			<p><a href="#">13 new drops</a></p>
		</div>
	</article>
	// END SYSTEM MESSAGE -->

	<hgroup class="page-title cf">
		<div class="center">
			<div class="col_9">
				<h1><a href="/markup/river"><?php print $page_title; ?></a> <em>discussion</em></h1>
			</div>
			<div class="page-action col_3">
				<a href="/markup/bucket" class="button-white">Return to bucket</a>
				<a href="#" class="button button-primary filters-trigger"><i class="icon-filter"></i>Filters</a>
			</div>
		</div>
	</hgroup>

	<div id="content" class="river list cf">
		<div class="center">

			<section id="filters" class="col_3">
				<div class="modal-window">
					<div class="modal">		
						<div class="modal-title cf">
							<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
							<h1>Filters</h1>
						</div>
						<ul class="filters-primary">
							<li class="active"><a href="#"><span class="total">204</span> All messages</a></li>
							<li><a href="#">Yours</a></li>
							<li><a href="#">Mentions</a></li>
						</ul>
							
						<div class="filters-type">
							<!--ul>
								<li class="active"><a href="#"><span class="remove icon-cancel"></span><i class="icon-calendar"></i>November 1, 2012 to present</a></li>
								<li class=""><a href="#"><span class="remove icon-cancel"></span><i class="icon-pencil"></i>hate, robbed</a></li>
							</ul-->
							<a href="/markup/_modals/add-search-filter.php" class="button-add modal-trigger"><i class="icon-search"></i>Add search filter</a>				
						</div>

						<div class="modal-toolbar">
							<a href="#" class="button-submit button-primary modal-close">Done</a>				
						</div>
					</div>
				</div>
			</section>
			
			<div id="stream" class="col_9">

					<article class="drop base cf">
						<section class="drop-source cf">
							<a href="#" class="avatar-wrap"><img src="http://profile.ak.fbcdn.net/hprofile-ak-prn1/c0.0.80.80/535812_247939241973205_197626713_s.jpg" /></a>
							<div class="byline">
								<h2>Fred Aluhondo</h2>
							</div>
						</section>
						<div class="drop-body">
							<div class="drop-content">
								<h1><a href="/markup/drop/" class="zoom-trigger">This are terrorists in the making and should be crashed immediately</a></h1>
							</div>
							<div class="drop-details">
								<p class="metadata">2:15 a.m. Sept. 19, 2012</p>
								<div class="drop-actions cf">
									<ul class="dual-buttons drop-move">
										<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
								<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span><span class="bucket-total">4</span></a></li>
									</ul>
									<span class="drop-score"><a href="#" class="button-white"><span class="icon-star"></span></a></span>
									<ul class="drop-status cf">
										<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
										<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
									</ul>								
								</div>
							</div>
						</div>				
					</article>

					<article class="drop base cf">
						<section class="drop-source cf">
							<a href="#" class="avatar-wrap"><img src="http://www.ushahidi.com/uploads/people/team_Emmanuel-Kala.jpg" /></a>
							<div class="byline">
								<h2>Emmanuel Kala</h2>
							</div>
						</section>
						<div class="drop-body">
							<div class="drop-content">
								<textarea></textarea>
							</div>
							<div class="drop-details">
								<div class="drop-actions cf">
									<a href="#" class="button-primary">Publish</a>								
								</div>
							</div>
						</div>				
					</article>			

			</div>
		</div>
	</div>

<div id="zoom-container">
	<div class="modal-window"></div>
</div>

<div id="modal-container">
	<div class="modal-window"></div>
</div>

</body>
</html>