<article class="dashboard">
	<div class="center page-title cf">
		<hgroup class="edit user">
			<img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" />
			<h1><span class="edit_trigger" title="dashboard" id="edit_<?php echo $user->id; ?>" onclick=""><?php echo $user->name; ?></span></h1>
		</hgroup>
	</div>
	
	<div class="center canvas cf">
		<section class="panel">		
			<div class="panel_body">
				<div class="controls">
					<div class="row">
						<ul class="views cf">
							<li class="button_view"><a href="<?php echo URL::site().'dashboard/';?>"><?php echo __('Activity'); ?></a></li>
							<li class="button_view"><a href="<?php echo URL::site().'dashboard/rivers';?>"><?php echo __('Rivers'); ?></a></li>
							<li class="button_view"><a href="<?php echo URL::site().'dashboard/buckets';?>"><?php echo __('Buckets'); ?></a></li>
							<li class="button_view"><a href="<?php echo URL::site().'dashboard/teams';?>"><?php echo __('Teams'); ?></a></li>
						</ul>
					</div>
				</div>
			</div>
		</section>
	</div>	
</article>