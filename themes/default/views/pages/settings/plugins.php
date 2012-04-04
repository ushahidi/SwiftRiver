<?php echo Form::open(); ?> 
	<input type="hidden" name="action" value="">
	<input type="hidden" name="id" value="">
	<article class="container base">
		<header class="cf">
			<div class="property-title">
				<h1><?php echo __("Plugins"); ?></h1>
			</div>
		</header>
		<section id="plugin_listing" class="property-parameters">
		</section>
	</article>
<?php echo Form::close(); ?>

<script type="text/template" id="plugin_item_template">
	<div class="actions">
		<% var link_title = (plugin_enabled)
		       ? "<?php echo __('Deactivate Plugin'); ?>" 
		       : "<?php echo __('Activate Plugin'); ?>"; 

		    var selected = (plugin_enabled)? "selected" : "";
		%>
		<p class="button-white has-icon only-icon follow <%= selected %>">
			<a href="#" title="<%= link_title %>">
				<span class="icon">
				<span class="nodisplay"><%= link_title %></span>
			</a>
		</p>
	</div>
	<h2><a href="#" title="<%= plugin_name %>"><%= plugin_name %></a></h2>
	<p class="metadata"><%= plugin_description %></p>
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

		tagName: "div",

		className: "parameter",

		template: _.template($("#plugin_item_template").html()),

		events: {
			"click p.button-white > a": "toggleActivation"
		},

		toggleActivation: function(e) {
			targetEl = $(e.currentTarget).parent('p');
			this.model.toggleActivation(targetEl);
		},

		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		}
	});

	var PluginsView = Backbone.View.extend({
		el: "section#plugin_listing",

		initialize: function() {
			this.plugins = new PluginItemList;
			this.plugins.on("reset", this.addPlugins, this);
			this.plugins.on("add", this.addPlugin, this);
		},

		addPlugin: function(plugin) {
			view = new PluginItemView({model: plugin}).render().el;
			this.$el.append(view);
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