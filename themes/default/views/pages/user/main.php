<div class="col_9">
	<article class="container base">
		<header class="cf">
			<div class="property-title"><h1><?php echo __("Activity"); ?></h1></div>
		</header>
		<section id="activity_stream" class="property-parameters">
			<?php echo $activity_stream; ?>
		</section>
	</article>
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
</div>

<div class="col_3">
	<article class="container action-list base">
		<header class="cf">
			<div class="property-title">
				<h1><a href="<?php echo URL::site().$account->account_path.'/profile'; ?>"><?php echo __("Rivers"); ?></a></h1>
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
				<h1><a href="<?php echo URL::site().$account->account_path.'/profile'; ?>"><?php echo __("Buckets"); ?></a></h1>
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