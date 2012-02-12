<div class="container list select data">

	<?php if ($has_buckets): ?>
	<div class="controls edit-advanced">
		<div class="row cf">
			<p class="button-go edit-single"><a href="#">Edit Bucket</a></p>
			<p class="button-view edit-multiple"><a href="<?php echo URL::site()?>dashboard/edit_multiple">Edit multiple</a></p>
			<p class="button-go create-new"><a href="<?php echo URL::site().'bucket/new'; ?>"><?php echo __('Create new');?></a></p>
		</div>
	</div>

	<?php else:?>
		<h2 class="null">
			<?php echo __('No Buckets to display yet'); ?> 
			<em><a href="<?php echo URL::site().'bucket/new'; ?>"><?php echo __('Create one.');?></a></em>
		</h2>
	<?php endif; ?>

</div>

<script type="text/template" id="bucket-list-item-template">
	<div class="content">
		<h1>
			<!--  Namespace the river name if logged in user is not the owner -->
			<% if (!is_owner) { %>
			<a href="<%= bucket_owner_url %>"><%= account_path %>/</a>
			<% } %>

			<a href="<%= bucket_url %>" class="title"><%= bucket_name %></a>
		</h1>
	</div>
	<div class="summary">
		<section class="actions">
			<div class="button">
				<p class="button-change">
					<a class="delete">
						<span class="icon"></span>
						<span class="nodisplay"><?php echo __('Delete Bucket'); ?></span>
					</a>
				</p>
				<div class="clear"></div>
				<div class="dropdown container">
					<p><?php echo __('Are you sure you want to delete this Bucket?'); ?></p>
					<ul>
						<li class="confirm">
							<a><?php echo __('Yep.'); ?></a>
						</li>
						<li class="cancel"><a><?php echo __('No, nevermind.'); ?></a></li>
					</ul>
				</div>
			</div>
		</section>
		<section class="meta">
			<p>
				<a href="#"><strong><%= subscriber_count %></strong> <?php echo __('Subscribers'); ?></a>
			</p>
		</section>
	</div>
</script>

<?php echo $buckets_js; ?>