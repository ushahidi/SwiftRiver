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
	</article>
</div>

<div class="col_9">
	<article class="container action-list base">
		<?php if (count($rivers) == 0): ?>
		<div class="alert-message blue">
			<p>
				<strong><?php echo __("No rivers"); ?></strong>
				<?php echo __(":user_name does not have any rivers", array(":user_name" => $account->user->name)); ?>
			</p>
		</div>
		<?php else: ?>
		<header class="cf">
			<div class="property-title">
				<h1>
					<a href="<?php echo URL::site().$account->account_path.'/rivers'; ?>">
						<?php echo __("Rivers"); ?>
					</a>
				</h1>

				<?php if ( ! $owner): ?>
				<p id="subscribe_all_rivers" class="button-white add-parameter follow">
					<a href="#" title="<?php echo __("Subscribe"); ?>">
						<span class="icon"></span><?php echo __("Subscribe to all"); ?>
					</a>
				</p>
				<?php endif; ?>

			</div>
		</header>
		<section id="river_listing" class="property-parameters">
			<!-- List of all rivers accessible to the visitor -->
		</section>
		<?php endif; ?>
	</article>
	
	<article class="container action-list base">
		<?php if (count($buckets) == 0): ?>
		<div class="alert-message blue">
			<p>
				<strong><?php echo __("No buckets"); ?></strong>
				<?php echo __(":user_name does not have any buckets", array(":user_name" => $account->user->name)); ?>
			</p>
		</div>
		<?php else: ?>
		<header class="cf">
			<div class="property-title">
				<h1>
					<a href="<?php echo URL::site().$account->account_path.'/buckets'; ?>">
						<?php echo __("Buckets"); ?>
					</a>
				</h1>

				<?php if ( ! $owner): ?>
				<p id="subscribe_all_buckets" class="button-white add-parameter follow">
					<a href="#" title="<?php echo __("Subscribe"); ?>">
						<span class="icon"></span><?php echo __("Subscribe to all"); ?>
					</a>
				</p>
				<?php endif; ?>

			</div>
		</header>
		<section id="bucket_listing" class="property-parameters">
			<!-- List of all buckets accessible to the visitor -->
		</section>
		<?php endif; ?>
	</article>
</div>

<?php echo $profile_js; ?>