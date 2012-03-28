<script type="text/template" id="activity_template">
	
	<?php if ($owner AND $gravatar_view): ?>
		<a class="avatar-wrap"><img src="<%= user_avatar %>"/></a>
	<?php else: ?>
		<span class="icon"></span>
	<?php endif; ?>

	<div class="item-body">
		
		<?php if ($owner): ?>
			<% if (action == "invite" && parseInt(action_to_self) && !parseInt(confirmed)) { %>
				<div class="actions">
					<ul class="dual-buttons">
						<li class="button-white no-icon"><a href="#"><?php echo __('Accept'); ?></a></li>
						<li class="button-white no-icon"><a href="#"><?php echo __('Ignore'); ?></a></li>
					</ul>
				</div>
			<% } %>
		<?php endif; ?>

		<h2>
			<a href="<%= user_url %>"><%= user_name %></a>
			<% if (action == "invite" && parseInt(action_to_self)) { %>
				<?php echo __("invited you to collaborate on "); ?>
				<a href="<%= action_on_url %>"><% if (action_on == "account") { %> an <% } else { %> a <% } %> <%= action_on %></a>
			<% } %>
			<% if (action == "invite" && !parseInt(action_to_self)) { %>
				invited <%= action_to_name %> to collaborate on <a href="<%= action_on_url %>"> <% if (action_on == "account") { %> an <% } else { %> a <% } %> <%= action_on %></a>
			<% } %>
			<% if (action == "create") { %>
				created the <a href="<%= action_on_url %>"><%= action_on %> "<%= action_on_name %>"</a>
			<% } %>

			<?php if ($owner): ?>
				<% if (action == "invite" && parseInt(action_to_self) && !parseInt(confirmed)) { %>
					<p>
					<?php echo __("By accepting this invitation, you will be able to view and edit the settings for the "); ?>
					<a href="<%= action_on_url %>">"<%= action_on_name %>"</a> <%= action_on %> along with <a href="<%= user_url %>"><%= user_name %></a>.
					</p>
				<% } %>
			<?php endif; ?>

		</h2>
		<p class="metadata"><%= new Date(action_date).toLocaleString() %></p>
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
			var action = this.model.get("action");
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
	
	// Master activity list
	var Activities = new ActivityList;
	
	// View of the entire activity stream
	var ActivityStream = Backbone.View.extend({
		
		el: "#activity_stream",
		
		initialize: function() {
			Activities.on('add',	 this.addActivity, this);
			Activities.on('reset', this.addActivities, this);			
		},
		
		addActivity: function(activity) {
			var view = new ActivityView({model: activity});	
			this.$el.append(view.render().el);
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