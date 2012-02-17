<div class="container list select data">

	<?php if ($has_items): ?>
	<div class="controls edit-advanced">
		<div class="row cf">
			<p class="button-go edit-single"><a href="#"><?php echo __("Edit ".ucfirst($action_object)); ?></a></p>
			<p class="button-view edit-multiple"><a href="<?php echo URL::site()?>dashboard/edit_multiple">Edit multiple</a></p>
			<p class="button-go create-new"><a href="<?php echo $new_item_url; ?>"><?php echo __('Create new');?></a></p>
		</div>
	</div>

	<?php else: ?>
		<h2 class="null">
			<?php echo __('No '.ucfirst($action_object).'s to display yet'); ?> 
			<em><a href="<?php echo $new_item_url; ?>"><?php echo __('Create one.');?></a></em>
		</h2>
	<?php endif; ?>

</div>

<script type="text/template" id="list-item-template">
	<div class="content">
		<h1>
			<% if (!is_owner) { %>
			<a href="<%= item_owner_url %>"><%= account_path %>/</a>
			<% } %>

			<a href="<%= item_url %>" class="title"><%= item_name %></a>
		</h1>
	</div>
	<div class="summary">
		<section class="actions">
			<div class="button">
				<p class="button-change">
					<a class="delete">
						<span class="icon"></span>
						<span class="nodisplay"><?php echo __('Delete '.ucfirst($action_object)); ?></span>
					</a>
				</p>
				<div class="clear"></div>
				<div class="dropdown container">
					<p><?php echo __('Are you sure you want to delete this '.$action_object.'?'); ?></p>
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

<?php echo $rivers_buckets_js; ?>