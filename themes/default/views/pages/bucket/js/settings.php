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
	window.Collaborator = Backbone.Model.extend();
	window.BucketCollaborators = Backbone.Collection.extend({
		model: Collaborator,
		url: "<?php echo $collaborator_fetch_url; ?>"
	});
	
	// List view for the 
	window.CollaboratorsListView = Backbone.View.extend({
		
		// Parent container
		el: $("#collaborators div.data"),
		
		initialize: function() {
			this.model.bind("reset", this.render, this);
		},
		
		render: function(eventName) {
			_.each(this.model.models, function(collaborator) {
				$(this.el).append(new CollaboratorItemView({model: collaborator}).render().el);
			}, this);
			
			return this;
		}
	
	});

	// View for a single collaborator 
	window.CollaboratorItemView = Backbone.View.extend({
	
		tagName: "article",
	
		className: "item cf",
	
		template: _.template($("#bucket-collaborator-list-item").html()),
	
		events: {
			// Delete button clicked
			"click .button-change a.delete": "removeCollaborator"
		},
	
		// Callback for the delete action
		removeCollaborator: function(e) {
			var collaborator = this;
			
			// TODO: Remove the collaborator from the bucket
			
			// Remove item from the UI
			$(collaborator).remove();
			
			e.stopPropagation()
		},
		
		// Render
		render: function(eventName) {
			$(this.el).attr("data-collaboration-id", this.model.get("id"));
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		}
	});
	
	
	// Render
	var collaborators = new BucketCollaborators();
	collaborators.fetch();
	
	var collaboratorsList = new CollaboratorsListView({model: collaborators});
	collaboratorsList.render();
	
	
	// ---------------------------------------------------------------
	// Event handling
	// ---------------------------------------------------------------
	
	// When "Apply changes"" is clicked
	$(".controls-buttons p.button-go > a").live('click', function() {
		var postData = {
			bucket_name: $(".new-bucket :text").val()
		}
		
		$.post("<?php echo $save_settings_url?>", postData, function(response) {
			if (response.success) {
				
				// Check if the redirect URL is non-empty
				if (response.redirect_url != "") {
					window.location.href = response.redirect_url;
				}
			}
			
		}, "json");
	});
	
	// "Delete Bucket"
	$(".dropdown li.confirm > a").live('click', function(e) {
		$.post("<?php echo $delete_bucket_url; ?>", function(response) {
			if (response.success) {
				window.location.href = "/dashboard";
			}
			
			$(e.currentTarget).parent("ul.dropdown").css("display", "none");
			
		}, "json");
	});

})();
</script>