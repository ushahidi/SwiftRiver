<div class="col_9">
	<div id="primer" class="base cf">
		<div class="primer-item learn">
			<h3 class="button-blue"><a href="#">Learn how SwiftRiver works</a></h3>
			<a href="#" class="primer-image"><img src="/markup/images/content/primer-learn.gif" /></a>
		</div>
		<div class="primer-item create">
			<h3 class="button-blue"><a href="<?php echo URL::site().$account->account_path.'/river/create'; ?>">Create a river</a></h3>
			<a href="<?php echo URL::site().$account->account_path.'/river/create'; ?>" class="primer-image"><img src="/markup/images/content/primer-create.gif" /></a>
		</div>
		<div class="primer-item search">
			<h3 class="button-blue"><a href="/markup/modal-search.php" class="modal-trigger">Find stuff that interests you</a></h3>
			<a href="/markup/modal-search.php" class="primer-image modal-trigger"><img src="/markup/images/content/primer-search.gif" /></a>
		</div>
	</div>
	<p class="remove"><a href="#primer">Hide these options</a></p>

	<article class="container base">
		<?php if ($has_activity): ?>
		<header class="cf">
			<div class="property-title"><h1><?php echo __("Activity"); ?></h1></div>
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
	<article class="container action-list base">
		<header class="cf">
			<div class="property-title">
				<h1>
					<a href="<?php echo URL::site().$account->account_path.'/rivers'; ?>">
						<?php echo __("Rivers"); ?>
					</a>
				</h1>
			</div>
		</header>
		<section id="river_listing" class="property-parameters">
			<p id="owned_rivers" class="category"><?php echo __("Your Rivers"); ?></p>
			<!-- Add list of rivers owned and those the user is collaborating on -->

			<p id="subscribed_rivers" class="category"><?php echo __("Rivers you follow"); ?></p>
			<!-- Add only the list of rivers the user is subscribed to -->
		</section>
	</article>

	<article class="container action-list base">
		<header class="cf">
			<div class="property-title">
				<h1>
					<a href="<?php echo URL::site().$account->account_path.'/buckets'; ?>">
						<?php echo __("Buckets"); ?>
					</a>
				</h1>
			</div>
		</header>
		<section id="bucket_listing" class="property-parameters">
			<p id="owned_buckets" class="category"><?php echo __("Your Buckets"); ?></p>
			<!-- Add the list of buckets owned and those the user is collaborating on -->

			<p id="subscribed_buckets" class="category"><?php echo __("Buckets you follow"); ?></p>
			<!-- Add only the list of buckets the user is subscribed to -->
		</section>
	</article>
</div>

<?php echo $profile_js; ?>
