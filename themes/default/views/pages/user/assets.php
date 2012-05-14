<div class="col_12" id="assets">
	<article class="container action-list base own" style="display:none">
		<header class="cf">
			<div class="property-title">
				<?php if ($owner): ?>
					<h1>Your <?php echo $asset; ?>s</h1>
				<?php else: ?>
					<h1><?php echo $visited_account->user->name; ?>'s <?php echo $asset; ?>s</h1>
				<?php endif; ?>
			</div>
		</header>
		<section class="property-parameters">
		</section>
	</article>
	
	<?php if ($owner): ?>
		<div class="alert-message blue empty-message" style="display:none">
			<?php if ($asset == 'river'): ?>
				<p><strong>No <?php echo $asset; ?>s.</strong> <a href="<?php echo URL::site().$visited_account->account_path.'/river/create'; ?>">Click here to create a <?php echo $asset; ?></a></p>
			<?php else: ?>
				<p><strong>No <?php echo $asset; ?>s.</strong> <a href="#">Click here to create a <?php echo $asset; ?></a></p>
			<?php endif; ?>
		</div>
	<?php else: ?>
		<div class="alert-message blue empty-message" style="display:none">
			<p><strong>No <?php echo $asset; ?>s.</strong> <?php echo $visited_account->user->name; ?> does not have public <?php echo $asset; ?>s.</p>
		</div>
	<?php endif; ?>
	
	<article class="container action-list base collaborating" style="display:none">
		<header class="cf">
			<div class="property-title">
				<?php if ($owner): ?>
					<h1><?php echo ucfirst($asset); ?>s you collaborate on</h1>
				<?php else: ?>
					<h1><?php echo ucfirst($asset); ?>s <?php echo $visited_account->user->name; ?> collaborates on</h1>
				<?php endif; ?>
			</div>
		</header>
		<section class="property-parameters">
			<!-- Bucket's user is collaborating on go here -->
		</section>
	</article>

	<article class="container action-list base following" style="display:none">
		<header class="cf">
			<div class="property-title">
				<?php if ($owner): ?>
					<h1><?php echo ucfirst($asset); ?>s you follow</h1>
				<?php else: ?>
					<h1><?php echo ucfirst($asset); ?>s <?php echo $visited_account->user->name; ?> follows</h1>
				<?php endif; ?>
			</div>
		</header>
		<section class="property-parameters">
			<!-- Buckets user is subscribed to go here -->
		</section>
	</article>
</div>

<script type="text/template" id="profile-asset-list-item-template">
<div class="parameter">
	<div class="actions">
		<p class="follow-count"><strong><%= subscriber_count %></strong> followers</p>
		<?php if ( ! $anonymous): ?>
			<% if (account_id == logged_in_account && logged_in_account == <?php echo $visited_account->id; ?>) { %>
				<p class="remove-small"><a href="/markup/modal-remove.php" class="modal-trigger"><span class="icon"></span><span class="nodisplay">Remove</span></a></p>
			<% } else if (account_id != logged_in_account){ %>
				<% var selected = (subscribed||collaborator) ? "selected" : ""; %>
				<p class="button-white follow only-icon has-icon <%= selected %>"><a href="#"><span class="icon"></span><span class="nodisplay"></span></a></p>
			<% } %>
		<?php endif; ?>
	</div>
	<!-- When viewing another users profile, do not namespace their rivers and buckets. -->
	<% 
		var display_name;
		if (is_owner && account_id == <?php echo $visited_account->id; ?>) {
			display_name = name;
		} else {
			display_name = name_namespaced;
		}
	%>
	<h2><a href="<%= url %>"><%= display_name %></a></h2>
</div>
</script>