<div class="col_9">
	<article id="primer" class="container base">
		<header class="cf">
			<div class="property-title col_6">
				<h1>Get started</h1>
			</div>
		</header>
		<section class="property-parameters cf">
			<div class="parameter primer-item learn">
				<h3><a href="#">Learn how SwiftRiver works</a></h3>
			</div>
			<div class="parameter primer-item create">
				<h3><a href="/markup/river/new.php">Create a river</a></h3>
			</div>
			<div class="parameter primer-item search">
				<h3><a href="/markup/modal-search.php" class="modal-trigger">Find stuff that interests you</a></h3>
			</div>
		</section>
	</article>

	<article class="container base">
		<?php if ($has_activity): ?>
		<header class="cf">
			<div class="property-title col_12"><h1><?php echo __("Activity"); ?></h1></div>
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
			<div class="property-title col_12">
				<h1>
					<a href="<?php echo URL::site().$account->account_path.'/buckets'; ?>">
						<?php echo __("Buckets"); ?>
					</a>
				</h1>
			</div>
		</header>
		<section class="property-parameters asset-list" style="display:none;">
			<h2 class="category own-title" style="display:none;"><?php echo __("Your Buckets"); ?></h2>
			<!-- Users's buckets go here -->
			
			<h2 class="category collaborating-title" style="display:none;"><?php echo __("Buckets You Collaborate On"); ?></h2>
			<!-- Bucket's user is collaborating on go here -->
			
			<h2 class="category following-title" style="display:none;"><?php echo __("Buckets You Follow"); ?></h2>
			<!-- Buckets user is subscribed to go here -->
		</section>
		<section class="property-parameters empty-message" style="display:none">
			<div class="parameter">
				<p><a href="#">Create a bucket</a></p>
			</div>
		</section>		
	</article>	
	
	<article class="container action-list base" id="rivers">
		<header class="cf">
			<div class="property-title col_12">
				<h1>
					<a href="<?php echo URL::site().$account->account_path.'/rivers'; ?>">
						<?php echo __("Rivers"); ?>
					</a>
				</h1>
			</div>
		</header>
		<section class="property-parameters asset-list" style="display:none;">
			<h2 class="category own-title" style="display:none;"><?php echo __("Your Rivers"); ?></h2>
			<!-- Users's rivers go here -->
			
			<h2 class="category collaborating-title" style="display:none;"><?php echo __("Rivers You Collaborate On"); ?></h2>
			<!-- Rivers's user is collaborating on go here -->
			
			<h2 class="category following-title" style="display:none;"><?php echo __("Rivers You Follow"); ?></h2>
			<!-- Rivers user is subscribed to go here -->
		</section>
		<section class="property-parameters empty-message" style="display:none">
			<div class="parameter">
				<p><a href="<?php echo URL::site().$account->account_path.'/river/create'; ?>">Click here to create a river</a></p>
			</div>
		</section>		
	</article>
</div>

<script type="text/template" id="dashboard-asset-list-item-template">
    <% if (parseInt(account_id) != logged_in_account) { %>
	<% var selected = (subscribed||collaborator) ? "selected" : ""; %>
	<div class="actions">
		<p class="button-white follow only-icon has-icon <%= selected %>"><a href="#" title="now following"><span class="icon"></span><span class="nodisplay"></span></a></p>
	</div>
	<% } %>
	<h3><a href="<%= url %>"><%= display_name %></a></h3>
</script>