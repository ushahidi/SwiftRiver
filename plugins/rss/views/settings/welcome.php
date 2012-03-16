<div id="start-urls-control">
	<div class="controls">
		<div class="row cf">
			<div class="input">
				<h3><?php echo __('RSS/Atom URL'); ?></h3>
				<?php echo Form::input("url"); ?>
				<button class="save" type="button">
					<span>Add</span>
				</button>
			</div>
		</div>
		<div class="global row cf">
			<div class="loading center"></div>
			<div class="system_error" style="display:none"></div>
			<div class="system_success" style="display:none"></div>
		</div>
	</div>
	<div class="container list select data">
		<ul class="urls">
		</ul>
	</div>
</div>


<script type="text/template" id="welcome-url-template">
	<div class="content">
		<hgroup>
			<h3><a href="#" class="title"><%= title %></a></h3>
			<span class="description"><%= url %></span>
		</hgroup>
	</div>
	<div class="summary">
		<section class="actions">
				<p class="button-delete"><a><?php echo __('Delete URL'); ?></a></p>
				<ul class="dropdown">
					<p><?php echo __('Are you sure you want to delete this URL?'); ?></p>
					<li class="confirm"><a><?php echo __('Yep.'); ?></a></li>
					<li class="cancel"><a onclick=""><?php echo __('No, nevermind.'); ?></a></li>
				</ul>
		</section>
	</div>
</script>

<script type="text/javascript">

$(function() {
	var StarterUrl = Backbone.Model.extend();
	
	var StarterUrls = Backbone.Collection.extend({		
		model: StarterUrl,
		url: "<?php echo URL::site('settings/rsswelcome/urls') ?>"
	});
	
	var StarterUrlView = Backbone.View.extend({
		
		tagName: "article",
		
		className: "item cf",
		
		template: _.template($("#welcome-url-template").html()),
		
		events: {
			"click .actions li.confirm": "deleteUrl"
		},
		
		render: function() {
			this.$el.html(this.template(this.model.toJSON()));
			return this;
		},
		
		deleteUrl: function() {
			view = this;
			this.model.destroy({
				wait: true,
				context: this,
				error: function() {
					flashMessage(view.$("div.system_error"), "Error removing the URL");
				},
				success: function() {
					view.$el.fadeOut("slow");
				}
			});
		}
	});
	
	var StarterUrlsApp = Backbone.View.extend({
	
		el: $("#start-urls-control"),
		
		events: {
			"click button.save": "createUrl",
			"keypress input[name=url]": "checkSubmit"
		},
						
		initialize: function() {
			this.urls = new StarterUrls;
			this.urls.on('add',	 this.addUrl, this);
			this.urls.on('reset', this.addUrls, this);
			
			this.$("input[name=url]").focus();
		},
		
		addUrl: function(url) {
			var view = new StarterUrlView({model: url});
			this.$("div.list").prepend(view.render().el);
		},
		
		addUrls: function() {
			this.urls.each(this.addUrl, this);
		},
		
		isPageFetching: false,
		
		createUrl: function() {
			var url = this.$("input[name=url]").val();
			
			if (!url.length || this.isPageFetching) 
				return;
				
			this.isPageFetching = true;
			
			var addButton = this.$("button.save");
			addButton.fadeOut();
			
			var loading_msg = window.loading_message.clone().append("<br />Adding url...");
			loading_msg.appendTo(this.$("div.loading")).show();
			
			this.urls.create({url: url}, {
				wait: true,
				context: this,
				complete: function() {
					this.isPageFetching = false;
					this.$("input[name=url]").val("");
					loading_msg.fadeOut().remove();
					addButton.fadeIn();
				},
				error: function(model, response, c) {
					if (response.status == 400) {
						flashMessage(this.$("div.global div.system_error"), response.responseText);
					} else {
						flashMessage(this.$("div.global div.system_error"), "Error");
					}
				}
			})
		},
		
		checkSubmit: function(e) {
			if(e.which == 13){
				this.createUrl();
			}
		}
	});
	
	var app = new StarterUrlsApp;
	app.urls.reset(<?php echo $urls ?>);	
});

</script>