<article id="droplet_full" class="<?php echo $template_type; ?> droplet dashboard cf">
	<div class="center page-title cf">
		<hgroup class="edit user">
			<img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" />
			<h1>
				<span class="edit-trigger" title="dashboard" id="edit_1" onclick="">
					<?php echo $user->name ?>
				</span>
			</h1>
		</hgroup>
		<section class="actions">
			<p class="button_change follow_user"><a class="subscribe"><span></span><strong>follow</strong></a></p>
		</section>
	</div>
	
	<div class="center canvas cf">
		<section class="panel">		
			<nav class="cf">
				<ul class="views">
					<li <?php if ($active == 'rivers') echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().'user/'.$account_path;?>"><?php echo __('Rivers'); ?></a>
					</li>
					<li <?php if ($active == 'buckets') echo 'class="active"'; ?>>
						<a href="<?php echo URL::site().'user/'.$account_path.'/buckets';?>"><?php echo __('Buckets'); ?></a>
					</li>
				</ul>
			</nav>
			<div class="drawer"></div>
		</section>
		
		<?php echo $sub_content; ?>
		
	</div>	
</article>