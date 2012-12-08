<?php
	$page_title = "SwiftRiver press coverage";
	$template_type = "masonry";
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
				<h1><?php print $page_title; ?></h1>
			</div>
			<div class="page-action col_3">
				<!-- IF: User manages this river -->
				<a href="discussion.php" class="button button-white settings"><span class="icon-comment"></span></a>
				<a href="settings.php" class="button button-white settings"><span class="icon-cog"></span></a>
				<a href="#" class="button button-primary filters-trigger"><i class="icon-filter"></i>Filters</a>				
				<!-- ELSE IF: User follows this river
				<a href="#" class="button-follow selected button-primary"><i class="icon-checkmark"></i>Following</a>
				! ELSE
				<a href="#" class="button-follow button-primary"><i class="icon-checkmark"></i>Follow</a>
				-->
			</div>
		</div>
	</hgroup>

	<div id="content" class="river drops cf">
		<div class="center">

			<section id="filters" class="col_3">
				<div class="modal-window">
					<div class="modal">		
						<div class="modal-title cf">
							<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
							<h1>Filters</h1>
						</div>

						<ul class="filters-primary">
							<li class="active"><a href="#" class="modal-close"><span class="total">86</span> Drops</a></li>
							<li><a href="view-list.php" class="modal-close">List</a></li>
							<li><a href="view-photos.php" class="modal-close">Photos</a></li>
							<li><a href="#" class="modal-close">Map</a></li>
						</ul>
				
						<div class="filters-type">
							<ul>
								<!--li class="active"><a href="#"><span class="remove icon-cancel"></span><i class="icon-calendar"></i>November 1, 2012 to present</a></li-->
								<!--li class=""><a href="#"><span class="remove icon-cancel"></span><i class="icon-pencil"></i>hate, robbed</a></li-->
							</ul>
							<a href="/markup/_modals/add-search-filter.php" class="button-add modal-trigger"><i class="icon-search"></i>Add search filter</a>				
						</div>

						<div class="modal-toolbar">
							<a href="#" class="button-submit button-primary modal-close">Done</a>				
						</div>						
					</div>
				</div>												
			</section>
			
			<div id="stream" class="col_9">

				<article class="drop base">				
					<h1><a href="/markup/drop/" class="zoom-trigger">Saluting @chiefkariuki and what he's doing for Lanet Umoja Location via Twitter. You restore hope in our leadership sir! cc @ushahidi</a></h1>
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
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/_img/content/avatar1.png" /></a>
						<div class="byline">
							<h2>Nanjira Sambuli</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-twitter"></span>via Twitter</a></p>
						</div>
					</section>					
				</article>
			
				<article class="drop base">
					<a href="/markup/drop" class="drop-image-wrap zoom-trigger"><img src="/markup/_img/content/drop-image.png" class="drop-image" /></a>
					<h1><a href="/markup/drop/" class="zoom-trigger">The Europe Roundup: Cybercrime in the UK, Ushahidi in Serbia, Big Data in Norway</a></h1>				
					<div class="drop-actions cf">
						<ul class="dual-buttons drop-move">
							<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
							<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
						</ul>
						<span class="drop-score selected"><a href="#" class="button-white"><span class="icon-star"></span><span class="star-total">57</span></a></span>
						<ul class="drop-status cf">
							<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
							<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
						</ul>
					</div>					
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/_img/content/avatar2.png" /></a>
						<div class="byline">
							<h2>The Global Journal</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-rss"></span>via RSS</a></p>
						</div>
					</section>
				</article>
			
				<article class="drop base">
					<h1><a href="/markup/drop/" class="zoom-trigger">Is there any one here in Egypt who can explain to me how could I used USHAHIDI and Crowdmap for an advocacy campaign to fight illiteracy?</a></h1>
					<div class="drop-actions cf">
						<ul class="dual-buttons drop-move">
							<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
							<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
						</ul>
						<span class="drop-score"><a href="#" class="button-white"><span class="icon-star"></span></a></span>
						<ul class="drop-status cf">
							<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
							<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
						</ul>
					</div>
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/_img/content/avatar3.png" /></a>
						<div class="byline">
							<h2>The Global Journal</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-facebook"></span>via Facebook</a></p>
						</div>
					</section>
				</article>
	
				<article class="drop base">
					<h1><a href="/markup/drop/" class="zoom-trigger">Saluting @chiefkariuki and what he's doing for Lanet Umoja Location via Twitter. You restore hope in our leadership sir! cc @ushahidi</a></h1>
					<div class="drop-actions cf">
						<ul class="dual-buttons drop-move">
							<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
							<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
						</ul>
						<span class="drop-score"><a href="#" class="button-white"><span class="icon-star"></span></a></span>
						<ul class="drop-status cf">
							<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
							<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
						</ul>
					</div>
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/_img/content/avatar1.png" /></a>
						<div class="byline">
							<h2>Nanjira Sambuli</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-twitter"></span>via Twitter</a></p>
						</div>
					</section>
				</article>
			
				<article class="drop base">
					<a href="/markup/drop" class="drop-image-wrap zoom-trigger"><img src="/markup/_img/content/drop-image.png" class="drop-image" /></a>
					<h1><a href="/markup/drop/" class="zoom-trigger">The Europe Roundup: Cybercrime in the UK, Ushahidi in Serbia, Big Data in Norway</a></h1>
					<div class="drop-actions cf">
						<ul class="dual-buttons drop-move">
							<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
							<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
						</ul>
						<span class="drop-score"><a href="#" class="button-white"><span class="icon-star"></span></a></span>
						<ul class="drop-status cf">
							<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
							<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
						</ul>
					</div>
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/_img/content/avatar2.png" /></a>
						<div class="byline">
							<h2>The Global Journal</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-rss"></span>via RSS</a></p>
						</div>
					</section>
				</article>
			
				<article class="drop base">
					<h1><a href="/markup/drop/" class="zoom-trigger">Is there any one here in Egypt who can explain to me how could I used USHAHIDI and Crowdmap for an advocacy campaign to fight illiteracy?</a></h1>
					<div class="drop-actions cf">
						<ul class="dual-buttons drop-move">
							<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
							<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
						</ul>
						<span class="drop-score"><a href="#" class="button-white"><span class="icon-star"></span></a></span>
						<ul class="drop-status cf">
							<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
							<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
						</ul>
					</div>
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/_img/content/avatar3.png" /></a>
						<div class="byline">
							<h2>The Global Journal</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-facebook"></span>via Facebook</a></p>
						</div>
					</section>
				</article>

				<article class="drop base">
					<h1><a href="/markup/drop/" class="zoom-trigger">Saluting @chiefkariuki and what he's doing for Lanet Umoja Location via Twitter. You restore hope in our leadership sir! cc @ushahidi</a></h1>
					<div class="drop-actions cf">
						<ul class="dual-buttons drop-move">
							<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
							<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
						</ul>
						<span class="drop-score"><a href="#" class="button-white"><span class="icon-star"></span></a></span>
						<ul class="drop-status cf">
							<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
							<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
						</ul>
					</div>
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/_img/content/avatar1.png" /></a>
						<div class="byline">
							<h2>Nanjira Sambuli</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-twitter"></span>via Twitter</a></p>
						</div>
					</section>
				</article>
			
				<article class="drop base">
					<a href="/markup/drop" class="drop-image-wrap zoom-trigger"><img src="/markup/_img/content/drop-image.png" class="drop-image" /></a>
					<h1><a href="/markup/drop/" class="zoom-trigger">The Europe Roundup: Cybercrime in the UK, Ushahidi in Serbia, Big Data in Norway</a></h1>
					<div class="drop-actions cf">
						<ul class="dual-buttons drop-move">
							<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
							<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
						</ul>
						<span class="drop-score"><a href="#" class="button-white"><span class="icon-star"></span></a></span>
						<ul class="drop-status cf">
							<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
							<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
						</ul>
					</div>
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/_img/content/avatar2.png" /></a>
						<div class="byline">
							<h2>The Global Journal</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-rss"></span>via RSS</a></p>
						</div>
					</section>
				</article>
			
				<article class="drop base">
					<h1><a href="/markup/drop/" class="zoom-trigger">Is there any one here in Egypt who can explain to me how could I used USHAHIDI and Crowdmap for an advocacy campaign to fight illiteracy?</a></h1>
					<div class="drop-actions cf">
						<ul class="dual-buttons drop-move">
							<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
							<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
						</ul>
						<span class="drop-score"><a href="#" class="button-white"><span class="icon-star"></span></a></span>
						<ul class="drop-status cf">
							<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
							<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
						</ul>
					</div>
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/_img/content/avatar3.png" /></a>
						<div class="byline">
							<h2>The Global Journal</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-facebook"></span>via Facebook</a></p>
						</div>
					</section>
				</article>
	
				<article class="drop base">
					<h1><a href="/markup/drop/" class="zoom-trigger">Saluting @chiefkariuki and what he's doing for Lanet Umoja Location via Twitter. You restore hope in our leadership sir! cc @ushahidi</a></h1>
					<div class="drop-actions cf">
						<ul class="dual-buttons drop-move">
							<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
							<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
						</ul>
						<span class="drop-score"><a href="#" class="button-white"><span class="icon-star"></span></a></span>
						<ul class="drop-status cf">
							<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
							<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
						</ul>
					</div>
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/_img/content/avatar1.png" /></a>
						<div class="byline">
							<h2>Nanjira Sambuli</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-twitter"></span>via Twitter</a></p>
						</div>
					</section>
				</article>
			
				<article class="drop base">
					<a href="/markup/drop" class="drop-image-wrap zoom-trigger"><img src="/markup/_img/content/drop-image.png" class="drop-image" /></a>
					<h1><a href="/markup/drop/" class="zoom-trigger">The Europe Roundup: Cybercrime in the UK, Ushahidi in Serbia, Big Data in Norway</a></h1>
					<div class="drop-actions cf">
						<ul class="dual-buttons drop-move">
							<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
							<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
						</ul>
						<span class="drop-score"><a href="#" class="button-white"><span class="icon-star"></span></a></span>
						<ul class="drop-status cf">
							<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
							<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
						</ul>
					</div>
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/_img/content/avatar2.png" /></a>
						<div class="byline">
							<h2>The Global Journal</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-rss"></span>via RSS</a></p>
						</div>
					</section>
				</article>
			
				<article class="drop base">
					<h1><a href="/markup/drop/" class="zoom-trigger">Is there any one here in Egypt who can explain to me how could I used USHAHIDI and Crowdmap for an advocacy campaign to fight illiteracy?</a></h1>
					<div class="drop-actions cf">
						<ul class="dual-buttons drop-move">
							<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
							<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
						</ul>
						<span class="drop-score"><a href="#" class="button-white"><span class="icon-star"></span></a></span>
						<ul class="drop-status cf">
							<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
							<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
						</ul>
					</div>
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="/markup/_img/content/avatar3.png" /></a>
						<div class="byline">
							<h2>The Global Journal</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-facebook"></span>via Facebook</a></p>
						</div>
					</section>
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