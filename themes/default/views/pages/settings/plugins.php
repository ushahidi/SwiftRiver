<?php echo Form::open(); ?> 
	<input type="hidden" name="action" value="">
	<input type="hidden" name="id" value="">
	<div class="settings-toolbar"></div>
<?php echo Form::close(); ?>

<script type="text/template" id="plugin_item_template">
	<header class="cf">
		<div class="actions">
			<% if (plugin_enabled && plugin_settings) { %>
				<p class="button-blue button-small">
					<a href="<?php echo URL::site('settings')?>/<%= plugin_path %>">Settings</a>
				</p>
				<p>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>
			<% } %>
			<% var link_title = (plugin_enabled)
			       ? "<?php echo __('deactivated plugin'); ?>" 
			       : "<?php echo __('activated plugin'); ?>"; 

			    var selected = (plugin_enabled)? "selected" : "";
			%>		
			<a href="#" title="<%= link_title %>" class="<%= selected %>"><span class="icon-checkmark"></span><span class="nodisplay"><%= link_title %></span></a>
		</div>		
	</header>
	<section class="property-parameters">
		<div class="parameter">
			<p><%= plugin_description %></p>
		</div>
	</section>
</script>

<script type="text/javascript">
$(function() {

	var PluginItem = Backbone.Model.extend({
		toggleActivation: function(target) {
			this.save({
				plugin_enabled: this.get("plugin_enabled")? 0: 1
			},
			{
				wait: true,
				success: function(model, response) {
					if (model.get("plugin_enabled") == 1) {
						$(target).addClass("selected");
					} else {
						$(target).removeClass("selected");
					}
				}
			}
		)}
	});

	var PluginItemList = Backbone.Collection.extend({
		model: PluginItem
	});

	var PluginItemView = Backbone.View.extend({

		tagName: "article",

		className: "container base",

		template: _.template($("#plugin_item_template").html()),

		events: {
			"click .button-actions a": "toggleActivation"
		},

		toggleActivation: function(e) {
			targetEl = $(e.currentTarget);
			this.model.toggleActivation(targetEl);
			return false;
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}
	});

	var PluginsView = Backbone.View.extend({

		initialize: function() {
			this.plugins = new PluginItemList;
			this.plugins.on("reset", this.addPlugins, this);
			this.plugins.on("add", this.addPlugin, this);
		},

		addPlugin: function(plugin) {
			view = new PluginItemView({model: plugin}).render().el;
			$("div.settings-toolbar").after(view);
		},

		addPlugins: function() {
			this.plugins.each(this.addPlugin, this);
		}
	});

	var pluginsView =  new PluginsView;
	pluginsView.plugins.url = "<?php echo $fetch_url; ?>";
	pluginsView.plugins.reset(<?php echo $plugins_list; ?>);
});
</script>