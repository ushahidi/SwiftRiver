<div class="col_9">
	<article id="primer" class="container base">
		<header class="cf remove">
			<a href="#primer" class="remove-large">
				<span class="icon"></span>
				<span class="nodisplay"><?php echo __('ui.button.remove'); ?></span>
			</a>
			<div class="property-title">
				<h1><?php echo __("ui.user.getstarted"); ?></h1>
			</div>
		</header>
		<section class="property-parameters cf">
			<div class="parameter primer-item learn">
				<h3><a href="#">Learn how SwiftRiver works</a></h3>
			</div>
			<div class="parameter primer-item create">
				<h3><a href="<?php echo URL::site().$account->account_path.'/river/create'; ?>"><?php echo __('ui.river.create'); ?></a></h3>
			</div>
			<div class="parameter primer-item search">
				<h3><a href="<?php echo URL::site("search/main"); ?>" class="modal-trigger">Find stuff that interests you</a></h3>
			</div>
		</section>
	</article>

	<article class="container base">
		<?php if ($has_activity): ?>
		<header class="cf">
			<div class="property-title"><h1><?php echo __("ui.user.activity"); ?></h1></div>
		</header>
		<section id="activity_stream" class="property-parameters">
			<?php echo $activity_stream; ?>
		</section>
		<?php else: ?>
		<div class="alert-message blue">
			<p>
				<strong><?php echo __("Empty activity stream"); ?></strong>
				<?php echo __("There are no items in your activity stream"); ?>
			</p>
		</div>
		<?php endif; ?>
	</article>
	<?php
	/*
	<article class="container action-list base">
		<header class="cf">
			<div class="property-title">
				<h1><?php echo __("Popular this week"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<!-- List what has taken place this past week -->
		</section>
	</article>
	*/
	?>
</div>

<div class="col_3">
	<article class="container action-list base" id="buckets">
		<header class="cf">
			<div class="property-title">
				<h1>
					<a href="<?php echo URL::site().$account->account_path.'/buckets'; ?>">
						<?php echo __("ui.nav.buckets"); ?>
					</a>
				</h1>
			</div>
		</header>
		<section class="property-parameters asset-list" style="display:none;">
			<p class="category own-title" style="display:none;">
				<?php echo __("ui.user.buckets.mine"); ?>
			</p>
			<!-- Users's buckets go here -->
			
			<p class="category collaborating-title" style="display:none;">
				<?php echo __("ui.user.buckets.collaborate"); ?>
			</p>
			<!-- Bucket's user is collaborating on go here -->
			
			<p class="category following-title" style="display:none;">
				<?php echo __("ui.user.buckets.follow"); ?>
			</p>
			<!-- Buckets user is subscribed to go here -->
		</section>
		<section class="property-parameters empty-message" style="display:none">
			<div class="parameter">
				<p><a href="#"><?php echo __('ui.bucket.create.click'); ?></a></p>
			</div>
		</section>		
	</article>
	
	<article class="container action-list base" id="rivers">
		<header class="cf">
			<div class="property-title">
				<h1>
					<a href="<?php echo URL::site().$account->account_path.'/rivers'; ?>">
						<?php echo __("ui.nav.rivers"); ?>
					</a>
				</h1>
			</div>
		</header>
		<section class="property-parameters asset-list" style="display:none;">
			<p class="category own-title" style="display:none;">
				<?php echo __("ui.user.rivers.mine"); ?>
			</p>
			<!-- Users's rivers go here -->
			
			<p class="category collaborating-title" style="display:none;">
				<?php echo __("ui.user.rivers.collaborate"); ?>
			</p>
			<!-- Rivers's user is collaborating on go here -->
			
			<p class="category following-title" style="display:none;">
				<?php echo __("ui.user.rivers.follow"); ?>
			</p>
			<!-- Rivers user is subscribed to go here -->
		</section>
		<section class="property-parameters empty-message" style="display:none">
			<div class="parameter">
				<p><a href="<?php echo URL::site().$account->account_path.'/river/create'; ?>">
					<?php echo __('ui.river.create.click'); ?></a>
				</p>
			</div>
		</section>		
	</article>
</div>

<script type="text/template" id="dashboard-asset-list-item-template">
    <% if (parseInt(account_id) != logged_in_account) { %>
	<% var selected = (subscribed||collaborator) ? "selected" : ""; %>
	<div class="actions">
		<p class="button-white follow only-icon has-icon <%= selected %>">
			<a href="#" title="now following">
				<span class="icon"></span>
				<span class="nodisplay"></span>
			</a>
		</p>
	</div>
	<% } %>
	<h2><a href="<%= url %>"><%= display_name %></a></h2>
</script>
