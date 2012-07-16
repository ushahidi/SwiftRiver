<div class="col_12">
	<?php if (isset($errors)): ?>
		<div class="alert-message red">
		<p><strong>Uh oh.</strong></p>
		<ul>
			<?php if (is_array($errors)): ?>
				<?php foreach ($errors as $error): ?>
					<li><?php echo $error; ?></li>
				<?php endforeach; ?>
			<?php else: ?>
				<li><?php echo $errors; ?></li>
			<?php endif; ?>
		</ul>
		</div>
	<?php endif; ?>
	<?php if (isset($messages)): ?>
		<div class="alert-message blue">
		<p><strong>Success.</strong></p>
		<ul>
			<?php if (is_array($messages)): ?>
				<?php foreach ($messages as $message): ?>
					<li><?php echo $message; ?></li>
				<?php endforeach; ?>
			<?php else: ?>
				<li><?php echo $messages; ?></li>
			<?php endif; ?>
		</ul>
		</div>
	<?php endif; ?>

	<?php echo Form::open(NULL, array('id' => 'invites-form')); ?>
	<input type="hidden" name="emails" value="" /><?php if ($user->invites > 0): ?>
	<article class="container base">
		<header class="cf">
			<div class="property-title">
				<h1>Email</h1>
				<div class="popover add-parameter">
					<p class="button-white add has-icon"><a href="#"><span class="icon"></span>Add more (<?php echo $user->invites-1; ?>)</a></p>
				</div>
			</div>
		</header>
		<section class="property-parameters">
			<div class="parameter">
				<label for="email">
					<p class="field">Email Address</p>
					<input type="text" class="form-email" placeholder="Enter recipient's email address">
					<p class="remove-small actions"><span class="icon"></span><span class="nodisplay">Remove</span></p>
				</label>
			</div>
		</section>
	</article>
	
	<div class="save-toolbar">
		<p class="button-blue"><a href="#" onClick="onSendInvite()"><?php echo __("Send"); ?></a></p>
	</div><?php else: ?>
	<div class="alert-message red">
		<p><strong>Uh oh.</strong></p>
		<ul>
			<li>You have no invites to send!</li>
		</ul>
	</div><?php endif; ?>
	<?php echo Form::close(); ?>

<script type="text/javascript">
	//var line_content = $('div.parameter').last().clone().wrap('<div>').parent().html();
	var line_content = $('.property-parameters').first().html();
	var invites = <?php echo $user->invites; ?>;
	$('.add a').click(function() {
		if ($('div.parameter').length < invites) {
			$('section.property-parameters').append(line_content);
			$('.add a').html('<span class="icon"></span>Add more ('+(invites-$('div.parameter').length)+')');
			$('p.remove-small').click(function() {
				$(this).parent().parent().remove();
				$('.add a').html('<span class="icon"></span>Add more ('+(invites-$('div.parameter').length)+')');
			});
		}
		return false;
	});
	$('p.remove-small').click(function() {
		$(this).parent().parent().remove();
		$('.add a').html('<span class="icon"></span>Add more ('+(invites-$('div.parameter').length)+')');
	});
	function onSendInvite() {
		$('.save-toolbar a').removeClass('visible');
		var emails = [];
		$('input.form-email').each(function(){emails.push($(this).val());});
		$('input[name=emails]').val(emails.join(', '));
		submitForm('.save-toolbar a');
		//$('.save-toolbar a').click('onSendInvite');
		return false;
	}
</script>
