<div id="content" class="settings collaborators cf">
	<div class="center">
		<div class="col_12">
			<div class="button-actions">
				<span><a href="#" class="modal-trigger"><i class="icon-users"></i>Add collaborator</a></span>
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
			<div class="property-title col_8">
				<a href="#" class="avatar-wrap"><img src="<%= avatar %>" /></a>
				<% if (read_only) { %>
					<h1><%= name %> (Viewer) </h1>
				<% } else { %>
					<h1><%= name %> (Editor) </h1>
				<% } %>							
			</div>
			<% if (id != logged_in_user) { %>
			<ul class="button-actions col_4">
				<% if (read_only) { %>
				<li class="button-blue button-small editor"><a href="#" title="<?php echo __('Editors have full access and can change settings.'); ?>">Make editor</a></li>
				<% } else { %>
				<li class="button-blue button-small viewer"><a href="#" title="<?php echo __('Viewers have read only access and cannot change settings.'); ?>">Make viewer</a></li>							
				<% } %>
				<li class="popover"><a href="#" class="popover-trigger"><span class="icon-remove"></span><span class="nodisplay">Remove</span></a>
					<ul class="popover-window popover-prompt base">
						<li class="destruct"><a href="#">Remove collaborator from river</a></li>
					</ul>							
				</li>
			</div>
			<% } %>						
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
					<span class="icon-cancel"></span>
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