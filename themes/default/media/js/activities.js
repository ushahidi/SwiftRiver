/**
 * Activities module
 */
(function (root) {
	
	// Init the module
	Activities = root.Activities = {};
	
	// Activity model, collection and view
	var Activity = Activities.Activity = Backbone.Model.extend({
		
		defaults: {
		    "count":  1,
		},
				
		ignore: function() {
			this.save({ignored: 1}, {wait: true});
		},
		
		incCount: function() {
			this.set('count', this.get('count')+1)
		}
	
	});
	
	var ActivityList = Activities.ActivityList = Backbone.Collection.extend({
		model: Activity
	});
	
	var ActivityView = Activities.ActivityView = Backbone.View.extend({
		
		tagName: "div",
		
		className: "parameter activity-item cf",
				
		events: {
			"click .actions .confirm a": "confirm",
			"click .actions .ignore a": "ignore"
		},
		
		initialize: function() {
			
			this.template = _.template($("#activity_template").html());
			
			// Listen for confirmed state change
			this.model.on("change:confirmed", this.render, this);
			this.model.on("change:count", this.render, this);
		},
		
		confirm: function() {
			this.model.save({confirmed: true}, {
			    	wait: true,
			    	success: function(model, response) {
						// Show notification message of the acceptance
						var message = "You have accepted " + model.get("user_name") + "'s " +
						    "invitation to collaborate on the " + model.get("action_on_name") +
						    " " + model.get("action_on");

						showConfirmationMessage(message);

			    	},
			    	error: function(model, response) {
						// The server threw an error
						var message = "Oops! Something went wrong...";
						showConfirmationMessage(message);
			    	}
			});

			return false;
		},

		ignore: function() {
			this.model.ignore();
			this.$el.fadeOut('slow');
			return false;
		},
		
		render: function() {
			var action = this.model.get("action");
			if (action == "create" || action == "invite") {
				this.$el.addClass("add");
			} else if (action == "follow") {
				this.$el.addClass("follow");
			}
			
			this.$el.html(this.template(this.model.toJSON()));
			return this;	
		}
	});
	
	// View of the entire activity stream
	var ActivityStream = Activities.ActivityStream = Backbone.View.extend({
		
		el: "#activity_stream",
		
		inviteViews: {},
		
		maxId: 0,
		
		initialize: function() {
			this.collection.on('add', this.addActivity, this);
			this.collection.on('reset', this.addActivities, this);			
		},
		
		showNewActivity: function(activity, isInvite) {
			var view = new ActivityView({model: activity});

			if (isInvite) {
				this.inviteViews[key] = [view];	
			}

			if (activity.get("id") > this.maxId) {
				this.$el.prepend(view.render().el);
			} else {
				this.$el.append(view.render().el);
			}
			
			return view;
		},
		
		addActivity: function(activity) {
			if (activity.get("action") == "invite") {
				// For invites, if we have seen an invite for a specific river/bucket
				// id before and its timestamp is within 6 hours of the activity,
				// then simply reuse that view otherwise create a new one. 
				// In effect, group nearby river/bucket invites.
				key = activity.get("action_on") + activity.get("action_on_id");
				if (!_.has(this.inviteViews, key)) {
					this.showNewActivity(activity, true);
				} else {
					var view = _.find(this.inviteViews[key], function(v){ 
						var activityTimestamp = Date.parse(activity.get("action_date_add"));
						var modelTimestamp = Date.parse(v.model.get("action_date_add"));
						return Math.abs(activityTimestamp - modelTimestamp) <= 21600000;
					});
					
					if (view == undefined) {
						view = this.showNewActivity(activity, true);
					} else {
						view.model.incCount();
					}

					if (activity.get("action_on_id") == logged_in_user) {
						// If the logged in user is invited, display that
						// view to allow them to ignore/accept the invite
						// Also there can be multiple invites but only one unconfirmed so
						// the below IF makes sure we always display the unconfirmed one
						if (!(activity.get("action_on_id") == view.model.get("action_on_id") && activity.get("confirmed"))) {
							activity.set("count", view.model.get("count"));
							view.model = activity;
							view.initialize();
							view.render();
						}
					}
				}
			} else {
				this.showNewActivity(activity, false);
			}
			
			if (activity.get("id") > this.maxId) {
				this.maxId = activity.get("id");
			}
		},
		
		addActivities: function() {
			this.collection.each(this.addActivity, this);
		}		
	});

}(this));