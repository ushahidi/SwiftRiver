<div class="row controls list cf" id="collaborator-control">
	<h2><?php echo __('Collaborators'); ?></h2>
	<div class="data">
	</div>
	<div class="input">
		<h3><?php echo __('Add people to share with below'); ?></h3>
		<input type="text" placeholder="+ Type name..." id="add-collaborator-input" />
		<div id="livesearch">
			<ul></ul>
		</div>
	</div>
</div>


<script type="text/template" id="collaborator-template">
	<div class="content">
		<h1><a href="<?php echo url::site('user').'/<%= account_path %>' ?>" class="go"><%= name %></a></h1>
	</div>
	<% if (id != <?php echo $logged_in_user_id ?>) { %>
	<div class="summary">
		<section class="actions">
			<div class="button">
				<p class="button-change">
					<a class="delete">
						<span class="icon"></span>
						<span class="nodisplay"><?php echo __('Remove'); ?></span>
					</a>
				</p>
				<div class="clear"></div>
				<div class="dropdown container">
					<p><?php echo __('Are you sure you want to stop sharing with this person?'); ?></p>
					<ul>
						<li class="confirm"><a onclick=""><?php echo __('Yep'); ?></a></li>
						<li class="cancel"><a onclick=""><?php echo __('No, nevermind'); ?></a></li>
					</ul>
				</div>
			</div>
		</section>
		<section class="meta">
			<p>Editor</p>
		</section>
	</div>
	<% } %>
</script>

<script type="text/template" id="collaborator-search-result-template">
	<a><%= name %></a>
</script>


<script type="text/javascript">

$(function() {
	
	var fetch_url = "<?php echo $fetch_url ?>";
	
	// Collaborator model, collection and view
	var Collaborator = Backbone.Model.extend();
	
	var CollaboratorList = Backbone.Collection.extend({		
		model: Collaborator,		
		url: fetch_url
	});
	
	var CollaboratorView = Backbone.View.extend({
		
		tagName: "article",
		
		className: 	"item cf",
		
		template: _.template($("#collaborator-template").html()),
		
		events: {
			"click .actions .dropdown .confirm a": "removeCollaborator",	
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;	
		},
				
		removeCollaborator: function(e) {
			var viewItem = this;
			this.model.destroy({
				wait: true,
				success: function() {
					$(viewItem.el).fadeOut("slow");
				}
			});
			e.stopPropagation();
		}
	});
	
	// Initialize the list of collaborators.
	var Collaborators = new CollaboratorList;
	
	// Search result view
	var SearchResultView = Backbone.View.extend({
		
		tagName: "li",
		
		template: _.template($("#collaborator-search-result-template").html()),
		
		events: {
			"click a": "addCollaborator"
		},
		
		addCollaborator: function(e) {
			var viewItem = this.$el;
			Collaborators.create(this.model.toJSON(),{
				wait: true,
				success: function() {
					viewItem.fadeOut();
				}
			});
			e.stopPropagation();
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}	
	});
	
	// The collaborator control
	var CollaboratorControl = Backbone.View.extend({
		
		el: "#collaborator-control",
		
		events: {
			"click": "resetSearch",
			"keyup #add-collaborator-input": "liveSearch"
		},
		
		initialize: function() {
			Collaborators.on('add',	 this.addCollaborator, this);
			Collaborators.on('reset', this.addCollaborators, this);
			
			// Search results collection
			this.searchResults = new CollaboratorList;
			this.searchResults.on('add', this.addSearchResult, this);
			this.searchResults.on('reset', this.removeSearchResults, this);
		},
		
		// Clear the search input and remove the dropdown
		resetSearch: function(e) {
			if(e.isPropagationStopped()) return;
			this.$("#livesearch ul").html("");
			this.$("#add-collaborator-input").val("");
		},
		
		addCollaborator: function(collaborator) {
			var view = new CollaboratorView({model: collaborator});	
			this.$(".data").append(view.render().el);
		},
		
		addCollaborators: function() {
			Collaborators.each(this.addCollaborator, this);
		},
		
		// Display a search result
		addSearchResult: function(searchResult) {
			var view = new SearchResultView({model: searchResult});	
			this.$("#livesearch ul").append(view.render().el);
		},
		
		
		// Clear the search results
		removeSearchResults: function() {
			this.$("#livesearch ul").html("");
		},
		
		// Handle keyup event and only do an ajax request if there has been none in the last
		// 500ms
		liveSearch: function(e) {
			// Prevent initiating empty searches
			if (!((e.keyCode >= 16 && e.keyCode <= 90) || e.keyCode == 8))
				return;
			
			var view = this;
			var collaboratorsList = Collaborators; // Pass this on to the call back
			if(view.timer)
				clearTimeout(view.timer);
			}
			
			var doLiveSearch = this.doLiveSearch;
			view.timer = setTimeout(function() {
			        view.searchResults.reset();
			        if ($.trim(view.$("#add-collaborator-input").val())) {
			            doLiveSearch(view, collaboratorsList);
			        }
			        view.timer = null;
			    }, 500);
		},
		
		// Do the actual search and display of results
		doLiveSearch: function(view, collaboratorsList) {
			$.ajax({
			  url: fetch_url,
			  dataType: "json",
			  data: {
				q: 	view.$("#add-collaborator-input").val()
			  },
			  success: function(response){
				if (response.length) {
					// Remove already existing collaborators					
					var results = _.filter(response, function(searchResult) {						
						return ! _.find(collaboratorsList.toArray(), function(collaborator) { return collaborator.get('id') == searchResult["id"]});
					});
					
					// Feedback if no results
					if (!results.length) {
						view.$("#livesearch ul").html("<strong>no results<strong>");
					} else {
						// Add the search results if any to the live search view					
				    	_.each(results, function(searchResult) {
				    	    view.searchResults.add(searchResult, view);
				    	});
					}										
				} else {
					view.$("#livesearch ul").html("<strong>no results<strong>");
				}
			  }
			});
		}
	});
	
		
	// Bootstrap the list
	var collaboratorControl = new CollaboratorControl;
	Collaborators.reset(<?php echo $collaborator_list ?>);
});
	
</script>