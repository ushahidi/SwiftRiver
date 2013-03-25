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
				<li class="active"><a href="#all" class="modal-close"><?php echo __("All your stuff"); ?></a></li>
				<li><a href="#river" class="modal-close"><?php echo __("Rivers"); ?></a></li>
				<li><a href="#bucket" class="modal-close"><?php echo __("Buckets"); ?></a></li>
				<li><a href="#form" class="modal-close"><?php echo __("Forms"); ?></a></li>
			</ul>

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
				<li class="active"><a href="#all"><?php echo __("All"); ?></a></li>
				<li><a href="#managing"><?php echo __("Managing"); ?></a></li>
				<li><a href="#following"><?php echo __("Following"); ?></a></li>
			</ul>

			<div class="container-toolbar cf">
				<span class="button">
					<a href="#" class="button-white delete-asset"><?php echo __("Delete"); ?></a>
				</span>
				<span class="button" class="button-white">
					<a href="#" class="button-white"><?php echo __("Duplicate"); ?></a>
				</span>
				<?php if ($owner): ?>
				<span class="button create-new">
					<a href="#" class="button-primary modal-trigger">
						<i class="icon-plus"></i>
						<?php echo __("Create new"); ?>
					</a>
				</span>
				<?php endif; ?>
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
	<td class="select-toggle">
	<% if (is_owner) { %>
		<input type="checkbox" />
	<% } %>
	</td>
	<td class="item-type">
		<span class="icon-<%= type %>"></span>
	</td>										
	<td class="item-summary">
		<h2><a href="<%= url %>"><%= display_name %></a></h2>
		<% if (type == "river") { %>
			<div class="metadata">
				<%= drop_count %> drops
				<% percent_full = (drop_count > 0) ? Math.round(drop_count/drop_quota) : 0 %>
				<span class="quota-meter">
					<span class="quota-meter-capacity">
						<span class="quota-total" style="width:<%= percent_full %>%;"></span>
					</span>
					<%= percent_full %>% full
				</span>
			</div>	
		<% } %>
	</td>
	<td class="item-categories">
	</td>
</script>

<script type="text/template" id="create-asset-modal-template">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white">
					<i class="icon-cancel"></i>
					<?php echo __("Close"); ?>
				</a>
				<h1><?php echo __("Create new"); ?></h1>
			</div>
			
			<div class="modal-body">
				<div class="base">
					<ul class="view-table">
						<li>
							<a href="#river" class="modal-transition">
								<span class="transition icon-arrow-right"></span>
								<i class="icon-river"></i>
								<?php echo __("River"); ?>
							</a>
						</li>
						<li>
							<a href="#bucket" class="modal-transition">
								<span class="transition icon-arrow-right"></span>
								<i class="icon-bucket"></i>
								<?php echo __("Bucket"); ?>
							</a>
						</li>

						<li>
							<a href="#form" class="modal-transition">
								<span class="transition icon-arrow-right"></span>
								<i class="icon-form"></i>
								<?php echo __("Custom form"); ?>
							</a>
						</li>
					</ul>
				</div>
			</div>
	</div>
</script>

<script type="text/template" id="create-form-modal-template">
	<div class="modal-title cf">
		<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
		<h1>Create a new custom form</h1>
	</div>

	<div class="modal-body">
		<div class="base">
			<h2 class="label">Basics</h2>
			<div class="modal-field">
				<h3 class="label">Form name</h3>
				<input type="text" placeholder="Name this custom form"  name="form_name" value="<%= name %>" />
			</div>
		</div>

		<div class="base">
			<h2 class="label">Fields</h2>
			<ul class="view-table">
				<!-- Field options will go here -->
				
				<li class="add"><a href="#create-river" class="modal-transition">Add a field</a></li>
			</ul>
		</div>
	
		<div class="modal-toolbar">
			<a href="#" class="button-submit button-primary">Create custom form</a>				
		</div>					
	</div>
</script>

<script type="text/template" id="add-field-modal-template">
	<div class="modal-title cf">
		<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
		<h1>Add a form field</h1>
	</div>
				
	<div class="modal-body modal-tabs-container">
		<div class="base">
			<ul class="modal-tabs-menu">
				<li class="active"><a href="#add-custom-text"><span class="label">Text input</span></a></li>
				<li><a href="#add-custom-list"><span class="label">Checkbox/List</span></a></li>
			</ul>
			<div class="modal-tabs-window">
				<!-- Fields will go here -->
			</div>						
		</div>

		<div class="modal-toolbar">
			<a href="#" class="button-submit button-primary">Add field</a>				
		</div>
	</div>
</script>

<script type="text/template" id="edit-field-modal-template">
	<div class="modal-title cf">
		<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
		<h1>Edit field</h1>
	</div>
				
	<div class="modal-body modal-tabs-container">
		<div class="base">
			<ul class="modal-tabs-menu">
			<!-- List of available channels goes here -->
			</ul>
			<div class="modal-tabs-window">
				<div id="add-twitter" class="active">
					<div class="modal-field modal-field-tabs-container">
						<ul class="modal-field-tabs-menu">
						</ul>
						<div class="modal-field-tabs-window">
						</div>
					</div>
				</div>														
			</div>
		</div>
		<div class="modal-toolbar">
			<a href="#" class="button-submit button-primary">Save field</a>				
		</div>					
	</div>
</script>

<script type="text/template" id="edit-text-field-modal-template">
	<div class="modal-field">
		<h3 class="label">Title</h3>
		<input type="text" name="title" placeholder="Enter a title for this field..." value="<%= title %>" />
		<label>
	</div>																
	<div class="modal-field">
		<h3 class="label">Description</h3>
		<input type="text" name="description" placeholder="Provide a description of this field..."  value="<%= description %>" />
		<label>
	</div>		
</script>

<script type="text/template" id="edit-list-field-modal-template">
	<div class="modal-field">
		<h3 class="label">Title</h3>
		<input type="text" name="title" placeholder="Enter a title for this field..."  value="<%= title %>" />
	</div>
		
	<div class="modal-field">
		<h3 class="label">Description</h3>
		<input type="text" name="description" placeholder="Provide a description of this field..." value="<%= description %>" />
	</div>
		
	<div class="modal-field">		
		<input type="checkbox" name="multi" <% if (type == "multiple") { %> checked <% } %> /> Allow multiple selection <br>
	</div>
	
	<div class="modal-field">
		<h3 class="label">Options</h3>
		<span class="option-field">
			<input type="text" placeholder="Provide an option..." />
			<a href="#" class="add-field remove-option"><span class="icon-cancel"></span></a>
			<a href="#" class="add-field add-option"><span class="icon-plus"></span></a>
		</span>
	</div>
</script>

<script type="text/template" id="text-field-template">
	<div class="modal-field">
		<h3 class="label"><%= title %></h3>
		<span class="remove icon-pencil"></span>
		<span class="remove icon-cancel"></span>
		<input type="text" placeholder="<%= description %>" disabled/>
	</div>
</script>

<script type="text/template" id="checkbox-field-template">
	<div class="modal-field">
		<h3 class="label"><%= title %></h3>
		<h4 class="label"><%= description %></h4>
		<span class="remove icon-pencil"></span>
		<span class="remove icon-cancel"></span>
	</div>																
</script>

<script type="text/template" id="checkbox-field-option-template">
	<input type="checkbox" disabled/> <%= option %> <br>
</script>

<script type="text/template" id="list-field-template">
	<div class="modal-field">
		<h3 class="label"><%= title %></h3>
		<h4 class="label"><%= description %></h4>	
		<span class="remove icon-pencil"></span>	
		<span class="remove icon-cancel"></span>
		<select>
			<option></option>
		</select>
	</div>																	
</script>

<script type="text/template" id="list-field-option-template">
	<option><%= option %></option>
</script>