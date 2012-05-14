<div id="content" class="settings collaborators cf">
	<div class="center">
		<div class="col_12">
			<div class="settings-toolbar">
				<p class="button-blue button-small create">
					<a href="#" class="modal-trigger">
						<span class="icon"></span>
						<?php echo __('ui.button.add.collaborator'); ?>
					</a>
				</p>
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
			<% } %>
			<div class="property-title">
				<a href="#" class="avatar-wrap"><img src="<%= avatar %>" /></a>
				<h1><%= name %> </h1>
			</div>
		</header>
	</article>	
</script>

<script type="text/template" id="collaborator-search-result-template">
	<a class="avatar-wrap"><img src="<%= avatar %>" /></span></a><%= name %>
</script>

<script type="text/template" id="collaborator-modal-template">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1><?php echo __('ui.button.add.collaborator'); ?></h1>
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
		<h2 style="display:none"><?php echo __('ui.button.add.collaborators'); ?></h2>
		<ul>
		</ul>
	</div>
</script>

<script type="text/javascript">

$(function() {
	
	var fetch_url = "<?php echo $fetch_url ?>";
	
	// Collaborator model, collection and view
	var Collaborator = Backbone.Model.extend({
		defaults: {
			collaborator_active: 0
		},
	});
	
	var CollaboratorList = Backbone.Collection.extend({		
		model: Collaborator,		
		url: fetch_url
	});
	
	var CollaboratorView = Backbone.View.extend({
		
		tagName: "article",
		
		className: "item cf",
		
		template: _.template($("#collaborator-template").html()),
		
		events: {
			"click a.remove-large": "removeCollaborator",	
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;	
		},
				
		removeCollaborator: function() {
			var viewItem = this;
			var message = this.model.get("name") + " is no longer a collaborator.";
			this.model.destroy({
				wait: true,
				success: function() {
					$(viewItem.el).fadeOut("slow");
					showConfirmationMessage(message);
				}
			});
			return false;
		}
	});
	
	// Initialize the list of collaborators.
	var collaborators = new CollaboratorList;
	
	// Search result view
	var SearchResultView = Backbone.View.extend({
		
		tagName: "li",
		
		template: _.template($("#collaborator-search-result-template").html()),
		
		events: {
			"click": "addCollaborator"
		},
		
		addCollaborator: function(e) {
			var viewItem = this.$el;
			collaborators.create(this.model.toJSON(),{
				wait: true,
				success: function() {
					viewItem.fadeOut();
				}
			});
			return false;
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}	
	});
	
	// The modal view for adding collaborators
	var AddCollaboratorsView = Backbone.View.extend({
		
		tagName: "article",
		
		className: "modal",
		
		template: _.template($("#collaborator-modal-template").html()),
		
		events: {
			"click": "resetSearch",
			"focusout input[name=search_box]": "resetSearch",
			"focus input[name=search_box]": "liveSearch",
			"keyup input[name=search_box]": "liveSearch"
		},
		
		initialize: function() {
			// Search results collection
			this.searchResults = new CollaboratorList;
			this.searchResults.on('add', this.addSearchResult, this);
			this.searchResults.on('reset', this.removeSearchResults, this);
			
			collaborators.on('add',	 this.addCollaborator, this);
		},
		
		render: function() {
			this.$el.html(this.template());
			
			// List of current collaborators
			//collaborators.each(this.addCollaborator, this)
			
			return this;	
		},
		
		addCollaborator: function(collaborator) {
			this.$("div.link-list h2").show();
			this.$("div.link-list ul").append("<li><a href=\"#\">" + collaborator.get("name") + "</a></li>");
		},
		
		focusSearchBox: function() {
			this.$("input[name=search_box]").focus();
		},
		
		// Clear the search input and remove the dropdown
		resetSearch: function() {
			this.$(".livesearch").fadeOut("slow").children("li").remove();
			this.$("#add-collaborator-input").val("");
		},
		
		// Display a search result
		addSearchResult: function(searchResult) {
			var view = new SearchResultView({model: searchResult});	
			this.$(".livesearch ul").append(view.render().el);
		},
		
		
		// Clear the search results
		removeSearchResults: function() {
			this.$(".livesearch ul").html("");
		},
		
		// Handle keyup event and only do an ajax request if there has been none in the last
		// 500ms
		liveSearch: function(e) {
			var view = this;
			var collaboratorsList = collaborators; // Pass this on to the call back
			if (view.timer) {
				clearTimeout(view.timer);
			}
			
			var doLiveSearch = this.doLiveSearch;
			var resetSearch = this.resetSearch;
			view.timer = setTimeout(function() {
			        view.searchResults.reset();
			        if ($.trim(view.$("input[name=search_box]").val())) {
			            doLiveSearch(view, collaboratorsList);
			        } else {
			            resetSearch();
					}
			        view.timer = null;
			    }, 500);
		},
		
		// Do the actual search and display of results
		doLiveSearch: function(view, collaboratorsList) {
			if (! view.loading_msg) {
				// If there isn't already another search in progress
				view.$(".livesearch").fadeIn().children("ul").html("Searching...");
			}
			$.ajax({
				url: fetch_url,
				dataType: "json",
				data: {
					q: 	view.$("input[name=search_box]").val()
				},
				
				success: function(response){
					if (response.length) {
						// Remove already existing collaborators					
						var results = _.filter(response, function(searchResult) {						
							return ! _.find(collaboratorsList.toArray(), function(collaborator) { 
								return collaborator.get('id') == searchResult["id"]
							});
						});
					
						// Feedback if no results
						if (!results.length) {
							view.$(".livesearch ul").html("<strong>no results<strong>");
						} else {
							// Add the search results if any to the live search view
							view.$(".livesearch ul").html("");
							_.each(results, function(searchResult) {
								view.searchResults.add(searchResult, view);
							});
						}
					} else {
						view.$(".livesearch ul").html("<strong>no results<strong>");
					}
				}
			});
		}
	});
	
	var CollaboratorsControl = Backbone.View.extend({
		
		el: "div.collaborators",
		
		events: {
			"click .settings-toolbar p.create a": "showAddCollaboratorsModal"
		},
		
		initialize: function() {
			collaborators.on('add',	 this.addCollaborator, this);
			collaborators.on('reset', this.addCollaborators, this);
			
			collaborators.on('reset', this.checkEmpty, this);
			collaborators.on('add', this.checkEmpty, this);
			collaborators.on('remove', this.checkEmpty, this);
		},
		
		showAddCollaboratorsModal: function() {
			var addCollaboratorsView = new AddCollaboratorsView({model: this.model});
			modalShow(addCollaboratorsView.render().el);
			addCollaboratorsView.focusSearchBox();
			return false;
		},
		
		addCollaborator: function(collaborator) {
			var view = new CollaboratorView({model: collaborator});	
			this.$(".col_12").append(view.render().el);
		},
		
		addCollaborators: function() {
			collaborators.each(this.addCollaborator, this);
		},
		
		checkEmpty: function() {
			if (collaborators.length) {
				this.$(".alert-message").fadeOut();
			} else {
				this.$(".alert-message").fadeIn();
			}
		}
				
	});
	
		
	// Bootstrap the list
	var collaboratorsControl = new CollaboratorsControl;
	collaborators.reset(<?php echo $collaborator_list ?>);
});
	
</script>