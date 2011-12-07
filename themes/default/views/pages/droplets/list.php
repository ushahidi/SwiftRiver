<?php foreach ($droplets as $droplet): ?>
	<article class="droplet cf">
		<div class="summary">
			<section class="source <?php echo $droplet['channel'] ?>">
				<a><img src="<?php echo $droplet['identity_avatar'] ?>" /></a>
				<div class="actions">
					<span class="type"></span>
					<p class="button_change score"><a onclick=""><span>0</span></a><p>
					<div class="clear"></div>
					<ul class="dropdown left">
						<li class="confirm"><a onclick="">This is useful</a></li>
						<li class="not_useful"><a onclick="">This is not useful</a></li>
					</ul>
				</div>
			</section>
			<section class="content">
				<div class="title">
					<p class="date"><?php echo $droplet['droplet_date_pub'] ?></p>
					<h1><?php echo $droplet['identity_name'] ?></h1>
				</div>
				<div class="body">
					<p><?php echo $droplet['droplet_title'] ?></p>
				</div>
			</section>
			<section class="actions">
				<p class="button_view"><a href="/droplet/detail/<?php echo $droplet['id'];?>" class="detail_view"><span></span><strong>detail</strong></a></p>
				<div class="button">
					<p class="button_change bucket"><a><span></span><strong>buckets</strong></a></p>
					<div class="clear"></div>
					<ul class="dropdown">
						<li class="bucket"><a onclick=""><span class="select"></span>Bucket 1</a></li>
						<li class="bucket"><a onclick=""><span class="select"></span>Bucket 2</a></li>
						<li class="create_new"><a onclick=""><span class="create_trigger"><em>Create new</em></span></a></li>
					</ul>
				</div>
			</section>
		</div>
		<section class="detail cf"></section>
	</article>
<?php endforeach; ?>