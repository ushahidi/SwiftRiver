/**
 * Collaborators module
 */
(function (root) {
	
	// Init the module
	Collaborators = root.Collaborators = {};
	
	// Collaborator model, collection and view
	var Collaborator = Backbone.Model.extend({
		defaults: {
			collaborator_active: 0,
			read_only: false
		},
	});

	var CollaboratorList = Collaborators.CollaboratorList = Backbone.Collection.extend({		
		model: Collaborator
	});

	var CollaboratorView = Backbone.View.extend({

		tagName: "article",

		className: "item cf",

		events: {
			"click a.remove-large": "removeCollaborator",
			"click .actions .editor": "makeEditor",
			"click .actions .viewer": "makeViewer",
		},
		
		initialize: function() {
			this.template = _.template($("#collaborator-template").html());
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;	
		},
		
		setReadOnly: function(isReadOnly) {
			var button = this.$(".actions p.button-blue");
			var loading_msg = window.loading_message.clone();
			// Show loading icon if there is a delay
			var t = setTimeout(function() { button.replaceWith(loading_msg); }, 500);
			view = this;
			this.model.save({read_only: isReadOnly}, {
				wait: true,
				complete: function() {
					clearTimeout(t);
				},
				success: function() {
					view.render();
				},
				error: function() {
					showConfirmationMessage("Unable to change collaboration level. Try again later.");
					loading_msg.replaceWith(button);
				}
			});
		},
		
		makeEditor: function() {
			this.setReadOnly(false);
			return false;
		},
		
		makeViewer: function() {
			this.setReadOnly(true);
			return false;
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

	// Search result view
	var SearchResultView = Backbone.View.extend({

		tagName: "li",

		events: {
			"click .actions .editor a": "addAsEditor",
			"click .actions .viewer a": "addAsViewer"
		},
		
		initialize: function() {
			this.template = _.template($("#collaborator-search-result-template").html());
		},
		
		addCollaborator: function(collaborator) {
			var viewItem = this.$el;
			
			var buttons = this.$(".actions .dual-buttons");
			var loading_msg = window.loading_message.clone();
			// Show loading icon if there is a delay
			var t = setTimeout(function() { buttons.replaceWith(loading_msg); }, 500);
			
			this.collection.create(collaborator.toJSON(),{
				wait: true,
				complete: function() {
					clearTimeout(t);
				},
				success: function() {
					viewItem.fadeOut();
				},
				error: function() {
					showConfirmationMessage("Unable to add collaborator. Try again later.");
					loading_msg.replaceWith(buttons);
				}
			});
		},

		addAsEditor: function() {
			this.addCollaborator(this.model);
			return false;
		},
		
		addAsViewer: function() {
			this.model.set('read_only', true);
			this.addCollaborator(this.model);
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

		events: {
			"focus input[name=search_box]": "liveSearch",
			"keyup input[name=search_box]": "liveSearch"
		},

		initialize: function() {
			this.template = _.template($("#collaborator-modal-template").html());
			
			// Search results collection
			this.searchResults = new CollaboratorList;
			this.searchResults.on('add', this.addSearchResult, this);
			this.searchResults.on('reset', this.removeSearchResults, this);

			this.collection.on('add',	 this.addCollaborator, this);
		},

		render: function() {
			this.$el.html(this.template());

			// List of current collaborators
			//collaborators.each(this.addCollaborator, this)

			return this;	
		},

		addCollaborator: function(collaborator) {
			this.$("div.link-list h2").show();
			this.$("div.link-list ul").append("<li>" + collaborator.get("name") + "</li>");
		},

		focusSearchBox: function() {
			this.$("input[name=search_box]").focus();
		},

		// Clear the search input and remove the dropdown
		resetSearch: function() {
			this.$(".livesearch").fadeOut("slow", function() {
				$(this).children("li").remove();
			});
			this.$("#add-collaborator-input").val("");
		},

		// Display a search result
		addSearchResult: function(searchResult) {
			var view = new SearchResultView({model: searchResult, collection: this.collection});	
			this.$(".livesearch > ul").append(view.render().el);
		},


		// Clear the search results
		removeSearchResults: function() {
			this.$(".livesearch ul").html("");
		},

		// Handle keyup event and only do an ajax request if there has been none in the last
		// 500ms
		liveSearch: function(e) {
			var view = this;
			var collaboratorsList = this.collection; // Pass this on to the call back
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
				url: collaboratorsList.url,
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
								view.searchResults.add(searchResult);
							});
						}
					} else {
						view.$(".livesearch ul").html("<strong>no results<strong>");
					}
				}
			});
		}
	});

	var CollaboratorsControl = Collaborators.CollaboratorsControl = Backbone.View.extend({

		el: "div.collaborators",

		events: {
			"click .settings-toolbar p.create a": "showAddCollaboratorsModal"
		},

		initialize: function(options) {
			this.collection.on('add',	 this.addCollaborator, this);
			this.collection.on('reset', this.addCollaborators, this);

			this.collection.on('reset', this.checkEmpty, this);
			this.collection.on('add', this.checkEmpty, this);
			this.collection.on('remove', this.checkEmpty, this);
		},

		showAddCollaboratorsModal: function() {
			var addCollaboratorsView = new AddCollaboratorsView({collection: this.collection});
			modalShow(addCollaboratorsView.render().el);
			addCollaboratorsView.focusSearchBox();
			return false;
		},

		addCollaborator: function(collaborator) {
			var view = new CollaboratorView({model: collaborator});	
			this.$(".col_12").append(view.render().el);
		},

		addCollaborators: function() {
			this.collection.each(this.addCollaborator, this);
		},

		checkEmpty: function() {
			if (this.collection.length) {
				this.$(".alert-message").fadeOut();
			} else {
				this.$(".alert-message").fadeIn();
			}
		}

	});
	
}(this));