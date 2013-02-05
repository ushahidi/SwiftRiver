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
					<li><a href="#"><span class="mark" style="background-color:orange;"></span><span class="total">10</span>Project 2</a></li>
					<li><a href="#"><span class="mark" style="background-color:green;"></span><span class="total">3</span>Project 3</a></li>
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
				-->
							
				<table>
					<tbody id="asset-list">
						<!-- List of river, buckets go here -->									
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/template" id="asset-template">
	<td class="select-toggle"><input type="checkbox" /></td>
	<td class="item-type">
		<span class="icon-<%= asset_type %>"></span>
	</td>										
	<td class="item-summary">
		<h2><a href="/markup/river"><%= display_name %></a></h2>
		<% if (asset_type == "river") { %>
		<div class="metadata"><%= drop_count %> drops <span class="quota-meter"><span class="quota-meter-capacity"><span class="quota-total" style="width:<%= Math.round(drop_count/drop_quota) %>%;"></span></span><%= Math.round(drop_count/drop_quota) %>% full</span></div>										
		<% } %>
	</td>
	<td class="item-categories">
	</td>
</script>
