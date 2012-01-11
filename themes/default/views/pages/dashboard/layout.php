<article class="<?php echo $template_type; ?>">
	<div class="center page_title cf">
		<hgroup class="edit user">
			<img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" />
			<h1><span class="edit_trigger" title="dashboard" id="edit_<?php echo $user->id; ?>" onclick=""><?php echo $user->name; ?></span></h1>
		</hgroup>
	</div>
	
	<div class="center canvas cf">
		<section class="panel">		
			<nav class="cf">
				<ul class="views">
					<li <?php if ($active == 'main' OR ! $active) echo 'class="active"'; ?>><a href="<?php echo URL::site().'dashboard/';?>"><?php echo __('Activity'); ?></a></li>
					<li <?php if ($active == 'rivers') echo 'class="active"'; ?>><a href="<?php echo URL::site().'dashboard/rivers';?>"><?php echo __('Rivers'); ?></a></li>
					<li <?php if ($active == 'buckets') echo 'class="active"'; ?>><a href="<?php echo URL::site().'dashboard/buckets';?>"><?php echo __('Buckets'); ?></a></li>
					<li <?php if ($active == 'teams') echo 'class="active"'; ?>><a href="<?php echo URL::site().'dashboard/teams';?>"><?php echo __('Teams'); ?></a></li>
					<li class="view_panel more <?php if ($active == 'views') echo 'active'; ?>"><a href="<?php echo URL::site().'dashboard/views';?>" class="arrow">More<span class="icon"></span></a></li>
				</ul>
				<ul class="actions">
					<li class="view_panel"><a href="<?php echo URL::site().'dashboard/settings';?>" class="settings"><span class="icon"></span><span class="label"><?php echo __('Account settings'); ?></span></a></li>
				</ul>
			</nav>
		</section>
		
		<?php echo $sub_content; ?>
		
	</div>	
</article>