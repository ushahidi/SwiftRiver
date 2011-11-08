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
				<li><a href="/river/new.html"><em><?php echo __('Create New');?></em></a></li>
			</ul>
		</li>
		<li class="buckets has_dropdown">
			<a><span class="arrow"></span><?php echo __('Buckets');?></a>
			<ul class="dropdown">
				<li><a href="/bucket/">Bucket 1</a></li>
				<li><a href="/bucket/">Bucket 2</a></li>
				<li class="create_new"><a><em><?php echo __('Create New');?></em></a></li>
			</ul>
		</li>
		<div class="account">
			<li class="create"><a href="/login.html"><?php echo __('Create an Account');?></a></li>
			<li class="login has_dropdown">
				<a><span class="arrow"></span><?php echo __('Log In');?></a>
				<ul class="dropdown">
					<li><strong><?php echo __('Email');?></strong><input type="text" value=""></li>
					<li><strong><?php echo __('Password');?></strong><input type="password" value=""></li>
					<li><div class="buttons"><button class="save"><?php echo __('Log In');?></button></div></li>
				</ul>
			</li>
		</div>
	</ul>
</nav>