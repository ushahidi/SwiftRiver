<?php
	$page_title = "Create a river";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_12">
				<h1><?php print $page_title; ?></h1>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li><a href="/markup/river/new.php">1. Name your river</a></li>
			<li class="active"><a href="/markup/river/new2.php">2. Open channels</a></li>
		</ul>
	</nav>

	<div id="content" class="settings flow cf">
		<div class="center">
			<div class="col_12">
				<article class="container base channels">
					<header class="cf">
						<div class="property-title col_6">
							<h1>Channels</h1>
						</div>
						<ul class="button-actions col_6">
							<li class="popover"><a href="#" class="popover-trigger"><i class="icon-plus"></i>Add channel</a>
								<ul class="popover-window base">
									<li><a href="#"><i class="icon-twitter"></i>Twitter</a></li>
									<li><a href="#"><i class="icon-rss"></i>RSS</a></li>
									<li><a href="#"><i class="icon-facebook"></i>Facebook</a></li>
								</ul>
							</li>
						</ul>
					</header>
					<section class="property-parameters cf">
						<div class="parameter-placeholder col_2">
							<a href="add-channel-twitter.php" class="parameter-edit modal-trigger">
								<h1><span class="icon-twitter"><span class="icon-plus"></span></span><span class="label">Add Twitter search for <em>river name</em></label></h1>							
							</a>					
						</div>
						<div class="parameter-placeholder col_2">
							<a href="add-channel-rss.php" class="parameter-edit modal-trigger">
								<h1><span class="icon-rss"><span class="icon-plus"></span></span><span class="label">Add RSS feeds</label></h1>							
							</a>					
						</div>
						<div class="parameter-placeholder col_2">
							<a href="add-channel-twitter.php" class="parameter-edit modal-trigger">
								<h1><span class="icon-facebook"><span class="icon-plus"></span></span><span class="label">Add Facebook search for <em>river name</em></label></h1>							
							</a>					
						</div>																	
					</section>
				</article>
				
				<div class="property-link">
					<span class="icon-river"></span>
					<span class="button-actions"><a href="#"><span class="icon-plus"></span></a></span>
				</div>
	
				<article class="container base">
					<header class="cf">
						<div class="property-title col_6">
							<h1><i class="icon-locked"></i>Filter group</h1>
						</div>
						<ul class="button-actions col_6">
							<li class="popover"><a href="#" class="popover-trigger"><i class="icon-plus"></i>Add filter</a>
								<ul class="popover-window base">
									<li><a href="#">Keyword</a></li>
									<li><a href="#">Date</a></li>
								</ul>
							</li>
							<li><a href="edit-filter-group_unsaved.php" class="modal-trigger"><i class="icon-settings"></i>Group settings</a></li>
							<li><a href="#"><span class="icon-remove"></span><span class="nodisplay">Remove</span></a></li>
						</ul>						
					</header>
					<section class="property-parameters cf">
						<!-- WHEN NO PARAMETERS EXIST, YET. -->
						<div class="parameter-placeholder col_2">
							<a href="add-filter-date.php" class="parameter-edit modal-trigger">
								<h1><span class="icon-calendar"><span class="icon-plus"></span></span><span class="label">Add date filter</label></h1>							
							</a>					
						</div>
						<div class="parameter-placeholder col_2">
							<a href="add-filter-keyword.php" class="parameter-edit modal-trigger">
								<h1><span class="icon-pencil-2"><span class="icon-plus"></span></span><span class="label">Add keyword filter</label></h1>							
							</a>					
						</div>
						<div class="parameter-placeholder col_2">
							<a href="open-group.php" class="parameter-edit modal-trigger">
								<h1>Open a saved filter group</h1>							
							</a>					
						</div>																
					</section>
				</article>				
				
				<div class="property-link">
					<span class="icon-river"></span>
					<span class="button-actions"><a href="#"><span class="icon-plus"></span></a></span>
				</div>				

				<span class="view-results button-blue"><a href="/markup/river">View your river</a></span>	
			</div>
		</div>
	</div>

<div id="modal-container">
	<div class="modal-window"></div>
	<div class="modal-fade"></div>
</div>

</body>
</html>