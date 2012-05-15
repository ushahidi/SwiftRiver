<div class="col_3">
	<article class="container base">
		<header class="cf">
			<div class="property-title">
				<h1><?php echo __("Activity"); ?></h1>
			</div>
		</header>
		<section id="activity_stream" class="property-parameters">
			<?php echo $activity_stream; ?>
		</section>
		<?php if ( ! $has_activity): ?>
			<section class="property-parameters empty-message">
				<div class="parameter">
					<p>No recent activity.</p>
				</div>
			</section>
		<?php endif; ?>
	</article>
</div>

<div class="col_9">
	<article class="container action-list base" id="buckets">
		<header class="cf">
			<div class="property-title">
				<h1>
					<a href="<?php echo URL::site().$account->account_path.'/buckets'; ?>"><?php echo __("Buckets"); ?></a>
				</h1>
			</div>
		</header>
		<section class="property-parameters asset-list" style="display:none;">
			<p class="category own-title" style="display:none;"><?php echo __(":name's Buckets", array(':name' => $account->user->name)); ?></p>
			<!-- Users's buckets go here -->
			
			<p class="category collaborating-title" style="display:none;"><?php echo __("Buckets :name Collaborates On", array(':name' => $account->user->name)); ?></p>
			<!-- Bucket's user is collaborating on go here -->
			
			<p class="category following-title" style="display:none;"><?php echo __("Buckets :name Follows", array(':name' => $account->user->name)); ?></p>
			<!-- Buckets user is subscribed to go here -->
		</section>
		<section class="property-parameters empty-message" style="display:none">
			<div class="parameter">
				<p><?php echo $account->user->name; ?> does not have any public buckets.</p>
			</div>
		</section>
	</article>
	
	<article class="container action-list base" id="rivers">
		<header class="cf">
			<div class="property-title">
				<h1>
					<a href="<?php echo URL::site().$account->account_path.'/rivers'; ?>"><?php echo __("Rivers"); ?></a>
				</h1>
			</div>
		</header>
		<section class="property-parameters asset-list" style="display:none;">
			<p class="category own-title" style="display:none;"><?php echo __(":name's Rivers", array(':name' => $account->user->name)); ?></p>
			<!-- Users's rivers go here -->
			
			<p class="category collaborating-title" style="display:none;"><?php echo __("Rivers :name Collaborates On", array(':name' => $account->user->name)); ?></p>
			<!-- Rivers's user is collaborating on go here -->
			
			<p class="category following-title" style="display:none;"><?php echo __("Rivers :name Follows", array(':name' => $account->user->name)); ?></p>
			<!-- Rivers user is subscribed to go here -->
		</section>
		<section class="property-parameters empty-message" style="display:none">
			<div class="parameter">
				<p><?php echo $account->user->name; ?> does not have any public rivers.</p>
			</div>
		</section>
	</article>
</div>

<script type="text/template" id="profile-asset-list-item-template">
	<?php if ( ! $anonymous): ?>
    	<% if (parseInt(account_id) != logged_in_account) { %>
		<% var selected = (subscribed||collaborator) ? "selected" : ""; %>
		<div class="actions">
			<p class="button-white follow only-icon has-icon <%= selected %>"><a href="#"><span class="icon"></span><span class="nodisplay"></span></a></p>
		</div>
		<% } %>
	<?php endif; ?>
	<!-- When viewing another users profile, do not namespace their rivers and buckets. -->
	<% 
		var display_name;
		if (is_owner && account_id == <?php echo $account->id; ?>) {
			display_name = name;
		} else {
			display_name = name_namespaced;
		}
	%>
	<h2><a href="<%= url %>"><%= display_name %></a></h2>
</script>