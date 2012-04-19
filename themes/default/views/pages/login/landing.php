<hgroup class="page-title cf">
	<div class="center">
		<div class="page-h1 col_12">
			<h1>Redirecting...</h1>
		</div>
	</div>
</hgroup>

<div id="content" class="settings cf">
	<div class="center">
		<div class="col_9">
			
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
			
		</div>
	</div>
</div>