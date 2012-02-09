<script type="text/javascript">
/**
 * JavaScript for the bucket settings view
 *
 * @author    Ushahidi Dev Team
 * @package   Swiftriver - https://github.com/ushahidi/Swiftriver_v2
 * @copyright Ushahidi Inc - 2008-2012
 */

(function() {
	
	// Collaborator model
	window.Collaborator = Backbone.Model.extend({
		urlRoot: "<?php echo $fetch_url; ?>"
	});

	// Collection for the collaborators
	window.CollaboratorsCollection = Backbone.Collection.extend({
		model: Collaborator,
		url: "<?php echo $fetch_url; ?>"
	});
	

	// Global reference for the collaborators collection
	window.Collaborators = new CollaboratorsCollection;

	// Bucket model
	window.Bucket = Backbone.Model.extend({
		urlRoot: "<?php echo $bucket_url_root; ?>",

		// Adds a collaborator to the bucket
		addCollaborator: function(collaborator) {
			this.save({collaborator: collaborator}, {
				wait: true, 
				success: function(model, response) {
					if (response.success) {
						Collaborators.add(collaborator);
					}
				}
			});
		}

	});

	window.settingsBucket = new Bucket();

	// View for the settings view
	window.settingsBucketView = Backbone.View.extend({

		// Parent element for the settings view
		el: $("div#collaborators"),

		// Events
		events: {
			"click .controls-buttons .button-go > a": "saveBucket",
			"click #confirm-bucket-delete li.confirm > a": "deleteBucket",
			"keyup .input #add-collaborator": "liveSearch"
		},

		initialize: function() {

			var bucketId = $(this.el).data("settings-bucket-id");
			if (typeof(bucketId) != 'undefined' && parseInt(bucketId) > 0) {
				settingsBucket.set({id: bucketId});
			}

			// UI container for the collaborators list
			this.dataContainer = $("div.data", this.el);

			// UI container for the live search results
			this.liveSearchDialog = this.$("#live-search-dialog");

			Collaborators.on('reset', this.addCollaborators, this);
			Collaborators.on('add', this.addCollaborator, this);
			Collaborators.on('addnew', this.saveCollaborator, this);

		},

		addCollaborator: function(collaborator) {
			var view = new CollaboratorItemView({model: collaborator});
			$(this.dataContainer).append(view.render().el);
		},

		addCollaborators: function() {
			Collaborators.each(this.addCollaborator, this);
		},

		saveCollaborator: function(collaborator) {
			if (typeof(settingsBucket.get('id') == 'undefined')) {

				// Check if the item exists in the collection
				if (typeof(Collaborators.get(collaborator)) == 'undefined') {
					Collaborators.add(collaborator);
				}

			} else {
				settingsBucket.addCollaborator(collaborator);
			}
		},

		// Saves a bucket
		saveBucket: function(e) {
			var bucketName = $(".new-bucket :text").val();
			if (bucketName != '') {

				var bucketData ={bucket_name: bucketName};
				
				// Check if the bucket has an id
				if (typeof(settingsBucket.get('id')) == 'undefined') {
					bucketData.collaborators = Collaborators;
				}
				// console.log(settingsBucket.isNew());

				// Save that bucket
				settingsBucket.save(bucketData, {
					wait: true, 
					success: function(model, response) {
						if (response.success) {
							// Redirect to the newly created bucket
							window.location.href = response.redirect_url;
						}
					}
				}); // end this.model.save
			} // end if
		},

		// Deletes the current bucket
		deleteBucket: function(e) {
			// Initiate removal on the server
			settingsBucket.destroy({
				wait: true, 
				success: function(model, response) {
					if (response.success) {
						// Redirect to the dashboard
						window.location.href = response.redirect_url;
					}
				}
			});
		},

		addLiveSearchItem: function(searchItem) {
			// Save the selected colloborator on the server
			var item = new LiveSearchItem({model: searchItem});
			$("ul", this.liveSearchDialog).append(item.render().el);
		},

		// Collaborator live search
		liveSearch: function(e) {
			if (!((e.keyCode >= 16 && e.keyCode <= 90) || e.keyCode == 8))
				return;
			
			// Get the search term
			var searchTerm = $(e.currentTarget).val();

			var view = this;

			if ($.trim(searchTerm) != '' && searchTerm.length >= 2) {
				var tempCollection = new CollaboratorsCollection();

				// Initiate live search
				tempCollection.fetch({
					data: {search: searchTerm}, 
					wait: true, 
					success: function(collection, response) {
						if (response.length > 0) {
							$(view.liveSearchDialog).css("display", "block");
							$("ul", view.liveSearchDialog).empty();

							collection.each(view.addLiveSearchItem, view);
						} else {
							$(view.liveSearchDialog).hide();
						}
					}
					}); // end fetch
			}
		}

	});


	// View for a single collaborator 
	window.CollaboratorItemView = Backbone.View.extend({
	
		tagName: "article",
	
		className: "item cf",
	
		template: _.template($("#collaborator-template").html()),
	
		events: {
			// Delete button clicked
			"click .actions .dropdown li.confirm a": "removeCollaborator"
		},
	
		// Callback for the delete action
		removeCollaborator: function(e) {
			var view = this;
			
			// Remove model from the collection
			Collaborators.remove(this.model);

			if (typeof(settingsBucket.get('id')) == 'undefined') {
				$(view.el).remove();
			} else {
				// Remove the collaborator on the server
				this.model.destroy({
					wait: true, 
					success: function(model, response) {
						if (response.success) {
							// Remove item from the UI
							$(view.el).fadeOut();
							$(view.el).remove();
						}
					}
				});
			}
			
			e.stopPropagation()
		},
		
		// Render
		render: function(eventName) {
			$(this.el).attr("data-collaborator-id", this.model.get("id"));
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		}
	});

	// View for the live search
	window.LiveSearchItem = Backbone.View.extend({
		
		tagName: "li",

		className: "checkbox",

		template: _.template($("#live-search-template").html()),

		events: {
			"click li.checkbox a": "selectCollaborator"
		},

		// When a collaborator is selected from the
		selectCollaborator: function(e) {
			
			// Add the selected item
			Collaborators.trigger('addnew', this.model);

		},

		render: function(eventName) {
			$(this.el).html(this.template(this.model.toJSON()));

			return this;
		}
	});
	
	
	// Bootstrap rendering
	window.settingsView = new settingsBucketView;
	Collaborators.reset(<?php echo $collaborators_list; ?>);
	
})();
</script>