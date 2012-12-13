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
			<div class="page-actions col_3">
				<?php if ($owner): ?>
					<h2 class="settings">
						<a href="<?php echo $settings_url; ?>">
							<span class="icon"></span>
							<?php echo __("Bucket settings"); ?>
						</a>
					</h2>
				<?php endif; ?>
				<h2 class="back">
					<a href="<?php echo $bucket_url; ?>">
						<span class="icon"></span>
						<?php echo __('Return to bucket'); ?>
					</a>
				</h2>
			</div>
		</div>
	</hgroup>

	<div id="content" class="list cf">
		<div class="center">
			<div class="center no-content nodisplay">
				<div class="col_12">
					<article class="container base">
						<div class="alert-message blue">
							<p>
								<strong><?php echo __('Nothing to display yet.') ?></strong>
								<?php echo __('There are no comments in this bucket yet. Add the first one below.'); ?>
							</p>
						</div>
					</article>
				</div>
			</div>
			<div class="col_12">
				<div id="comments"></div>
				<div id="comments_error"></div>
				<div id="comments_footer">
					<?php if ( ! $anonymous):?>
						<article class="add-comment drop base cf">
							<div class="drop-content">
								<div class="drop-body">
									<textarea id="comment_content"></textarea>
								</div>
								<section class="drop-source cf">
									<a href="#" class="avatar-wrap"><img src="<?php echo $user_avatar; ?>" /></a>
									<div class="byline">
										<h2><?php echo $user->name; ?></h2>
									</div>
								</section>
							</div>
							<div class="drop-actions cf">
								<p class="button-blue"><a href="#">Publish</a></p>
							</div>
						</article>
					<?php endif;?>
				</div>
								
			</div>
		</div>
	</div>

	<script type="text/template" id="comment-item-template">
		<article class="drop base cf" id="comment-<%= id %>">
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
			<% if (user_id != logged_in_user) { %>
				<div class="drop-actions stacked cf">
					<ul class="dual-buttons score-drop">
						<li class="button-white like <%= parseInt(score) == 1 ? 'scored' : ''  %>"><a href="#"><span class="icon"></span></a></li>
						<li class="button-white dislike <%= parseInt(score) == -1 ? 'scored' : ''  %>"><a href="#"><span class="icon"></span></a></li>
					</ul>
				</div>
			<% } %>
		</article>	
	</script>

	<script type="text/javascript">
	/**
	 * Backbone.js wiring for the comments MVC
	 */
	$(function() {
		var fetch_url = '<?php echo $fetch_url ?>';

		// Models
		var Comment = Backbone.Model.extend({
			// Score the comment
			score: function(val) {
				this.save({score: val});
			}			
		});
		
		var CommentCollection = Backbone.Collection.extend({
			model:Comment,
			url: fetch_url
		});
		 		 
		// Single comment in the comment list
		var CommentView = Backbone.View.extend({

			template:_.template($('#comment-item-template').html()),
		 
			initialize:function () {
				this.model.bind("change", this.render, this);
				this.model.bind("destroy", this.close, this);
			},
		 
			render:function (eventName) {
				$(this.el).html(this.template(this.model.toJSON()));
				return this;
			},

			events:{
				"click ul.score-drop > li.like a": "likeComment",
				"click ul.score-drop > li.dislike a": "dislikeComment",
			},

			close:function () {
				$(this.el).unbind();
				$(this.el).remove();
			},

			showCommentScore: function(selector) {
				var el = this.$(selector);
				el.toggleClass('scored');
				if (el.hasClass("scored")) {
					el.siblings("li").removeClass("scored");
				}
			},			

			likeComment: function() {
				this.model.score(1);
				return false;
			},
			
			dislikeComment: function() {
				this.model.score(-1);
				return false;
			}
		});
		 
		// Listing of comments
		var CommentListView = Backbone.View.extend({
		 
			el: "#content",
			
			events: {
				"click #comments_footer .drop-actions .button-blue": "publishComment",
			},
		 
			initialize:function () {
				this.collection.on("reset", this.addComments, this);
				this.collection.on("add", this.addComment, this);
			},

			addComments: function() {
				this.collection.each(this.addComment, this);
				if (!this.collection.length) {
					this.$(".no-content").show();
				}
			},
			
			addComment: function(comment) {
				var view = new CommentView({model: comment});
				this.$("#comments").append(view.render().el);
			},
			
			publishComment: function() {
				var textarea = this.$(".add-comment textarea");
			
				if (!$(textarea).val().length)
					return false;

				var publishButton = this.$(".add-comment .drop-actions p").clone();            
				var loading_msg = window.loading_message.clone();
				this.$(".add-comment .drop-actions p").replaceWith(loading_msg);
				var view = this;
				
				this.collection.create({comment_content: $(textarea).val()}, {
					wait: true,
					complete: function() {
						loading_msg.replaceWith(publishButton);
					},
					success: function(model, response) {
						textarea.val("");
						view.$(".no-content").hide();
					},
					error: function(model, response) {
						showConfirmationMessage("Unable to add comment. Try again later.");
					}
				});
			
				return false;
				
			}	 
		});
		
		var commentList = new CommentCollection;
		new CommentListView({collection: commentList});
		commentList.reset(<?php echo $comments; ?>);
	});
	</script>

	
