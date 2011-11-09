<nav>
	<ul>
		<li class="rivers has_dropdown">
			<a><span class="arrow"></span><?php echo __('Rivers');?></a>
			<ul class="dropdown">
				<?php
				foreach ($rivers as $river)
				{
					?><li><a href="/"><?php echo $river->river_name; ?></a></li><?php
				}
				?>
				<li><a href="<?php echo URL::site().'river/new'; ?>"><em><?php echo __('Create New');?></em></a></li>
			</ul>
		</li>
		<li class="buckets has_dropdown">
			<a><span class="arrow"></span><?php echo __('Buckets');?></a>
			<ul class="dropdown">
				<li><a href="/bucket/">Bucket 1</a></li>
				<li><a href="/bucket/">Bucket 2</a></li>
				<li><a><em><?php echo __('Create new');?></em></a></li>
			</ul>
		</li>
		<div class="account">
			<?php if ($user): ?>
				<li class="user has_dropdown">
					<a><img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" /><span class="arrow"></span><span class="label"><?php echo $user->name; ?></span></a>
					<ul class="dropdown">
						<li><a href="<?php echo URL::site().'profile'; ?>"><?php echo __('Your account');?></a></li>
						<li><a href="<?php echo URL::site().'settings'; ?>"><?php echo __('Your settings');?></a></li>
						<li><a href="<?php echo URL::site().'login/done'; ?>"><em><?php echo __('Log out');?></em></a></li>
					</ul>
				</li>				
			<?php else: ?>			
				<li class="create"><a href="<?php URL::site().'welcome'; ?>"><?php echo __('Create an Account');?></a></li>
				<?php echo Form::open(); ?>
					<li class="login has_dropdown">
						<a><span class="arrow"></span><?php echo __('Log In');?></a>
						<ul class="dropdown">
							<li><strong><?php echo __('Email');?></strong><?php echo Form::input("username", ""); ?></li>
							<li><strong><?php echo __('Password');?></strong><?php echo Form::password("password", ""); ?></li>
							<li><div class="buttons btn_click"><button class="save"><?php echo __('Log In');?></button></div></li>
						</ul>
					</li>
				<?php echo Form::close(); ?>			
			<?php endif ?>
		</div>	
	</ul>
</nav>