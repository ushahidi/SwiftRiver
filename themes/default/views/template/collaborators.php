<div id="collaborators">
	<article class="base settings-category">
		<h1><?php echo __("Collaborators"); ?></h1>
		<ul class="view-table">
			<li class="add">
				<a href="#" class="modal-trigger"><?php echo __("Add collaborator"); ?></a>
			</li>
		</ul>
	</article>
</div>

<script type="text/template" id="collaborator-template">
	<span class="remove icon-cancel"></span>
	<img src="<%= avatar %>" class="avatar" />
	<%= name %>
</script>

<script type="text/template" id="collaborator-search-result-template">
	<article class="container base">
		<header class="cf">
			<div class="property-title">
				<a class="avatar-wrap"><img src="<%= avatar %>" /></span></a>
				<h1><%= name %> </h1>
			</div>
			<div class="actions">
				<ul class="dual-buttons">
					<li class="button-blue button-small editor"><a href="#" title="<?php echo __('Editors have full access and can change settings.'); ?>"><?php echo __('Add as editor'); ?></a></li>
					<li class="button-blue button-small viewer"><a href="#" title="<?php echo __('Viewers have read only access and cannot change settings.'); ?>"><?php echo __('Add as viewer'); ?></a></li>
				</ul>
			</div>
		</header>
	</article>
</script>

<script type="text/template" id="collaborator-modal-template">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white">
					<i class="icon-cancel"></i><?php echo __("Close"); ?>
				</a>
				<h1><?php echo __("Add collaborator"); ?></h1>
			</div>
			<div class="modal-body">
				<div class="base">
					<div class="modal-search-field">
						<?php echo Form::input('search_box', '', array('placeholder' => __("Find a user..."))); ?>
					</div>
					<div class="modal-search-results">
						<ul class="view-table"></ul>
					</div>
				</div>
				<div class="modal-toolbar">
					<a href="#" class="button-submit button-primary modal-close"><?php echo __("Done"); ?></a>
				</div>
			</div>
		</div>
	</div>
</script>

<script type="text/javascript">

$(function() {
	
	// Bootstrap the list
	var collaborators = new Collaborators.CollaboratorList;
	collaborators.url = "<?php echo $fetch_url ?>"
	var collaboratorsControl = new Collaborators.CollaboratorsControl({collection: collaborators});
	collaborators.reset(<?php echo $collaborator_list ?>);
});
	
</script>