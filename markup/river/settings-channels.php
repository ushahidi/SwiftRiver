<?php
	$page_title = "Ushahidi at SXSW";
	$template_type = "settings";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_12">
				<h1><a href="/markup/river/"><?php print $page_title; ?></a> <em>settings</em></h1>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<div class="center">
			<div id="page-views" class="settings touchcarousel col_12">
				<ul class="touchcarousel-container">
					<li class="touchcarousel-item active"><a href="/markup/river/settings-channels.php">Flow</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/settings-collaborators.php">Collaborators</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/settings-display.php">Display</a></li>
					<li class="touchcarousel-item"><a href="/markup/river/settings-permissions.php">Permissions</a></li>
				</ul>
			</div>
		</div>
	</nav>

	<div id="content" class="settings flow cf">
		<div class="center">
			<div class="col_12">	
				<!-- ALTERNATE MESSAGE WHEN THERE ARE NO CHANNELS //
				<div class="alert-message blue">
					<p><strong>No more channels.</strong> You can flow new channels into your river by selecting the "Add channel" button above.</p>
				</div>
				// END MESSAGE -->
	
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
						<div class="parameter-summary col_2">
							<a href="edit-channel-twitter.php" class="parameter-edit modal-trigger">
								<h1><span class="icon-twitter"></span><span class="nodisplay">Twitter</span></h1>							
								<p class="parameter-data">SXSW, Ushahidi</p>
							</a>
							<span href="#" class="parameter-status parameter-control cf"><a href="#"></a></span>																		
							<a href="#" class="remove-parameter parameter-control"><i class="icon-cancel"></i><span class="nodisplay">Remove</span></a>
						</div>	
						<div class="parameter-summary col_2">
							<a href="edit-channel-rss.php" class="parameter-edit modal-trigger">
								<h1><span class="icon-rss"></span><span class="nodisplay">RSS</span></h1>							
								<p class="parameter-data">Mashable news headlines</p>
							</a>
							<span href="#" class="parameter-status parameter-control cf"><a href="#"></a></span>																		
							<a href="#" class="remove-parameter parameter-control"><i class="icon-cancel"></i><span class="nodisplay">Remove</span></a>
						</div>
						<div class="parameter-summary col_2">
							<a href="#" class="parameter-edit modal-trigger">
								<h1><span class="icon-facebook"></span><span class="nodisplay">Facebook</span></h1>							
								<p class="parameter-data">SXSW</p>
							</a>
							<span href="#" class="parameter-status parameter-control cf"><a href="#"></a></span>																		
							<a href="#" class="remove-parameter parameter-control"><i class="icon-cancel"></i><span class="nodisplay">Remove</span></a>
						</div>
						<div class="parameter-summary closed col_2">
							<a href="edit-channel-rss.php" class="parameter-edit modal-trigger">
								<h1><span class="icon-rss"></span><span class="nodisplay">RSS</span></h1>							
							</a>
							<span href="#" class="parameter-status parameter-control cf"><a href="#"></a></span>																		
							<a href="#" class="remove-parameter parameter-control"><i class="icon-cancel"></i><span class="nodisplay">Remove</span></a>
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
							<h1>Acme filter group</h1>
						</div>
						<ul class="button-actions col_6">
							<li class="popover"><a href="#" class="popover-trigger"><i class="icon-plus"></i>Add filter</a>
								<ul class="popover-window base">
									<li><a href="#">Keyword</a></li>
									<li><a href="#">Date</a></li>
								</ul>
							</li>
							<li><a href="edit-filter-group.php" class="modal-trigger"><i class="icon-settings"></i>Group settings</a></li>
							<li class="popover"><a href="#" class="popover-trigger"><span class="icon-remove"></span><span class="nodisplay">Remove</span></a>
								<ul class="popover-window popover-prompt base">
									<li><a href="#">Remove from river</a></li>
									<li class="destruct"><a href="#">Remove and delete from saved filters</a></li>
								</ul>							
							</li>
						</ul>						
					</header>
					<section class="property-parameters cf">						
						<div class="parameter-summary col_2">
							<a href="edit-filter-date.php" class="parameter-edit modal-trigger">
								<h1><span class="icon-calendar"></span><span class="nodisplay">Date</span></h1>							
								<p class="parameter-data">March 26, 2012 to <br />June 4, 2012</p>
							</a>					
							<span href="#" class="parameter-status parameter-control cf"><a href="#"></a></span>																		
							<a href="#" class="remove-parameter parameter-control"><i class="icon-cancel"></i><span class="nodisplay">Remove</span></a>
						</div>
						
						<div class="parameter-summary col_2">
							<a href="edit-filter-keyword.php" class="parameter-edit modal-trigger">
								<h1><span class="icon-pencil-2"></span><span class="nodisplay">Keyword</span></h1>
								<p class="parameter-data">SwiftRiver</p>						
							</a>						
							<span href="#" class="parameter-status parameter-control cf"><a href="#"></a></span>											
							<a href="#" class="remove-parameter parameter-control"><i class="icon-cancel"></i><span class="nodisplay">Remove</span></a>
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

	<footer id="global-footer"></footer>

	<div id="modal-container">
		<div class="modal-window"></div>
		<div class="modal-fade"></div>
	</div>

</body>
</html>