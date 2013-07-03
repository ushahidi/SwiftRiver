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
</script>

<script type="text/template" id="create-form-modal-template">
	<div class="modal-title cf">
		<% if (isNew) { %>
			<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
			<h1><?php echo __("Create a new custom form"); ?></h1>
		<% } else { %>
			<a href="#" class="modal-close button-white"><span class="icon-cancel"></span></a>
			<h1><?php echo __("Edit form"); ?></h1>
		<% } %>
	</div>

	<div class="modal-body">
		<div class="base">
			<h2 class="label"><?php echo __("Basics"); ?></h2>
			<div class="modal-field">
				<h3 class="label"><?php echo __("Form name"); ?></h3>
				<input type="text" placeholder="<?php echo __("Name this custom form"); ?>"  name="form_name" value="<%= name %>" />
			</div>
		</div>

		<div class="base">
			<h2 class="label"><?php echo __("Fields"); ?></h2>
			<ul class="view-table">
				<!-- Field options will go here -->
				
				<li class="add"><a href="#create-river" class="modal-transition"><?php echo __("Add a field"); ?></a></li>
			</ul>
		</div>
	
		<div class="modal-toolbar">
			<% if (isNew) { %>
				<a href="#" class="button-submit button-primary"><?php echo __("Create custom form"); ?></a>
			<% } else { %>
				<a href="#" class="button-submit button-primary"><?php echo __("Save"); ?></a>
			<% } %>
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
		<% if (!isLive) { %>
			<span class="remove icon-pencil"></span>
			<span class="remove icon-cancel"></span>
		<% } %>
		<input type="text" placeholder="<%= description %>" <% if (!isLive) { %> disabled <% } %> />
	</div>
</script>

<script type="text/template" id="checkbox-field-template">
	<div class="modal-field">
		<h3 class="label"><%= title %></h3>
		<h4 class="label"><%= description %></h4>
		<% if (!isLive) { %>
			<span class="remove icon-pencil"></span>
			<span class="remove icon-cancel"></span>
		<% } %>
	</div>
</script>

<script type="text/template" id="checkbox-field-option-template">
	<input type="checkbox" <% if (!isLive) { %> disabled <% } %> value="<%= option %>" /> <%= option %> <br>
</script>

<script type="text/template" id="list-field-template">
	<div class="modal-field">
		<h3 class="label"><%= title %></h3>
		<h4 class="label"><%= description %></h4>
		<% if (!isLive) { %>
			<span class="remove icon-pencil"></span>
			<span class="remove icon-cancel"></span>
		<% } %>
		<select>
			<option></option>
		</select>
	</div>
</script>

<script type="text/template" id="list-field-option-template">
	<option><%= option %></option>
</script>