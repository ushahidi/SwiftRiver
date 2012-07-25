<div id="content" class="settings collaborators cf">
	<div class="center">
		<div class="col_12">
			<div class="settings-toolbar">
				<p class="button-blue button-small create"><a href="#" class="modal-trigger"><span class="icon"></span>Add collaborator</a></p>
			</div>
			
			<div class="alert-message blue" style="display:none;">
				<p><strong>No collaborators.</strong> You can add collaborators by selecting the "Add collaborator" button above.</p>
			</div>
			
		</div>
	</div>
</div>

<script type="text/template" id="collaborator-template">
	<article class="container base">
		<header class="cf">
			<% if (id != logged_in_user) { %>
			<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay">Remove</span></a>
			<div class="actions">
				<% if (read_only) { %>
					<p class="button-blue button-small editor"><a href="#" title="<?php echo __('Editors have full access and can change settings.'); ?>">Make editor</a></p>
				<% } else { %>
					<p class="button-blue button-small viewer"><a href="#" title="<?php echo __('Viewers have read only access and cannot change settings.'); ?>">Make viewer</a></p>
				<% } %>
			</div>
			<% } %>
			<div class="property-title">
				<a href="#" class="avatar-wrap"><img src="<%= avatar %>" /></a>
				<% if (read_only) { %>
					<h1><%= name %> (Viewer) </h1>
				<% } else { %>
					<h1><%= name %> (Editor) </h1>
				<% } %>
			</div>
		</header>
	</article>	
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
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1>Add collaborator</h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon"></span>
					Close
				</a>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body create-new">
		<form>
			<h2>Invite a person to collaborate on this river</h2>
			<div class="field">
				<input type="text" placeholder="Type name or email address" class="name" name="search_box" />
				<div class="livesearch">
					<ul></ul>
				</div>
			</div>
		</form>
	</div>

	<div class="modal-body link-list">
		<h2 style="display:none">Added collaborators</h2>
		<ul>
		</ul>
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