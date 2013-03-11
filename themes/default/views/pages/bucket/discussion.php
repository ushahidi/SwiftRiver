<hgroup class="page-title cf">
	<div class="center">
		<div class="col_9">
			<h1><?php echo $bucket['name']; ?> <em><?php echo __("Discussions"); ?></em></h1>
		</div>
		<div class="page-action col_3">
			<a class="button button-white" href="<?php echo $bucket_base_url; ?>"><?php echo __('Return to bucket'); ?></a>
		</div>			
	</div>
</hgroup>

<div id="content" class="river list cf">
	<div class="center">
		<section id-"filters" class="col_3">
			<div class="modal-window">
				<div class="modal">
					<ul class="filters-primary">
						<?php foreach ($nav as $item): ?>
						<li id="<?php echo $item['id']; ?>" class="<?php echo $item['active'] == $active ? 'active' : ''; ?>">
							<a href="<?php echo $bucket_base_url.$item['url']; ?>">
								<?php echo $item['label'];?>
							</a>
						</li>
						<?php endforeach; ?>
				</div>
			</div>
		</section>
		
		<div id="stream" class="col_9">
		</div>
	</div>
</div>

<script type="text/template" id="comment-item-template">
<section class="drop-source cf">
	<a href="#" class="avatar-wrap"><img src="<%= account.owner.avatar %>" /></a>
	<div class="byline">
		<h2><%= account.owner.name %></h2>
	</div>
</section>
<div class="drop-body">
	<div class="drop-content">
		<h1><%= comment_text %></h1>
	</div>
	<div class="drop-details">
		<p class="metadata">
			<%= date_added %>
		</p>
		<div class="drop-actions cf">
			<ul class="drop-status cf" style="display: none;">
				<li class="drop-status-read"><a href="#"><span class="icon-checkmark"></span></a></li>
				<li class="drop-status-remove"><a href="#"><span class="icon-cancel"></span></a><li>
			</ul>
		</div>
	</div>
</div>
</script>

<script type="text/template" id="comment-area-template">
<article class="drop base cf">
	<section class="drop-source cf">
		<a href="#" class="avatar-wrap">
			<img src="<?php echo $user['owner']['avatar']; ?>" />
		</a>
		<div class="byline">
			<h2><?php echo __($user['owner']['name']); ?></h2>
		</div>
	</section>
	<div class="drop-body" id="add-comment">
		<div class="drop-content">
			<?php echo Form::textarea('drop_comment', NULL, array('style' => 'width: 95%;', 'rows' => '4')); ?>
		</div>
		<div class="drop-details">
			<div class="drop-actions cf">
				<a href="#" id="publish-comment" class="button-primary"><?php echo __("Publish"); ?></a>
			</div>
		</div>
	</div>
</article>	
</script>


<script type="text/javascript">
/**
 * Backbone.js wiring for the comments MVC
 */
$(function() {
	var fetch_url = '<?php echo $fetch_url ?>';
	
	var accountId = '<?php echo $user['id']; ?>';

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

	var commentList = new CommentCollection;
		 		 
	// Single comment in the comment list
	var CommentView = Backbone.View.extend({

		tagName: "article",
		
		className: "drop base cf",

		template:_.template($('#comment-item-template').html()),

		initialize:function () {
			this.model.bind("change", this.render, this);
		},
		 
		render:function (eventName) {
			$(this.el).html(this.template(this.model.toJSON()));
			return this;
		},

		events:{
			"click ul.score-drop > li.like a": "likeComment",
			"click ul.score-drop > li.dislike a": "dislikeComment",
			"click li.drop-status-remove a": "deleteComment",
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
		},
		
		deleteComment: function() {
			var view = this;
			this.model.destroy({
				wait: true,
				success: function(){
					showSuccessMessage("Comment successfully deleted!", {flash: true});
					view.$el.fadeOut('slow');
				},
				error: function(){
					showFailureMessage("The comment could not be deleted");
				}
			});
			return false;
		}
	});
		 
	// Listing of comments
	var CommentListView = Backbone.View.extend({

		el: "#content",
		
		selectedOwner: "all",

		commentAreaTemplate: _.template($("#comment-area-template").html()),
			
		events: {
			"click #publish-comment": "publishComment",
			"click #all-comments-link a": "showAllComments",
			"click #my-comments-link a": "showOwnComments"
		},

		initialize:function () {
			this.collection.on("reset", this.addComments, this);
			this.collection.on("add", this.addComment, this);
		},
		
		ownComments: function() {
			return this.collection.filter(function(comment){
				return comment.get('account').id == accountId;
			});
		},

		addComments: function() {
			this.updateCommentsView();
		},
			
		addComment: function(comment) {
			var view = new CommentView({model: comment});
			this.$("#stream").prepend(view.render().el);
		},
			
		publishComment: function() {
			var textarea = this.$("#add-comment textarea");
			
			if (!$(textarea).val().length)
				return false;

			var publishButton = this.$("#add-comment .drop-actions p").clone();            
			var loading_msg = window.loading_message.clone();
			this.$(".add-comment .drop-actions p").replaceWith(loading_msg);
			var view = this;
				
			this.collection.create({comment_text: $(textarea).val()}, {
				wait: true,
				complete: function() {
					loading_msg.replaceWith(publishButton);
				},
				success: function(model, response) {
					textarea.val("");
					view.$(".no-content").hide();
					showSuccessMessage("Your comment has been successfully posted.", {flash: true});
				},
				error: function(model, response) {
					showFailureMessage("failure", "Error", "Unable to add comment. Try again later.", false);
				}
			});
			
			return false;
				
		},

		showOwnComments: function(e) {
			var parentEl = $(e.currentTarget).parent();
			if (parentEl.hasClass("active")) {
				return false;
			}
			this.$("ul.filters-primary li").removeClass('active');
			parentEl.addClass('active');

			this.selectedOwner = "all";
			return false;
		},
		
		showAllComments: function(e) {
			var parentEl = $(e.currentTarget).parent();
			if (parentEl.hasClass("active")) {
				return false;
			}

			this.$("ul.filters-primary li").removeClass('active');
			parentEl.addClass('active');

			this.selectedOwner = "mine";
			return false;
		},
		
		updateCommentsView: function() {
			var filterList = this.selectedOwner == "mine" ? this.ownComments() : this.collection.models;

			this.$("#stream article").fadeOut('slow').remove();
			_.each(filterList, this.addComment, this);
			this.$("#stream").append(this.commentAreaTemplate());
			
		}

	});
		
	new CommentListView({collection: commentList});
	commentList.reset(<?php echo $comments; ?>);
});
</script>
