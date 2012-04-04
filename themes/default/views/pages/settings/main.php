<?php echo Form::open(NULL, array('id' => 'form_site_settings')); ?>
	<article class="container base" id="alert_messages" style="display:none">
		<div class="alert-message red">
			<p><?php echo __("Oops! Something went wrong while processing your request"); ?></p>
		</div>
	</article>

	<article class="container base">
		<header class="cf">
			<div class="property-title">
				<h1><?php echo __('Name'); ?></h1>
			</div>
		</header>

		<section class="property-parameters">
			<div class="parameter">
				<label for="site_name">
					<p class="field"><?php echo __("Site Name"); ?></p>
					<?php echo Form::input("site_name", $settings['site_name']); ?>
				</label>
			</div>
		</section>
	</article>
	<article class="container base">
		<header class="container cf">
			<div class="property-title">
				<h1><?php echo __("Locale"); ?></h1>
			</div>
		</header>

		<section class="property-parameters">
			<div class="parameter">
				<label for="site_locake">
					<p class="field"><?php echo __("Site Locale"); ?></p>
					<?php echo Form::input("site_locale", $settings['site_locale']); ?>
				</label>
			</div>
		</section>
	</article>
	<article class="container base">
		<header class="container cf">
			<div class="property-title">
				<h1><?php echo __("Access"); ?></h1>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<label for="public_registration_enabled">
					<?php echo Form::checkbox('public_registration_enabled', 1,  (bool)$settings['public_registration_enabled']); ?>
					<?php echo __('Allow public registration'); ?>
				</label>
			</div>
			<div class="parameter">
				<label for="anonymous_access_enabled">
					<?php echo Form::checkbox('anonymous_access_enabled', 1,  (bool)$settings['anonymous_access_enabled']); ?>
					<?php echo __('Allow anonymous access'); ?>
				</label>
			</div>
		</section>
	</article>
<?php echo Form::close(); ?>

<script type="text/javascript">
$(function() {
	var authToken;
	handleSettingsParam = function(e) {
		var field = e.data;
		if (typeof authToken == 'undefined') {
			authToken = "<?php echo CSRF::token(); ?>";
		}

		var fieldVal = $(field).val();
		if ($(field).attr("type") == 'checkbox') {
			fieldVal = $(field).attr("checked") ? 1 : 0;
		}

		$.ajax({
			type: "POST",
			url: "<?php echo $action_url ?>",
			async: false,
			data: {
				auth_token: authToken,
				key: $(field).attr("name"),
				value: fieldVal
			},
			dataType: "json",
			success: function(response) {
				$("#alert_messages").fadeOut();
				authToken = response.token;
			},
			error: function() {
				$("#alert_messages").fadeIn();
			}
		});
	}

	var inputFields = $("#form_site_settings input[type!= \"hidden\"]");
	$(inputFields).each(function(i, field) {
		$(field).change(field, handleSettingsParam);
	});

});
</script>