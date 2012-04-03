<script type="text/template" id="activity_template">
	<?php if ($owner AND $gravatar_view): ?>
		<a class="avatar-wrap"><img src="<%= user_avatar %>"/></a>
	<?php else: ?>
		<span class="icon"></span>
	<?php endif; ?>

	<div class="item-body">
		
		<?php if ($owner): ?>
			<% if (action_name == "invite" && parseInt(action_to_self) && !parseInt(confirmed)) { %>
				<div class="actions">
					<ul class="dual-buttons">
						<li class="button-white"><a href="#"><?php echo __('Accept'); ?></a></li>
						<li class="button-white"><a href="#"><?php echo __('Ignore'); ?></a></li>
					</ul>
				</div>
			<% } %>
		<?php endif; ?>

		<h2>
			<a href="<%= user_url %>"><%= user_name %></a>
			<% if (action_name == "invite" && parseInt(action_to_self)) { %>
				<?php echo __("invited you to collaborate on "); ?>
				<% var determiner = (action_on == "account") ? "an" : "the"; %>
				<%= determiner %><a href="<%= action_on_url %>"><%= action_on_name %></a> <%= action_on %>
			<% } %>
			
			<% if (action_name == "invite" && !parseInt(action_to_self)) { %>
				<% var determiner = (action_on == "account") ? "an" : "the"; %>
				invited <%= action_to_name %> to collaborate on <%= determiner %> 
				<a href="<%= action_on_url %>"><%= action_on_name %></a> <%= action_on %>
			<% } %>

			<% if (action_name == "create") { %>
				created the <a href="<%= action_on_url %>"><%= action_on %> "<%= user_data.action_on_name %>"</a>
			<% } %>

			<?php if ($owner): ?>
				<% if (action_name == "invite" && parseInt(action_to_self) && !parseInt(confirmed)) { %>
					<p>
					<?php echo __("By accepting this invitation, you will be able to view and edit the settings for the "); ?>
					<a href="<%= action_on_url %>">"<%= user_data.action_on_name %>"</a> 
					<%= action_on %> along with <a href="<%= user_url %>"><%= user_name %></a>.
					</p>
				<% } %>
			<?php endif; ?>

		</h2>
		<p class="metadata"><%= new Date(action_date).toLocaleString() %></p>
	</div>
</script>

<script type="text/template" id="grouped_activity_template">
	<?php if ($owner AND $gravatar_view): ?>
		<a class="avatar-wrap"><img src="<%= user_avatar %>"/></a>
	<?php else: ?>
		<span class="icon"></span>
	<?php endif; ?>

	<div class="item-body">
		<h2>
			<a href="<%= user_url %>"><%= user_name %></a>
			<% if (action_name == "invite") { %>
				<% if (users.length == 2) { %>
					<?php echo __("invited"); ?> <%= users[0].action_to_name %> and <%= users[1].action_to_name %> 
					<?php echo __("to collaborate on the "); ?>
					<a href="<%= action_on_url %>"><%= action_on_name %></a> <%= action_on %>
				<% } else if (users.length > 2) { %>
					<% var user_count = users.length - 1; %>
					<?php echo __("invited"); ?> <%= users[0].action_to_name %> and <%= user_count %> 
					<?php echo __("others to collaborate on the"); ?>
					<a href="<%= action_on_url %>"><%= action_on_name %></a> <%= action_on %>
				<% } %>
			<% } %>
		</h2>

		<?php 
			/* <p class="metadata"><%= new Date(action_date).toLocaleString() %></p> */ 
		?>
	</div>
</script>


<script type="text/javascript">

$(function() {
	
	var fetch_url = "<?php echo $fetch_url ?>";
	
	// Activity model, collection and view
	var Activity = Backbone.Model.extend({
		
		confirm: function() {
			this.save({confirmed: 1}, {wait: true});
		}
	
	});
	
	var ActivityList = Backbone.Collection.extend({		
		model: Activity,				
		url: fetch_url,		
	});
	
	var ActivityView = Backbone.View.extend({
		
		tagName: "div",
		
		className: "parameter activity-item cf",
		
		template: _.template($("#activity_template").html()),
		
		events: {
			"click section.actions .confirm a": "confirm"
		},
		
		initialize: function() {
			// Listen for confirmed state change
			this.model.on("change:confirmed", this.render, this);
		},
		
		confirm: function() {
			this.model.confirm();
		},
		
		render: function() {
			var action = this.model.get("action_name");
			<?php if ( ! $gravatar_view): ?>
			if (action == "create") {
				this.$el.addClass("add");
			} else if (action == "invite") {
				this.$el.addClass("follow");
			}
			<?php endif; ?>
			this.$el.html(this.template(this.model.toJSON()));
			return this;	
		}
	});

	// View for grouped activity streams
	var GroupedActivityView = Backbone.View.extend({
		
		tagName: "div",
		
		className: "parameter activity-item cf",
		
		template: _.template($("#grouped_activity_template").html()),

		render: function() {
			var action = this.model.get("action_name");
			<?php if ( ! $gravatar_view): ?>
			if (action == "create") {
				this.$el.addClass("add");
			} else if (action == "invite") {
				this.$el.addClass("follow");
			}
			<?php endif; ?>

			this.$el.html(this.template(this.model.toJSON()));

			return this;	
		}

	})
	
	// Master activity list
	var Activities = new ActivityList;
	
	// View of the entire activity stream
	var ActivityStream = Backbone.View.extend({
		
		el: "#activity_stream",
		
		initialize: function() {
			Activities.on('add', this.addActivity, this);
			Activities.on('reset', this.addActivities, this);			
		},
		
		addActivity: function(activity) {
			var json = activity.toJSON();
			for (var i=0; i<json.actions.length; i++) {
				var actionData = json.actions[i].action_data;
				
				for (var j=0; j<actionData.length; j++) {
					var actionTarget = actionData[j].action_on_target;

					var activityModel = new Activity({
						user_id: json.user_id,
						user_name: json.user_name,
						user_url: json.user_url,
						user_avatar: json.user_avatar,
						action_name: json.actions[i].action_name,
						action_on: actionData[j].action_on,
						action_on_url: actionTarget.action_on_url,
						action_on_name: actionTarget.action_on_name
					});

					if (actionTarget.users.length == 1) {
						// Rebuild the activity model
						activityModel.set(actionTarget.users[0]);
						var view = new ActivityView({model: activityModel});	
						this.$el.append(view.render().el);
					} else if (actionTarget.users.length > 1) {
						// Grouped items
						activityModel.set({
							action_date: actionData[j].timestamp_date_str,
							users: actionTarget.users
						});
						var view = new GroupedActivityView({model: activityModel});
						this.$el.append(view.render().el);
					}
				}
			}
		},
		
		addActivities: function() {
			Activities.each(this.addActivity, this);
		}		
	});
	
	// Bootstrap the list
	var activityStream = new ActivityStream;
	Activities.reset(<?php echo $activities ?>);
});
</script>