<article class="<?php echo $template_type; ?>">
	<div class="center page-title cf">
		<hgroup class="edit user cf">
			<img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" />
			<h1>
				<span class="edit-trigger" title="dashboard" id="edit_1" onclick="">
					<?php echo $user->name ?>
				</span>
			</h1>
		</hgroup>
		<section class="actions">
			<div class="button">
				<p class="button-change follow-user"><a class="subscribe"><span class="icon"></span><span class="label">Follow <?php echo $user->name ?></span></a></p>
				<div class="clear"></div>
				<div class="dropdown container">
					<p>Are you sure you want to follow <?php echo $user->name ?>?</p>
					<ul>
						<li class="confirm"><a onclick=""><?php echo __('Yep.'); ?></a></li>
						<li class="cancel"><a onclick=""><?php echo __('No, nevermind.'); ?></a></li>
					</ul>
				</div>
			</div>
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