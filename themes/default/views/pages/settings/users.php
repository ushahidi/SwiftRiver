<div class="settings-toolbar">
	<p class="button-blue create">
		<a href="#" class="modal-trigger">
			<span class="icon"></span><?php echo __("Add User"); ?>
		</a>
	</p>
</div>

<script type="text/template" id="user_item_template">
	<header class="cf">
		<a href="#" class="remove-large" title="<?php echo __("Delete"); ?>">
			<span class="icon"></span>
			<span class="nodisplay"><?php echo __("Remove"); ?></span>
		</a>
		<div class="property-title">
			<a href="<%= user_url %>" class="avatar-wrap"><img src="<%= user_avatar %>"/></a>
			<h1><a href="<%= user_url %>"><%= name %></a></h1>
		</div>
	</header>
</script>

<script type="text/javascript">
$(function() {
	var User = Backbone.Model.extend({});
	var UsersList = Backbone.Collection.extend({
		model: User
	});

	var UserItemView = Backbone.View.extend({
		
		tagName: "article",

		className: "container base",

		template: _.template($("#user_item_template").html()),

		events: {
			"click a.remove-large": "deleteUser"
		},

		deleteUser: function(e) {
			var containerEl = this.$el;
			this.model.destroy({wait: true, success: function(model, response) {
				$(containerEl).fadeOut();
			}});
			e.stopPropagation();
			return false;
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}
	});

	var UsersView = Backbone.View.extend({

		initialize: function() {
			this.users = new UsersList;
			this.users.on("reset", this.addUsers, this);
			this.users.on("add", this.addUser, this);
		},

		addUser: function(user) {
			view = new UserItemView({model: user}).render().el;
			$(".settings-toolbar").after(view);
		},

		addUsers: function() {
			this.users.each(this.addUser, this);
		}
	});

	var usersView = new UsersView;
	usersView.users.url = "<?php echo $fetch_url; ?>";
	usersView.users.reset(<?php echo $users_list; ?>);
});
</script>