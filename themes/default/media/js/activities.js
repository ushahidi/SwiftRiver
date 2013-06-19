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
		
		initialize: function() {
			if (this.get("action_on") == "account") {
				var actionOnObj = this.get("action_on_obj")
				actionOnObj["name"] = actionOnObj["owner"]["name"];
				this.set("action_on_obj", actionOnObj);
			} else if (this.get("action_on") == "river_collaborator") {
				this.set("invite_to", "river");
				this.set("invite_to_name", this.get("action_on_obj")["river"]["name"]);
			} else if (this.get("action_on") == "bucket_collaborator") {
				this.set("invite_to", "bucket");
				this.set("invite_to_name", this.get("action_on_obj")["bucket"]["name"]);
			}
			
			this.set("date", this.getDateString(this.get("date_added")));
		},
		
		getDateString: function(dateString) {

			var msPerMinute = 60 * 1000;
			var msPerHour = msPerMinute * 60;
			var msPerDay = msPerHour * 24;
			var msPerMonth = msPerDay * 30;
			var msPerYear = msPerDay * 365;
			var elapsed = Date.now() - Date.parse(dateString);
			
			if (elapsed < msPerMinute) {
				return Math.round(elapsed/1000) + ' seconds ago';
			}
			else if (elapsed < msPerHour) {
				return Math.round(elapsed/msPerMinute) + ' minutes ago';
			}
			else if (elapsed < msPerDay ) {
				return Math.round(elapsed/msPerHour ) + ' hours ago';
			}
			else if (elapsed < msPerMonth) {
				return 'approximately ' + Math.round(elapsed/msPerDay) + ' days ago';
			}
			else if (elapsed < msPerYear) {
				return 'approximately ' + Math.round(elapsed/msPerMonth) + ' months ago';
			}
			else {
				return 'approximately ' + Math.round(elapsed/msPerYear ) + ' years ago'; 
			}
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
		
		tagName: "article",
		
		className: "news-feed-item cf",
				
		events: {
			"click .actions .confirm a": "confirm",
			"click .actions .ignore a": "ignore"
		},
		
		initialize: function() {
			
			switch(this.model.get("action")) {
				case "create":
					this.template = _.template($("#create_activity_template").html());
					break;
				case "invite":
					this.template = _.template($("#invite_activity_template").html());
					break;
				case "follow":
					this.template = _.template($("#follow_activity_template").html());
					break;
				case "comment":
					this.template = _.template($("#comment_activity_template").html());
					break;
			}
			
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
		
		el: "#news-feed",
		
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
				// For invites, if we have seen an invite for a specific 
				// river/bucket id before and its timestamp is within 6 hours 
				// of the activity of the activity then simply reuse that view 
				// otherwise create a new one. 
				// In effect, group nearby river/bucket invites.
				var actionOnObjId = activity.get("action_on_obj")["id"];
				key = activity.get("action_on") + actionOnObjId;
				if (!_.has(this.inviteViews, key)) {
					this.showNewActivity(activity, true);
				} else {
					var view = _.find(this.inviteViews[key], function(v){ 
						var activityTimestamp = Date.parse(activity.get("date_added"));
						var modelTimestamp = Date.parse(v.model.get("date_added"));
						return Math.abs(activityTimestamp - modelTimestamp) <= 21600000;
					});
					
					if (view == undefined) {
						view = this.showNewActivity(activity, true);
					} else {
						view.model.incCount();
					}

					if (actionOnObjId == logged_in_user) {
						// If the logged in user is invited, display that
						// view to allow them to ignore/accept the invite
						// Also there can be multiple invites but only one unconfirmed so
						// the below IF makes sure we always display the unconfirmed one
						if (!(actionOnObjId == view.model.get("action_on_id")["id"] && activity.get("confirmed"))) {
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
			if (!this.collection.size()) {
				$("#no-activity-message").show();
			} else {
				$("#no-activity-message").hide();
				this.collection.each(this.addActivity, this);
			}
		}		
	});

}(this));