<hgroup class="page-title cf">
	<div class="center">
		<div class="page-h1 col_12">
			<h1><?php echo __("Get started"); ?></h1>
		</div>
	</div>
</hgroup>

<?php if ($public_registration_enabled): ?>
<nav class="page-navigation cf">
	<ul class="center">
		<li <?php if ($active == 'login') echo 'class="active"'; ?>>
			<a href="<?php echo URL::site('login'); ?>"><?php echo __("Log in"); ?></a>
		</li>
		<li <?php if ($active == 'create') echo 'class="active"'; ?>>
			<a href="<?php echo URL::site('login/create_account'); ?>">
				<?php echo __("Create an account"); ?>
			</a>
		</li>
	</ul>
</nav>
<?php endif; ?>

<div id="content" class="settings cf">
	<div class="center">
		<?php echo $sub_content; ?>
	</div>
</div>
