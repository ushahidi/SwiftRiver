<script type="text/template" id="activity_template">
	<?php if ($owner AND $gravatar_view): ?>
		<a class="avatar-wrap"><img src="<%= avatar %>"/></a>
	<?php else: ?>
		<span class="icon"></span>
	<?php endif; ?>

	<div class="item-body">
		
		<?php if ($owner): ?>
			<% if (action == "invite" && (action_to_id == logged_in_user) && !confirmed) { %>
				<div class="actions">
					<ul class="dual-buttons">
						<li class="button-white confirm"><a href="#"><?php echo __('Accept'); ?></a></li>
						<li class="button-white ignore"><a href="#"><?php echo __('Ignore'); ?></a></li>
					</ul>
				</div>
			<% } %>
		<?php endif; ?>

		<h2>
			<a href="<%= user_url %>"><%= user_name %></a>
			<% if (action == "invite" && (action_to_id == logged_in_user)) { %>
				invited you <% if (count > 1) { %> and <%= count %> others <% } %> to collaborate on the <%= action_on %> <a href="<%= action_on_url %>">"<%= action_on_name %>"</a>.
			<% } %>
			
			<% if (action == "invite" && (action_to_id != logged_in_user)) { %>
				invited <%= action_to_name %><% if (count > 1) { %> and <%= count %> others <% } %> to collaborate on the <%= action_on %> <a href="<%= action_on_url %>">"<%= action_on_name %>"</a>.
			<% } %>

			<% if (action == "create") { %>
				created the <%= action_on %> <a href="<%= action_on_url %>">"<%= action_on_name %>"</a>.
			<% } %>
			
			<% if (action == "follow") { %>
				<% if (action_on == "user") { %>
					<% if (action_on_id == logged_in_user) { %>
						started following you.
					<% } else { %>
						started following <a href="<%= action_on_url %>"><%= action_on_name %></a>.
					<% } %>
				<% } else {  %>
					subscribed to the <%= action_on %> <a href="<%= action_on_url %>">"<%= action_on_name %>"</a>.
				<% } %>
			<% } %>

			<?php if ($owner): ?>
				<% if (action == "invite" && (action_to_id == logged_in_user) && !confirmed) { %>
					<p>
					<?php echo __("By accepting this invitation, you will be able to view and edit the settings for the "); ?>
					<a href="<%= action_on_url %>">"<%= action_on_name %>"</a> 
					<%= action_on %> along with <a href="<%= user_url %>"><%= user_name %></a>.
					</p>
				<% } %>
			<?php endif; ?>

		</h2>
		<p class="metadata"><%= new Date(action_date_add).toLocaleString() %></p>
	</div>
</script>

<script type="text/javascript">

$(function() {
	var activities = new Activities.ActivityList;
	activities.url = "<?php echo $fetch_url ?>";
	var activityStream = new Activities.ActivityStream({collection: activities});
	
	<?php if ($owner): ?>
		// Keep track of the last activity we have in the view
		var lastId = -1;
		function updateLastId(activity) {
			if (activity.get("id") < lastId || lastId < 0) {
				lastId = activity.get("id");
			}
		}
		activities.on("add", updateLastId, this);
		activities.on("reset", function() { activities.each(updateLastId, this); }, this);
	
		var isPageFetching = false;
		var isAtLastPage = false;
		var loading_msg = window.loading_message.clone();
		$(window).bind("scroll", function() {
			bottomEl = $("#next_page_button");

			if (!bottomEl.length)
				return;

			if (nearBottom(bottomEl) && !isPageFetching && !isAtLastPage) {
				// Advance page and fetch it
				isPageFetching = true;

				// Hide the navigation selector and show a loading message				
				loading_msg.appendTo(bottomEl).show();

				activities.fetch({
				    data: {
				        last_id: lastId
				    }, 
				    add: true,
				    complete: function(model, response) {
						// Reanable scrolling after a delay
						setTimeout(function(){ isPageFetching = false; }, 700);
				        loading_msg.fadeOut('normal');
				    },
				    error: function(model, response) {
				        if (response.status == 404) {
				            isAtLastPage = true;
				        }
				    }
				});
		    }
		});
		
		// Poll for new activities every 30-60 seconds
		var newActivities = new Activities.ActivityList;
		newActivities.url = "<?php echo $fetch_url ?>";
		
		// Keep track of the last and first activity we have in the view
		var maxId = 0;
		function updateMaxId(activity) {
			if (activity.get("id") > maxId) {
				maxId = activity.get("id");
			}
			
		}
		activities.on("reset", function() { activities.each(updateMaxId, this); }, this);
		newActivities.on("add", updateMaxId);
		newActivities.on("add", function() {
			$("#no_activities_alert").hide();	
		});
		
		var isSyncing = false;
		setInterval(function() {
			if (!isSyncing) {
				isSyncing = true;
				newActivities.fetch({data: {since_id: maxId}, 
				    add: true, 
				    complete: function () {
				        isSyncing = false;
				    }
				});   
			}		    
		}, 30000 + Math.floor((Math.random()*30000)+1));
		
		// View of the entire activity stream
		var NewActivitiesAlert = Backbone.View.extend({
		
			el: "#new_activities_alert",
			
			events: {
				"click a": "showNewActivities",
			},
		
			initialize: function() {
				this.collection.on('add', this.alertNewActivities, this);
			},
		
			alertNewActivities: function(activity) {
				var message = null;
				if (this.collection.length > 1) {
					message = this.collection.length + " new activities";
				} else {
					message = this.collection.length + " new activity";
				}
				
				this.$("#new_activity_count").html(message);
				this.$el.show();
			},
			
			showNewActivities: function() {
				this.options.activities.add(this.collection.models.reverse());
				this.collection.reset();
				this.$el.hide();
				return false;
			}
		});
		new NewActivitiesAlert({collection: newActivities, activities: activities});
	<?php endif; ?>
	
	// Bootstrap the list
	activities.reset(<?php echo $activities ?>);
	
	if (!(activities.length > 0)) {
		$("#no_activities_alert").show();
		isAtLastPage = true;
	}
});
</script>