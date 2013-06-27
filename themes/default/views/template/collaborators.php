<div id="collaborators" class="active">
	<article class="base settings-category">
		<h1>Collaborators</h1>

		<ul class="view-table">
			<li class="add"><a href="/markup/_modals/add-collaborator.php" class="modal-trigger">Add collaborator</a></li>
		</ul>

	</article>
</div>


<script type="text/template" id="collaborator-template">
	<span class="remove icon-cancel"></span>
	<img src="<%= owner.avatar %>" class="avatar" />
	<%= owner.name %>
</script>

<script type="text/template" id="collaborator-search-result-template">
	<span class="select editor icon-plus"></span>
	<img src="<%= owner.avatar %>" class="avatar" ><%= owner.name %>
</script>

<script type="text/template" id="collaborator-modal-template">
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
			<div class="modal-search-results livesearch">
				<ul class="view-table"></ul>
			</div>
		</div>
	
		<div class="modal-search-results added nodisplay">
			<h2 class="label">People you have just made collaborators...</h2>
			<ul class="view-table">
			</ul>
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