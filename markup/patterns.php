<!DOCTYPE html> 
<html> 
 
<head> 
    <meta charset="utf-8"> 
	<title>SwiftRiver ~ Pattern portfolio</title> 
	<link rel="shortcut icon" href="#">
	<link type="text/css" href="/themes/default/media/css/styles.css" rel="stylesheet" />
	<style>
header {
   text-align: left;
   padding: 1.046666667%;
}

header h1 {
   color: #444444;
   font-size: 3em;
}

body > nav {
   text-align: left;
   padding: 0 1.046666667%;
}

body > nav li {
   font-size: 1.1em;
   list-style-type: disc;
   padding: 3px 0;
   margin-left: 20px;
}

.sample {
   padding: 20px 0;
}

.sample > [class^="col_"], 
.sample> [class*=" col_"] {
   margin-top: 15px;
}

.sample > h2 {
   color: #fff;
   text-align: left;
   background: #444444;
   padding: 1.046666667%;
   margin: 10px 0;
}

.sample > h2 em {
   color: #dedede;
   font-weight: normal;
   font-size: 0.8em;
}
	</style>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="/markup/_js/jquery.outside.js"></script>
	<script type="text/javascript" src="/markup/_js/global.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head> 
 
<body> 

<header>
	<h1>SwiftRiver pattern portfolio</h1>
</header>
	
<nav>
	<ul>
		<li><a href="#buttons">Buttons</a></li>
		<li><a href="#view-table">Table view</a></li>
		<li><a href="#page-title">Page title</a></li>
		<li><a href="#page-title-user">Page title, user</a></li>
		<li><a href="#page-navigation">Top-level navigation</a></li>
		<li><a href="#filter-views">Filter views</a></li>
		<li><a href="#activity-items">Activity items</a></li>
		<li><a href="#content-table">Content table</a></li>
		<li><a href="#containers-page">Page containers</a></li>
		<li><a href="#containers-modal">Modal containers</a></li>
		<li><a href="#drop-drops">Drop: In 'drops' view</a></li>
		<li><a href="#drop-list">Drop: In 'list' view</a></li>
	</ul>
</nav>

<div class="sample">
	<a name="buttons"></a>
	<h2>Buttons: <em>Various styles for different functions</em></h2>
	<div class="col_3">
		<a href="#" class="button-primary">Button primary</a></p>
	</div>
	<div class="col_3">
		<a href="#" class="button-primary"><i class="icon-plus"></i>Button primary, icon</a></p>
	</div>
	<div class="col_3">
		<a href="#" class="button-primary selected">Button primary, selected</a></p>
	</div>
	<div class="col_3">
		<a href="#" class="button-white">Button secondary</a>
	</div>
</div>

<div class="sample cf">
	<a name="view-table"></a>
	<h2>Table view: <em>Various patterns for listing items</em></h2>
	<div class="col_4">
		<!-- List of links -->
		<div class="base">
			<ul class="view-table">
				<li><a href="#">First item</a></li>
				<li><a href="#">Second item</a></li>
				<li><a href="#">Third item</a></li>
				<li class="add"><a href="#add-item">Add item</a></li>
			</ul>
		</div>
	</div>
	<div class="col_4">
		<!-- List static items that are selectable -->
		<div class="base">
			<ul class="view-table">
				<li class="static cf">
					<span class="select icon-plus"></span>
					First item
				</li>
				<li class="static selected cf">
					<span class="select icon-plus"></span>
					Second item, selected
				</li>
				<li class="static cf">
					<span class="select icon-plus"></span>
					Third item
				</li>				
				<li class="add"><a href="#add-item">Add item</a></li>
			</ul>
		</div>
	</div>
	<div class="col_4">
		<!-- List of links with optional 'remove' target -->
		<div class="base">
			<ul class="view-table">
				<li>
					<a href="#">
					<span class="remove icon-cancel"></span>
					First item
					</a>
				</li>
				<li>
					<a href="#">
					<span class="remove icon-cancel"></span>
					Second item
					</a>
				</li>
				<li>
					<a href="#">
					<span class="remove icon-cancel"></span>
					Third item
					</a>
				</li>								
				<li class="add"><a href="#add-item">Add item</a></li>
			</ul>
		</div>
	</div>		
</div>

<div class="sample">
	<a name="page-title"></a>
	<h2>Page title: <em>Used on rivers and buckets</em></h2>
	<hgroup class="page-title cf">
		<div class="center">
			<div class="col_9">
				<h1>Page title</h1>
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
</div>

<div class="sample">
	<a name="page-title-user"></a>
	<h2>Page title, user: <em>Used on user-related pages</em></h2>
	<hgroup class="page-title user-title cf">
		<div class="center">
			<div class="col_9">		
				<a class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_bigger.jpeg" class="avatar" /></a>
				<h1>Full name</h1>
				<h2 class="label">username</h2>
			</div>
			<div class="page-action col_3">
				<span class="follow-total"><a href="#"><strong>28</strong> followers</a>, <a href="#"><strong>18</strong> following</a></span>
				<span class="button-follow"><a href="#" class="button-primary selected"><i class="icon-checkmark"></i>Following</a></span>
			</div>
		</div>
	</hgroup>
</div>

<div class="sample">
	<a name="page-navigation"></a>
	<h2>Top-level navigation: <em>Used to navigate sections with multiple top-level pages</em></h2>
	<nav class="page-navigation cf">
		<div class="center">
				<ul class="col_12">
					<li class="active"><a href="#">First item</a></li>
					<li><a href="#">Second item</a></li>
				</ul>
			</div>
		</div>
	</nav>
</div>

<div class="sample cf">
	<a name="filter-views"></a>
	<h2>Filter views: <em>Various patterns for lists of filter/menu items</em></h2>
	<div class="col_3">
		<!-- Primary items -->
		<ul class="filters-primary">
			<li class="active"><a href="#first-item"><span class="total">204</span> First item</a></li>
			<li><a href="#second-item">Second item</a></li>
			<li><a href="#third-item">Third item</a></li>
			<li><a href="#fourth-item">Fourth item</a></li>
		</ul>

	</div>
	<div class="col_3">
		<!-- Generic items -->
		<div class="filters-type">
			<ul>
				<li><a href="#"><span class="total">39</span> First item</a></li>
				<li><a href="#"><span class="total">165</span> Second item</a></li>
			</ul>
		</div>

	</div>
	<div class="col_3">
		<!-- Generic items, grouped and labeled -->
		<div class="filters-type">
			<h2 class="">Filter type</h2>
			<ul>
				<li><a href="#"><span class="mark" style="background-color:red;"></span><span class="total">1</span>First item</a></li>
				<li><a href="#"><span class="mark" style="background-color:orange;"></span><span class="total">1</span>Second item</a></li>
				<li><a href="#"><span class="mark" style="background-color:green;"></span><span class="total">1</span>Third item</a></li>
			</ul>
		</div>

	</div>
	<div class="col_3">
		<!-- Generic items, grouped, labeled and toggled visibility -->
		<div class="filters-type">
			<span class="toggle-filters-display"><span class="total">5</span><span class="icon-arrow-down"></span><span class="icon-arrow-up"></span></span>				
			<span class="filters-type-settings"><a href="/markup/_modals/settings-channels.php" class="modal-trigger"><span class="icon-cog"></span></a></span>
			<h2>Filter type</h2>
			<div class="filters-type-details">
				<ul>
					<li class="active"><a href="#"><i class="icon-twitter"></i><span class="total">28</span> First item</a></li>
					<li class="active"><a href="#"><i class="icon-facebook"></i><span class="total">61</span> Second item</a></li>
					<li class="active"><a href="#"><i class="icon-rss"></i><span class="total">83</span> Third item</a></li>
				</ul>
			</div>
		</div>

	</div>		
</div>

<div class="sample cf">
	<a name="activity-items"></a>
	<h2>Activity items: <em>Used in users' activity view</em></h2>
	<div class="col_9">
		<div id="news-feed" class="container base">
		
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

<div class="sample cf">
	<a name="content-table"></a>
	<h2>Content table: <em>Used in users' content view</em></h2>
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
						</tbody>
					</table>
				</div>
			</div>
		</div>

	</div>
</div>

<div class="sample cf">
	<a name="containers-page"></a>
	<h2>Page containers: <em>Typically used for settings views</em></h2>
	<div class="col_9">
		<article class="base settings-category">
			<h1>Container title</h1>
			<div class="body-field">
				<h3 class="label">Field label</h3>
				<input type="text" value="Field value" />
			</div>
			<!-- Toolbar, only if necessary -->
			<div class="settings-category-toolbar">
				<a href="#" class="button-submit button-primary modal-close">Button primary</a>
				<a href="#" class="button-destruct button-secondary">Secondary option</a>						
			</div>																								
		</article>
		
		<article class="base settings-category">
			<h1>Container title, with tabeview</h1>

			<ul class="view-table">
				<li class="static cf">
					<span class="remove icon-cancel"></span>
					<i class="channel-icon icon-twitter"></i>First item <em>Description</em>
				</li>
				<li class="static cf">
					<span class="remove icon-cancel"></span>
					Second item <em>Description</em>
				</li>				
				<li class="add"><a href="/markup/_modals/add-service.php" class="modal-trigger">Add list item</a></li>
			</ul>

		</article>		
	</div>
</div>

<div class="sample cf">
	<a name="containers-modal"></a>
	<h2>Modal containers: <em>Typically used for managing content</em></h2>
	<div class="col_4">
		<article class="modal">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1>Window title</h1>
			</div>
			<div class="modal-body">
				<div class="base">
					<div class="modal-field">
						<h3 class="label">Field label</h3>
						<input type="text" placeholder="Placeholder text" />
					</div>
				</div>

				<div class="base modal-tabs-container">
					<h2 class="label">Container title</h2>
					<ul class="modal-tabs-menu">
						<li><a href="#add-twitter"><span class="channel-icon icon-twitter"></span></a></li>
						<li><a href="#add-facebook"><span class="channel-icon icon-facebook"></span></a></li>
						<li><a href="#add-rss"><span class="channel-icon icon-rss"></span></a></li>
						<li><a href="#add-email"><span class="channel-icon icon-mail"></span></a></li>
					</ul>
					<div class="modal-tabs-window">
						<div class="active"></div>
						
						<!-- ADD Twitter -->
						<div id="add-twitter">
							<div class="modal-field modal-field-tabs-container">
								<ul class="modal-field-tabs-menu">
									<li class="active"><a href="#input-keywords">Keywords</a></li>
									<li><a href="#input-users">Users</a></li>
									<li><a href="#input-location">Location</a></li>
								</ul>
								<div class="modal-field-tabs-window">
									<div id="input-keywords" class="active">
										<a href="#" class="add-field"><span class="icon-plus"></span></a>									
										<input type="text" placeholder="Enter keywords, separated by commas" />
									</div>
									<div id="input-users">
										<a href="#" class="add-field"><span class="icon-plus"></span></a>									
										<input type="text" placeholder="Enter usernames, separated by commas" />
									</div>
									<div id="input-location">
										<a href="#" class="add-field"><span class="icon-plus"></span></a>									
										<input type="text" placeholder="Enter location" />
										<select style="display:block;">
											<option>within 100km</option>
											<option>within 1000km</option>
										</select>
									</div>																				
								</div>
								
								<!-- IF: Parameter added
								<div class="modal-field-parameter">									
									<select style="display:block;">
										<option>AND</option>
										<option>OR</option>
									</select>
									
									<input type="text" value="SXSW" />
								</div>								
								-->
							</div>
						</div>

						<!-- ADD Facebook -->
						<div id="add-facebook">
							<div class="modal-field">
								<h3 class="label">Facebook Page name</h3>
								<a href="#" class="add-field"><span class="icon-plus"></span></a>
								<input type="text" placeholder="Enter the name of a Facebook page" />
								<!-- IF: Parameter added
								<div class="modal-field-parameter">									
									<select style="display:block;">
										<option>AND</option>
										<option>OR</option>
									</select>
									
									<input type="text" value="SXSW" />
								</div>								
								-->
							</div>
						</div>
						
						<!-- ADD RSS -->
						<div id="add-rss">
							<div class="modal-field">
								<h3 class="label">RSS URL</h3>
								<a href="#" class="add-field"><span class="icon-plus"></span></a>
								<input type="text" placeholder="Enter the address to an RSS feed" />
							</div>
						</div>

						<!-- ADD EMAIL -->
						<div id="add-email">
							<div class="modal-field">
								<h3 class="label">Email address</h3>
								<input type="text" placeholder="Enter your full email address" />
							</div>
							<div class="modal-field">
								<h3 class="label">Password</h3>
								<input type="password" />
							</div>								
						</div>																					
					</div>
				</div>
				
				<div class="modal-toolbar">
					<a href="#" class="button-submit button-primary modal-close">Save button</a>				
				</div>					
			</div>
		</article>	
	</div>

	<div class="col_4">
		<article class="modal">
			<div class="modal-title cf">
				<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
				<h1>Second-level title</h1>
			</div>
			
			<div class="modal-body">
				<div class="base">
					<div class="modal-field">
						<select>
							<option>Title</option>
							<option>Content</option>
							<option>Source</option>
							<option>Lorem ipsum</option>
						</select>
						
						<select>
							<option>contains</option>
							<option>is</option>
							<option>does not contain</option>
						</select>
						
						<input type="text" placeholder="Enter keywords..." />
					</div>																
				</div>
				<div class="modal-toolbar">
					<a href="#" class="button-submit button-primary modal-back">Add rule</a>				
				</div>						
			</div>
		</article>
	</div>
	
	<div class="col_4">
		<article class="modal">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1>Add via search</h1>
			</div>
			
			<div class="modal-body">
				<div class="base">
					<div class="modal-search-field">
						<input type="text" placeholder="Find a user..." />
						<a href="#" class="button-submit"><span class="icon-search"></span></a>
					</div>
					
					<div class="modal-search-results">
						<ul class="view-table">
							<li class="static selected user cf">
								<span class="select icon-plus"></span>
								<img src="https://si0.twimg.com/profile_images/2525445853/TweetLandPhoto_normal.jpg" class="avatar">Juliana Rotich
							</li>
							<li class="static user cf">
								<span class="select icon-plus"></span>
								<img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_normal.jpeg" class="avatar">Nathaniel Manning
							</li>
						</ul>													
					</div>
				</div>
				<div class="modal-toolbar">
					<a href="#" class="button-submit button-primary modal-close">Done</a>				
				</div>		
		</article>
	</div>		
</div>

<div class="sample cf">
	<a name="drop-drops"></a>
	<h2>Drop: <em>As seen in 'drops' view</em></h2>
	<div class="drops col_3">
		<article class="drop base">
			<a href="/markup/drop" class="drop-image-wrap zoom-trigger"><img src="http://omwenga.files.wordpress.com/2012/09/raila_at_ease1.jpg?w=645" class="drop-image" /></a>
			<h1><a href="/markup/drop/" class="zoom-trigger">Response To More Rants and Raves From A Well Known Raila Hater and Basher</a></h1>				
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
			<section class="drop-source cf">
				<a href="#" class="avatar-wrap"><img src="http://0.gravatar.com/avatar/0c6078b8694a5c8c2385ab7ba4a1f81b?s=200&r=pg&d=mm" /></a>
				<div class="byline">
					<h2>Uchambuzi Tanaka</h2>
					<p class="drop-source-channel"><a href="#"><span class="icon-rss"></span>via RSS</a></p>
				</div>
			</section>
		</article>
	</div>
</div>

<div class="sample cf">
	<a name="drop-list"></a>
	<h2>Drop: <em>As seen in 'list' view</em></h2>
	<div class="list col_9">
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
	</div>
</div>

</body>
</html>