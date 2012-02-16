<div class="feed" id="activity_stream">
	<h2 class="null"><?php echo __('Nothing to display yet.'); ?></h2>
</div>

<script type="text/template" id="activity_template">
	<div class="summary cf">
		<section class="source twitter">
			<a href="/user"><img src="<%= user_avatar %>" /></a>
		</section>
		<div class="content">
			<hgroup>
				<p class="date"><%= new Date(action_date).toLocaleString() %></p>
				<% if (action == "invite" && parseInt(action_to_self)) { %>
					<h1><%= user_name %> <span><a href="<%= action_on_url %>">invited you to collaborate on <% if (action_on == "account") { %> an <% } else { %> a <% } %> <%= action_on %></a></span></h1>
				<% } %>
				<% if (action == "invite" && !parseInt(action_to_self)) { %>
					<h1><%= user_name %> <span><a href="<%= action_on_url %>">invited <%= action_to_name %> to collaborate on <% if (action_on == "account") { %> an <% } else { %> a <% } %> <%= action_on %></a></span></h1>
				<% } %>
				<% if (action == "create") { %>
					<h1><%= user_name %> <span><a href="<%= action_on_url %>">created the <%= action_on %> "<%= action_on_name %>"</a></span></h1>
				<% } %>				
			</hgroup>
			<div class="body">
				<% if (action == "invite" && parseInt(action_to_self) && !parseInt(confirmed)) { %>
					<p>By accepting this invitation, you will be able to view and edit the settings for the <a href="<%= action_on_url %>">"<%= action_on_name %>"</a> <%= action_on %> along with <a href="<%= user_url %>"><%= user_name %></a>.</p>
				<% } %>
				<% if (action == "invite" && parseInt(action_to_self) && parseInt(confirmed)) { %>
					<p>You accepted the invitation and are be able to view and edit the settings for the <a href="<%= action_on_url %>">"<%= action_on_name %>"</a> <%= action_on %> along with <a href="<%= user_url %>"><%= user_name %></a>.</p>
				<% } %>
			</div>
		</div>
		<% if (action == "invite" && parseInt(action_to_self) && !parseInt(confirmed)) { %>
			<section class="actions">
				<div class="button">
					<p class="button-change checkbox-options" onclick=""><a><span class="icon"></span></a></p>
					<div class="clear"></div>
					<div class="dropdown container">
						<ul>
							<li class="confirm"><a onclick=""><?php echo __('Accept'); ?></a></li>
							<li class="cancel"><a onclick=""><?php echo __('Ignore'); ?></a></li>
						</ul>
					</div>
				</div>
			</section>
		<% } %>
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
		
		tagName: "article",
		
		className: "item",
		
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
			if (Activities.length) {
				this.$("h2").hide();
			}
		}		
	});
	
	// Bootstrap the list
	var activityStream = new ActivityStream;
	Activities.reset(<?php echo $activities ?>);
});
</script>