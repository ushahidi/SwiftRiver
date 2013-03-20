<div id="rules" class="active">
	<article class="base settings-category">
		<hgroup>
			<h1><?php echo __("Rules"); ?></h1>
		</hgroup>
						
		<ul class="view-table" id="rules-list">
			<li class="add"><a href="#" class="modal-trigger"><?php echo __("Add rule"); ?></a></li>
		</ul>

	</article>
</div>

<script type="text/template" id="rules-item-template">
<a href="#" class="modal-trigger">
	<span class="remove icon-cancel"></span>
	<%= name %>
</a>
</script>

<script type="text/template" id="create-rule-modal-template">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white">
					<i class="icon-cancel"></i><?php echo __("Close"); ?>
				</a>
				<h1>
					<% if (name != undefined && name.length > 0) { %>
						<%= name %>
					<% } else { %>
						 <?php echo __("Add rule"); ?>
					<% } %>
				</h1>
			</div>
		
			<div class="modal-body">
				<div class="base">
					<h2 class="label"><?php echo __("Name"); ?></h2>
					<div class="modal-field">
						<input type="text" name="rule_name" value="<%= name %>" placeholder="Name your new rule" id="rule_name" />
					</div>					
				</div>
			
				<div class="view-table base">
					<h2 class="label"><?php echo __("Conditions"); ?></h2>
					<ul id="rule-conditions">
						<li class="add">
							<a href="#add-condition" class="modal-transition"><?php echo __("Add condition"); ?></a>
						</li>
					</ul>
				</div>
			
				<div class="view-table base">
					<h2 class="label"><?php echo __("Actions"); ?></h2>
					<ul id="rule-actions">
						<li class="add">
							<a href="#add-actions" class="modal-transition"><?php echo __("Add actions"); ?></a>
						</li>
					</ul>
				</div>
			
				<div class="modal-toolbar">
					<a href="#" class="button-submit button-primary modal-close"><?php echo __("Done"); ?></a>
				</div>
			</div>
		</div>
		
		<div id="modal-secondary" class="modal-view">
		</div>
	</div>
</script>

<script type="text/template" id="edit-rule-action-template">
	<div class="modal-title cf">
		<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
		<h1><?php echo __("Add actions") ?></h1>
	</div>

	<div class="modal-body">
		<div class="base">
			<h2 class="label"><?php echo __("Remove"); ?></h2>
			<div class="modal-field">
				<label>
					<% checked = (removeFromRiver != undefined && removeFromRiver == true); %>
					<input type="checkbox" name="remove_from_river" id="remove_from_river" <% if (checked) { %> checked="true" <% } %>/>
					<?php echo __("Remove from river"); ?>
				</label>
			</div>
		</div>

		<div class="base">
			<h2 class="label"><?php echo __("Change status"); ?></h2>
			<div class="modal-field">
				<label>
					<% checked = (markAsRead != undefined && markAsRead == true); %>
					<input type="checkbox" name="mark_as_read", id="mark_as_read" <% if (checked) { %> checked="true" <% } %>/>
					<?php echo __("Mark as read"); ?>
				</label>
			</div>
		</div>

		<div class="base buckets-list">
			<h2 class="label"><?php echo __("Add to bucket"); ?></h2>
			<ul class="view-table"></ul>
		</div>

		<div class="modal-toolbar">
			<a href="#" class="button-submit button-primary"><?php echo __("Done"); ?></a>
		</div>
	</div>	
</script>

<script type="text/template" id="edit-rule-condition-template">
	<div class="modal-title cf">
		<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
		<h1><?php echo __("Add condition"); ?></h1>
	</div>

	<div class="modal-body">
		<div class="base">
			<div class="modal-field">
				<?php echo Form::select('condition_field', $condition_fields, '', array('id' => 'condition_field')); ?>
				<?php echo Form::select('condition_operators', $condition_operators, '', array('id' => 'condition_operator')); ?>
				<% value = (value == null)? "" : value; %>
				<input type="text" name="condition_value" id="condition_value" value="<%= value %>" placeholder="<?php echo __("Enter keywords..."); ?>" />
			</div>
		</div>
		<div class="modal-toolbar">
			<a href="#" class="button-submit button-primary"><?php echo __("Done"); ?></a>
		</div>
	</div>	
</script>

<script type="text/template" id="rule-condition-item-template">
	<a href="#" class="modal-transition">
		<span class="remove icon-cancel"></span>
		<%= field %> <%= operator %> <em><%= value %></em>
	</a>
</script>

<script type="text/template" id="rule-action-item-template">
	<a href="#" class="modal-transition">
	<span class="remove icon-cancel"></span>
		<%= label %>
	</a>
</script>

<script type="text/template" id="bucket-item-template">
	<span class="select icon-plus"></span>
	<%= name %>
</script>

<?php echo $rules_js; ?>