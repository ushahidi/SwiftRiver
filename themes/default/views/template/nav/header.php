<nav>
	<ul>
		<li class="rivers has_dropdown">
			<a href="<?php echo URL::site().'dashboard/rivers' ?>" class="arrow"><span class="icon"></span><?php echo __('Rivers');?></a>
			<ul class="dropdown">
				<?php foreach ($rivers as $river): ?>
					<li>
						<?php if ($river->account->user->id != $user->id): ?>
							<a href="<?php echo URL::site().'river/index/'.$river->id; ?>"><?php echo $river->account->account_path ?>/<?php echo $river->river_name; ?></a>
						<?php else: ?>
							<a href="<?php echo URL::site().'river/index/'.$river->id; ?>"><?php echo $river->river_name; ?></a>
						<?php endif; ?>
						
					</li>
				<?php endforeach; ?>
				<li><a href="<?php echo URL::site().'river/new'; ?>" class="plus"><em><?php echo __('Create new');?></em></a></li>
			</ul>
		</li>
		<li class="buckets has_dropdown">
			<a href="<?php echo URL::site().'dashboard/buckets' ?>" class="arrow"><span class="icon"></span><?php echo __('Buckets');?></a>
			<ul class="dropdown" id="dropdown_buckets">
				<?php foreach ($buckets as $bucket): ?>
					<li>
						<?php if ($bucket->account->user->id != $user->id): ?>
							<a href="<?php echo URL::site().'bucket/index/'.$bucket->id; ?>"><?php echo $bucket->account->account_path ?>/<?php echo $bucket->bucket_name; ?></a>
						<?php else: ?>
							<a href="<?php echo URL::site().'bucket/index/'.$bucket->id; ?>"><?php echo $bucket->bucket_name; ?></a>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
				<li class="create-new"><a href="<?php echo URL::site().'bucket/new'?>" class="plus"><em><?php echo __('Create new');?></em></a></li>
			</ul>
		</li>
		<div class="account">
			<?php if ($user): ?>
				<?php if ($num_notifications): ?>
					<li class="notifications">
						<a href="<?php echo URL::site().'dashboard'; ?>">
							<span class="badge"><?php echo $num_notifications; ?></span>
						</a>
					</li>
				<?php endif ?>
				<li class="user has_dropdown">
					<a href="<?php echo URL::site().'dashboard/settings' ?>" class="arrow">
						<span class="badge"><img src="<?php echo Swiftriver_Users::gravatar($user->email, 80); ?>" /></span>
						<span class="icon"></span><span class="label"><?php echo $user->name; ?></span>
					</a>
					<ul class="dropdown">
						<li><a href="<?php echo URL::site().'dashboard'; ?>"><?php echo __('Your account');?></a></li>
						<?php if ($admin): ?>
							<li><a href="<?php echo URL::site().'settings'; ?>"><?php echo __('Site settings');?></a></li>
						<?php endif; ?>						
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
							<li><div class="buttons" onclick="submitForm(this)"><button class="save"><?php echo __('Log In');?></button></div></li>
						</ul>
					</li>
				<?php echo Form::close(); ?>			
			<?php endif; ?>
		</div>	
	</ul>
</nav>