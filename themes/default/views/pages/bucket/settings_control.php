<<<<<<< HEAD
<div class="panel-body">
	<div id="settings" class="controls">
		<div class="row cf">
			<!-- collaborators -->
			<?php echo $collaborators_control; ?>
			<!-- /collaborators -->
		</div>
	</div>
</div>
=======
<div id="collaborators" class="controls" data-settings-bucket-id="<?php echo $bucket->id; ?>">
	<div class="row cf">
		<h2><?php echo __("Collaborators"); ?></h2>
		<div class="input">
			<h3><?php echo __("Add people to collaborate on this river"); ?></h3>
			<input type="text" id="add-collaborator" placeholder="<?php echo __("+ Type name..."); ?>">
		</div>
		<div class="clear"></div>
		<div class="dropdown" id="live-search-dialog">
			<div class="container">
				<ul></ul>
			</div>
		</div>
		<div class="data"> </div>
	</div>
	
	<div class="row controls-buttons cf">
		<p class="button-go"><a href="#"><?php echo __("Apply changes"); ?></a></p>
		<p class="other"><a class="close" onclick=""><?php echo __("Cancel"); ?></a></p>
		<div class="item actions">
			<p class="button-delete button-delete-subtle"><a onclick=""><?php echo __("Delete Bucket"); ?></a></p>
			<div class="clear"></div>
			<ul class="dropdown" id="confirm-bucket-delete">
				<p><?php echo __("Are you sure you want to delete this Bucket?"); ?></p>
				<li class="confirm"><a><?php echo __("Yep."); ?></a></li>
				<li class="cancel"><a><?php echo __("No, nevermind."); ?></a></li>
			</ul>
		</div>
	</div>
</div>

<!-- templates -->

<script type="text/template" id="collaborator-template">
	<div class="content">
		<h1><a href="#" class="go"><%= collaborator_name %></a></h1>
	</div>
	<div class="summary">
		<section class="actions">
			<div class="button">
				<p class="button-change">
					<a class="delete">
						<span class="icon"></span>
						<span class="nodisplay"><?php echo __("Remove"); ?></span>
					</a>
				</p>
				<div class="clear"></div>
				<div class="dropdown container collaboration-actions">
					<p><?php echo __("Are you sure you want to stop collaborating with this person?"); ?></p>
					<ul>
						<li class="confirm"><a onclick=""><?php echo __("Yep."); ?></a></li>
						<li class="cancel"><a onclick=""><?php echo __("No, nevermind."); ?></a></li>
					</ul>
				</div>
			</div>
		</section>
	</div>
</script>

<script type="text/template" id="live-search-template">
	<a><span class="input"></span><%= collaborator_name %></a>
</script>
<!-- /templates -->

<!-- Javascript for handling the UI -->
<?php echo $settings_js; ?>
>>>>>>> dd5c69cf69f23cee9b53233da5f9afba392bcb53
