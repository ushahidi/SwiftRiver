<div class="button-actions">
	<span><a href="/markup/modal-collaborators.php" class="modal-trigger"><i class="icon-users"></i>Add collaborator</a></span>
</div>

<script type="text/template" id="user_item_template">
	<header class="cf">
		<div class="property-title col_8">
			<a href="<%= user_url %>" class="avatar-wrap"><img src="<%= user_avatar %>"/></a>
			<h1><a href="<%= user_url %>"><%= name %></a></h1>
		</div>
		<div class="button-actions col_4">		
			<span class="popover"><a href="#" class="popover-trigger" title="<?php echo __("Delete"); ?>"><span class="icon-remove"></span><span class="nodisplay"><?php echo __("Remove"); ?></span></a>
				<ul class="popover-window popover-prompt base">
					<li class="destruct"><a href="#">Remove collaborator from river</a></li>
				</ul>							
			</span>					
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
			"click .destruct > a": "deleteUser"
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