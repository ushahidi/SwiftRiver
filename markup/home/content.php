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
		<div id="page-views" class="col_12">
			<ul class="center">
				<li><a href="/markup/">Activity</a></li>
				<li class="active"><a href="/markup/home/content.php">Content</a></li>
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
							<li class="active"><a href="#" class="modal-close">All your stuff</a></li>
							<li><a href="#" class="modal-close">Rivers</a></li>
							<li><a href="#" class="modal-close">Buckets</a></li>
							<li><a href="#" class="modal-close">Custom forms</a></li>
						</ul>

						<div class="filters-type">
							<h2 class="">Categories</h2>
							<ul>
								<li><a href="#"><span class="mark" style="background-color:red;"></span><span class="total">1</span>Project 1</a></li>
								<li><a href="#"><span class="mark" style="background-color:orange;"></span><span class="total">1</span>Project 2</a></li>
								<li><a href="#"><span class="mark" style="background-color:green;"></span><span class="total">1</span>Project 3</a></li>
							</ul>
						</div>

						<div class="modal-toolbar">
							<a href="#" class="button-submit button-primary modal-close">Done</a>				
						</div>						
					</div>
				</div>					
			</section>

			<div class="col_9">
				<div class="container base">
					<div class="container-tabs">
						<ul class="container-tabs-menu cf">
							<li class="active"><a href="/_modules/table-rivers.php">All</a></li>
							<li><a href="/_modules/table-buckets.php">Managing</a></li>
							<li><a href="/_modules/table-buckets.php">Following</a></li>
						</ul>
						<div class="container-toolbar cf">
							<span class="button"><a href="#" class="button-white">Delete</a></span>
							<span class="button" class="button-white"><a href="#" class="button-white">Duplicate</a></span>
							<span class="button has-dropdown">
								<a href="#" class="button-white">Category<i class="icon-arrow-down"></i></a>
								<ul class="dropdown-menu">
									<li class="selected"><a href="#"><span class="mark" style="background-color:red;"></span>Project 1</a></li>
									<li><a href="#"><span class="mark" style="background-color:orange;"></span>Project 2</a></li>
									<li><a href="#"><span class="mark" style="background-color:green;"></span>Project 3</a></li>
								</ul>
							</span>
							<span class="button create-new"><a href="/markup/_modals/create.php" class="button-primary modal-trigger"><i class="icon-plus"></i>Create new</a></span>
						</div>
						<div class="container-tabs-window">
							<!-- NO RESULTS: Clear filters
							<div class="null-message">
								<h2>No rivers or buckets to show.</h2>
								<p>Clear active filters.</p>
							</div>
							--->
							
							<table>
								<tbody>
									<tr>
										<td class="select-toggle"><input type="checkbox" /></td>
										<td class="item-type">
											<span class="icon-river"></span>
										</td>
										<td class="item-summary">
											<h2><a href="/markup/river">2012 U.S. Election polling</a></h2>
											<div class="metadata">81 drops <span class="quota-meter"><span class="quota-meter-capacity"><span class="quota-total" style="width:22%;"></span></span>22% full</span></div>
										</td>
										<td class="item-categories">
											<span class="mark" style="background-color:red;"></span>
										</td>
									</tr>	
									<tr>
										<td class="select-toggle"><input type="checkbox" /></td>
										<td class="item-type">
											<span class="icon-river"></span>
										</td>
										<td class="item-summary">
											<h2><a href="/markup/river">Kenya election speech</a></h2>
											<div class="metadata">208 drops <span class="quota-meter"><span class="quota-meter-capacity"><span class="quota-total" style="width:42%;"></span></span>42% full</span></div>
										</td>
										<td class="item-categories">
											<span class="mark" style="background-color:red;"></span>
										</td>
									</tr>																	
									<tr>
										<td class="select-toggle"><input type="checkbox" /></td>
										<td class="item-type">
											<span class="icon-bucket"></span>
										</td>										
										<td class="item-summary">
											<h2><a href="/markup/bucket">Kenya election hate speech</a></h2>
											<div class="metadata">38 drops</div>											
										</td>
										<td class="item-categories">
											<span class="mark" style="background-color:orange;"></span>
											<span class="mark" style="background-color:green;"></span>
										</td>
									</tr>
									<tr>
										<td class="select-toggle"><input type="checkbox" /></td>
										<td class="item-type">
											<span class="icon-river"></span>
										</td>										
										<td class="item-summary">
											<h2><a href="/markup/river">Open Source software</a></h2>
											<div class="metadata">102 drops <span class="quota-meter"><span class="quota-meter-capacity"><span class="quota-total" style="width:31%;"></span></span>31% full</span></div>										
										</td>
										<td class="item-categories">
										</td>
									</tr>																										
									<tr>
										<td class="select-toggle"><input type="checkbox" /></td>
										<td class="item-type">
											<span class="icon-form"></span>
										</td>										
										<td class="item-summary">
											<h2><a href="/markup/_modals/settings-custom-form.php" class="modal-trigger">Speech type</a></h2>
											<div class="metadata">Custom form fields for drops</div>																				
										</td>
										<td class="item-categories">
											<span class="mark" style="background-color:green;"></span>
										</td>
									</tr>
									<tr>
										<td class="select-toggle"><input type="checkbox" /></td>
										<td class="item-type">
											<span class="icon-bucket"></span>
										</td>										
										<td class="item-summary">
											<h2><a href="/markup/river">Web development</a></h2>
											<div class="metadata">62 drops</div>											
										</td>
										<td class="item-categories">
										</td>
									</tr>									
								</tbody>
							</table>
						</div>
					</div>
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