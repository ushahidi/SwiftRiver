<?php
	$page_title = "Kenya election speech";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<div class="col_9">
				<h1><?php print $page_title; ?></h1>
			</div>
			<div class="page-action col_3">
				<!-- IF: User manages this river -->
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
							<li><a href="/markup/river">Drops</a></li>
							<li class="active"><a href="view-list.php"><span class="total">204</span> List</a></li>
							<li><a href="view-photos.php">Photos</a></li>
							<li><a href="#">Map</a></li>
						</ul>
						<div class="filters-type">
							<ul>
								<li><a href="#"><span class="total">39</span> Unread</a></li>
								<li><a href="#"><span class="total">165</span> Read</a></li>
							</ul>
						</div>
				
						<div class="filters-type">
							<span class="toggle-filters-display"><span class="total">5</span><span class="icon-arrow-down"></span><span class="icon-arrow-up"></span></span>				
							<span class="filters-type-settings"><a href="/markup/_modals/settings-channels.php" class="modal-trigger"><span class="icon-cog"></span></a></span>
							<h2>Channels</h2>
							<div class="filters-type-details">
								<ul>
									<li class="active"><a href="#"><i class="icon-twitter"></i><span class="total">28</span> @Mainamshy, @rkulei...</a></li>
									<li class="active"><a href="#"><i class="icon-facebook"></i><span class="total">61</span> DailyNation, KTNKenya</a></li>
									<li class="active"><a href="#"><i class="icon-rss"></i><span class="total">83</span> The Kenyan Post</a></li>
									<li class="active"><a href="#"><i class="icon-rss"></i><span class="total">14</span> African Press</a></li>
									<li class="active"><a href="#"><i class="icon-rss"></i><span class="total">19</span> Standard Media</a></li>
								</ul>
							</div>
						</div>
							
						<div class="filters-type">
							<ul>
								<li class="active"><a href="#"><span class="remove icon-cancel"></span><i class="icon-calendar"></i>November 1, 2012 to present</a></li>
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
			
			<!-- ALTERNATE MESSAGE TO VIEW NEW DROPS //
			<article class="alert-message blue col_12 drop">
				<p><a href="#">13 new drops</a></p>
			</article>
			// END MESSAGE -->
			
			<div id="stream" class="col_9">

				<article class="drop base cf">
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="http://profile.ak.fbcdn.net/hprofile-ak-prn1/c0.0.80.80/535812_247939241973205_197626713_s.jpg" /></a>
						<div class="byline">
							<h2>Fred Aluhondo</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-facebook"></span>via Facebook</a></p>
						</div>
					</section>
					<div class="drop-body">
						<div class="drop-content">
							<h1><a href="/markup/drop/" class="zoom-trigger">This are terrorists in the making and should be crashed immediately</a></h1>
						</div>
						<div class="drop-details">
							<p class="metadata">2:15 a.m. Sept. 19, 2012 <a href="#"><i class="icon-comment"></i><strong>3</strong> comments</a></p>
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
						<a href="#" class="avatar-wrap"><img src="http://0.gravatar.com/avatar/0c6078b8694a5c8c2385ab7ba4a1f81b?s=200&r=pg&d=mm" /></a>
						<div class="byline">
							<h2>Uchambuzi Tanaka</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-rss"></span>via RSS</a></p>
						</div>
					</section>				
					<div class="drop-body">
						<div class="drop-content">
							<a href="/markup/drop" class="drop-image-wrap zoom-trigger"><img src="http://omwenga.files.wordpress.com/2012/09/raila_at_ease1.jpg?w=645" class="drop-image" /></a>
							<h1><a href="/markup/drop/" class="zoom-trigger">Response To More Rants and Raves From A Well Known Raila Hater and Basher</a></h1>
						</div>
						<div class="drop-details">						
							<p class="metadata">6 p.m. Sept. 12, 2012</p>
							<div class="drop-actions cf">
								<ul class="dual-buttons drop-move">
									<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
									<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span><span class="bucket-total">2</span></a></li>
								</ul>
								<span class="drop-score selected"><a href="#" class="button-white"><span class="icon-star"></span><span class="star-total">4</span></a></span>
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
						<a href="#" class="avatar-wrap"><img src="https://fbcdn-profile-a.akamaihd.net/hprofile-ak-ash3/c27.0.100.100/p100x100/48989_1024691504_9486_n.jpg" /></a>
						<div class="byline">
							<h2>Leo Kinuthia</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-facebook"></span>via Facebook</a></p>
						</div>
					</section>
					<div class="drop-body">
						<div class="drop-content">
							<h1><a href="/markup/drop/" class="zoom-trigger">Sounds like an ant saying it will drink Indian Ocean dry.</a></h1>
						</div>
						<div class="drop-details">
							<p class="metadata">2:15 a.m. Sept. 19, 2012 <a href="#"><i class="icon-comment"></i><strong>1</strong> comment</a></p>
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
						</div>
					</div>			
				</article>

				<article class="drop base cf">
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/62278078/twitter_icon_normal.png" /></a>
						<div class="byline">
							<h2>mashada</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-rss"></span>via RSS</a></p>
						</div>
					</section>
					<div class="drop-body">
						<div class="drop-content">
							<h1><a href="/markup/drop/" class="zoom-trigger">The Maasai people of kajiado have rejected Railas Odingas party of domo domo, lies and vitendawili. They have clearly said that they have no time to listen to lies about insuring their goats and cows from the chief liar Raila Odinga.</a></h1>
						</div>
						<div class="drop-details">						
							<p class="metadata">1:50 p.m. Sept. 17, 2012</p>
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
						</div>							
					</div>
				</article>

				<article class="drop base cf">
					<section class="drop-source cf">
						<a href="#" class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/1858200445/207881_1648161331257_1453651553_1331249_1891276_n_normal.jpg" /></a>
						<div class="byline">
							<h2>Deryl Aduda</h2>
							<p class="drop-source-channel"><a href="#"><span class="icon-twitter"></span>via Twitter</a></p>
						</div>
					</section>
					<div class="drop-body">
						<div class="drop-content">
							<a href="/markup/drop" class="drop-image-wrap zoom-trigger"><img src="http://i2.ytimg.com/vi/94_YcORrGT0/mqdefault.jpg" class="drop-image" /></a>
							<h1><a href="/markup/drop/" class="zoom-trigger">These guys in this video know who killed #ShemKwega,Forward to minute 5:01 Masked Democracy: http://youtu.be/94_YcORrGT0  via @youtube #UnitedKisumu</a></h1>
						</div>
						<div class="drop-details">						
							<p class="metadata">7:19 p.m. Sept. 16, 2012</p>
							<div class="drop-actions cf">
								<ul class="dual-buttons drop-move">
									<li class="share"><a href="/markup/_modals/add-to-service.php" class="button-primary modal-trigger"><span class="icon-share"></span></a></li>
									<li class="bucket"><a href="/markup/_modals/add-to-bucket.php" class="button-primary modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
								</ul>
								<span class="drop-score selected"><a href="#" class="button-white"><span class="icon-star"></span><span class="star-total">9</span></a></span>
								<ul class="drop-status cf">
									<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
									<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a></li>
								</ul>								
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