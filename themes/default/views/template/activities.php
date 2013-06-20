<script type="text/template" id="create_activity_template">
	<div class="item-type">
		<a href="#" class="avatar-wrap"><img src="<%= account.owner.avatar %>" /></a>
	</div>
	<div class="item-summary">
		<span class="timestamp"><%= date %></span>
		<h2><a href="<%= actor_url %>"><%= account.owner.name %></a> created the <a href="<%= action_on_url %>"><%= action_on_obj.name %></a> <%= action_on %>.</h2>
	</div>	
</script>

<script type="text/template" id="invite_activity_template">
	<div class="item-type">
		<a href="#" class="avatar-wrap"><img src="<%= account.owner.avatar %>" /></a>
	</div>
	<div class="item-summary">
		<span class="timestamp"><%= date %></span>
		<h2><a href="<%= actor_url %>"><%= account.owner.name %></a> invited <a href="<%= action_to_url %>"><%= actionTo.owner.name %></a> to collaborate on the <a href="<%= action_on_url %>"><%= invite_to_name %></a> <%= invite_to %>.</h2>
	</div>		
</script>

<script type="text/template" id="follow_activity_template">
	<div class="item-type">
		<a href="#" class="avatar-wrap"><img src="<%= account.owner.avatar %>" /></a>
	</div>
	<div class="item-summary">
		<span class="timestamp"><%= date %></span>
		<h2><a href="<%= actor_url %>"><%= account.owner.name %></a> started following the <a href="<%= action_on_url %>"><%= action_on_obj.name %></a> <%= action_on %>.</h2>
	</div>	
</script>

<script type="text/template" id="comment_activity_template">
	<div class="item-type">
		<a href="#" class="avatar-wrap"><img src="<%= account.owner.avatar %>" /></a>
	</div>
	<div class="item-summary">
		<span class="timestamp"><%= date %></span>
		<h2><a href="<%= actor_url %>">Nathaniel Manning</a> commented on bucket <a href="#">Web design and development</a></h2>
		<div class="item-sample">
			<a href="#" class="avatar-wrap"><img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_bigger.jpeg" /></a>
			<div class="item-sample-body">
				<p>Short loin meatball pork loin leberkas venison pork belly tri-tip short ribs ground round ribeye. Tail pastrami shankle pancetta pork belly ball tip, filet mignon shank.</p>
			</div>
		</div>
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