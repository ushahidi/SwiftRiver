<?php
	$page_title = "Kenya election speech";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<!-- SYSTEM MESSAGE //-->
	<article class="system-message failure">
		<div class="center">
			<a href="#" class="system-message-close"><span class="icon-cancel"></span></a>
			<p><strong>Confirmation.</strong> Pig shank andouille, meatball salami pancetta corned beef.</p>
		</div>
	</article>
	<!--// END SYSTEM MESSAGE -->

	<hgroup class="page-title cf">
		<div class="center">
			<div class="col_9">
				<h1><?php print $page_title; ?></h1>
			</div>
			<div class="page-action col_3">
				<!-- IF: User manages this river -->
				<a href="settings.php" class="button button-white settings"><span class="icon-cog"></span></a>
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
				<ul class="filters-primary">
					<li class="active"><a href="#"><span class="total">0</span> Drops</a></li>
					<li><a href="view-list.php">List</a></li>
					<li><a href="view-photos.php">Photos</a></li>
					<li><a href="#">Map</a></li>
				</ul>
			</section>

			<div id="stream" class="col_9">
				<!--// IF: No channels //-->
				<article class="stream-message no-drops">
					<h1>Let's add at least one channel.</h1>
					<p>Channels (like Twitter and RSS) are what feed your river information. You'll need at least one to see drops in your river. Otherwise, it'll remain dry.</p>
					<a href="/markup/_modals/settings-channels.php#add-channel" class="button-primary modal-trigger"><i class="icon-plus"></i>Add channels</a>
					<div id="stream-illustration"></div>
				</article>
				<!--// ENDIF -->
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