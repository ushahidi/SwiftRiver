	<hgroup class="page-title bucket-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1><?php print $page_title; ?></h1>
				<?php if ( ! empty($collaborators)): ?>
				<div class="rundown-people">
					<h2>Collaborators on this bucket</h2>
					<ul>
						<?php foreach ($collaborators as $collaborator): ?>
							<li><a href="<?php echo URL::site().$collaborator['account_path'] ?>" class="avatar-wrap" title="<?php echo $collaborator['name']; ?>"><img src="<?php echo $collaborator['avatar']; ?>" /></a></li>
						<?php endforeach;?>
					</ul>
				</div>
				<?php endif; ?>			
			</div>
			<?php if ($owner): ?>
			<div class="page-actions col_3">
				<h2 class="settings">
					<a href="<?php echo $settings_url; ?>">
						<span class="icon"></span>
						<?php echo __("Bucket settings"); ?>
					</a>
				</h2>
				<h2 class="back">
					<a href="<?php echo $bucket_url; ?>">
						<span class="icon"></span>
						<?php echo __('Return to bucket'); ?>
					</a>
				</h2>
			</div>
			<?php else: ?>
			<div class="follow-summary col_3">
				<p class="button-score button-white follow"><a href="#" title="now following"><span class="icon"></span>Follow</a></p>
			</div>
			<?php endif; ?>
		</div>
	</hgroup>

	<div id="content" class="list cf">
		<div class="center">
			<div class="col_12">

				<div id="comments"></div>
				<div id="comments_error"></div>
				<div id="comments_footer"></div>
								
			</div>
		</div>
	</div>

	<script type="text/template" id="comment-list-template"></script>

	<script type="text/template" id="comment-item-template">
		<article class="drop base cf">
			<div class="drop-content">
				<div class="drop-body">
					<h1><%= comment_content %></h1>
					<p class="metadata discussion"><%= date %></p>
				</div>
				<section class="drop-source cf">
					<a href="#" class="avatar-wrap"><img src="<%= avatar %>" /></a>
					<div class="byline">
						<h2><%= name %></h2>
					</div>
				</section>
			</div>
			<div class="drop-actions stacked cf">
				<ul class="dual-buttons score-drop">
					<li class="button-white like"><a href="#"><span class="icon"></span></a></li>
					<li class="button-white dislike"><a href="#"><span class="icon"></span></a></li>
				</ul>
			</div>
		</article>	
	</script>

	<script type="text/template" id="comment-footer-template">
		<article class="add-comment drop base cf">
			<div class="drop-content">
				<div class="drop-body">
					<textarea id="comment_content"></textarea>
				</div>
				<section class="drop-source cf">
					<a href="#" class="avatar-wrap"><img src="/markup/images/content/avatar4.jpg" /></a>
					<div class="byline">
						<h2>Brandon Rosage</h2>
					</div>
				</section>
			</div>
			<div class="drop-actions cf">
				<p class="button-blue"><a href="#">Publish</a></p>
			</div>
		</article>	
	</script>

	<script type="text/template" id="comment-error-template">
		<div class="alert-message red">
			<p><strong><?php echo __('Uh oh.');?></strong> <?php echo __('Invalid Comment'); ?></p>
		</div>
	</script>

	<script type="text/javascript">
	/**
	 * Backbone.js wiring for the comments MVC
	 */
	$(function() {
		var fetch_url = '<?php echo $fetch_url ?>';

		// Models
		window.Comment = Backbone.Model.extend({
			urlRoot: fetch_url
		});
		 
		window.CommentCollection = Backbone.Collection.extend({
			model:Comment,
			url: fetch_url
		});
		 
		// Views
		window.CommentListView = Backbone.View.extend({
		 
			//tagName:'ul',
		 
			initialize:function () {
				this.model.bind("reset", this.render, this);
				var self = this;
				this.model.bind("add", function (comment) {
					$(self.el).append(new CommentListItemView({model:comment}).render().el);
				});
			},
		 
			render:function (eventName) {
				_.each(this.model.models, function (comment) {
					$(this.el).append(new CommentListItemView({model:comment}).render().el);
				}, this);
				return this;
			},

			events:{
				
			}		
		});
		 
		window.CommentListItemView = Backbone.View.extend({

			//tagName:"li",

			template:_.template($('#comment-item-template').html()),
		 
			initialize:function () {
				this.model.bind("change", this.render, this);
				this.model.bind("destroy", this.close, this);
			},
		 
			render:function (eventName) {
				$(this.el).html(this.template(this.model.toJSON()));
				return this;
			},
		 
			close:function () {
				$(this.el).unbind();
				$(this.el).remove();
			}
		});
		 
		window.CommentView = Backbone.View.extend({
		 
			template:_.template($('#comment-list-template').html()),
		 
			initialize:function () {
				this.model.bind("change", this.render, this);
			},
		 
			render:function (eventName) {
				$(this.el).html(this.template(this.model.toJSON()));
				return this;
			},
		 
			events:{

			},
		 
			change:function (event) {
				var target = event.target;
				console.log('changing ' + target.id + ' from: ' + target.defaultValue + ' to: ' + target.value);
				// You could change your model on the spot, like this:
				// var change = {};
				// change[target.name] = target.value;
				// this.model.set(change);
			},
		 
			close:function () {
				$(this.el).unbind();
				$(this.el).empty();
			}
		});
		 
		window.FooterView = Backbone.View.extend({
			
			template:_.template($('#comment-footer-template').html()),
		 
			initialize:function () {
				//this.render();
			},
		 
			render:function (eventName) {
				$(this.el).append(this.template());
				return this;
			},

			events:{
				"click .button-blue":"saveComment"
			},

			saveComment:function (event) {
				var newComment = new window.Comment;
				newComment.set({
					comment_content:$('#comment_content').val(),
					name:'<?php echo $user->name; ?>',
					date:'<?php echo date('Y-m-d H:i:s', time()); ?>',
					avatar:'<?php echo Swiftriver_Users::gravatar($user->email); ?>'
				});
				newComment.save(
					newComment.attributes,
					{
						success: function (model, response) {
							//app.commentList.create(newComment);
							commentList.add(newComment);
							$('#comment_content').val('');
							$('#comments_error').html('');
						},
						error: function (model, response) {
							$('#comments_error').html(new ErrorView().render().el);
						}
					}
				);

				return false;				
			}
		});

		window.ErrorView = Backbone.View.extend({
			template:_.template($('#comment-error-template').html()),
		 
			initialize:function () {
				//this.render();
			},
		 
			render:function (eventName) {
				$(this.el).append(this.template());
				return this;
			},
		});		
		
		// Router
		var AppRouter = Backbone.Router.extend({
		 
			routes:{
				"":"list"
			},
		 
			initialize:function () {
				$('#comments_footer').html(new FooterView().render().el);
			},
		 
			list:function () {
				commentList = new CommentCollection();
				this.commentListView = new CommentListView({model:commentList});
				commentList.fetch();
				$('#comments').html(this.commentListView.render().el);
			} 
		});
		
		var commentList;
		var app = new AppRouter();
		Backbone.history.start();
	});
	</script>	



