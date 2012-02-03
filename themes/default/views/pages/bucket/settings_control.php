<div id="collaborators" class="controls">
	<div class="row cf">
		<h2><?php echo __("Collaborators"); ?></h2>
		<div class="input">
			<h3><?php echo __("Add people to collaborate on this river"); ?></h3>
			<input type="text" id="add-collaborator" placeholder="<?php echo __("+ Type name..."); ?>">
		</div>
		<div class="data"> </div>
	</div>
	
	<div class="row controls-buttons cf">
		<p class="button-go"><a href="#"><?php echo __("Apply changes"); ?></a></p>
		<p class="other"><a class="close" onclick=""><?php echo __("Cancel"); ?></a></p>
		<div class="item actions">
			<p class="button-delete button-delete-subtle"><a onclick=""><?php echo __("Delete Bucket"); ?></a></p>
			<div class="clear"></div>
			<ul class="dropdown">
				<p><?php echo __("Are you sure you want to delete this Bucket?"); ?></p>
				<li class="confirm"><a onclick=""><?php echo __("Yep."); ?></a></li>
				<li class="cancel"><a onclick=""><?php echo __("No, nevermind."); ?></a></li>
			</ul>
		</div>
	</div>
</div>

<!-- templates -->

<script type="text/template" id="bucket-collaborator-list-item">
	<div class="content">
		<h1><a href="#" class="go"><%= collaborator_name %></a></h1>
	</div>
	<div class="summary">
		<section class="actions">
			<div class="button">
				<p class="button-change">
					<a class="delete" onclick="">
						<span class="icon"></span>
						<span class="nodisplay"><?php echo __("Remove"); ?></span>
					</a>
				</p>
				<div class="clear"></div>
				<div class="dropdown container">
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

<!-- /templates -->

<!-- Javascript for handling the UI -->
<?php echo $settings_js; ?>