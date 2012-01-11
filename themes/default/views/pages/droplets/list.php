<div id="river_droplets">
<?php foreach ($droplets as $droplet): ?>
	<article class="item">
		<div class="summary cf">
			<section class="source <?php echo $droplet['channel'] ?>">
				<a><img src="<?php echo $droplet['identity_avatar'] ?>" /></a>
				<div class="actions">
					<span class="type"></span>
					<p class="button_change"><a class="score" onclick=""><span class="icon">0</span></a><p>
					<div class="clear"></div>
					<ul class="dropdown left">
						<li class="confirm"><a onclick="">This is useful</a></li>
						<li class="not_useful"><a onclick="">This is not useful</a></li>
					</ul>
				</div>
			</section>
			<div class="content">
				<hgroup>
					<p class="date"><?php echo $droplet['droplet_date_pub'] ?></p>
					<h1><?php echo $droplet['identity_name'] ?></h1>
				</hgroup>
				<div class="body">
					<p><?php echo $droplet['droplet_title'] ?></p>
				</div>
				<div class="button">
					<p class="button_change checkbox_options" onclick=""><a><span class="icon"></span></a></p>
					<div class="clear"></div>
					<div class="dropdown">
						<div class="container">
							<h3>Add to Bucket</h3>
							<ul>
							<?php foreach ($buckets as $bucket) :
								$bucket_action = Swiftriver_Droplets::bucket_action($bucket->id, $droplet['id']);?>
								<li class="checkbox"><a onclick="addBucketDroplet(this, <?php echo $bucket->id.','.$droplet['id']; ?>)" title="<?php echo $bucket_action; ?>" class="<?php echo ($bucket_action == 'remove') ? 'selected' : ''; ?>" ><span class="input"></span><?php echo $bucket->bucket_name; ?></a></li>
							<?php endforeach; ?>
							</ul>
							<p class="create_new"><a onclick="createBucket(this, 'droplet', <?php echo $droplet['id']; ?>)" class="plus"><span class="create_trigger"><em>Create new</em></span></a></li>
						</div>
					</div>
				</div>
			</section>
			<section class="actions">
				<p class="button_view"><a href="/droplet/detail/<?php echo $droplet['id'];?>" class="detail_view"><span class="icon"></span></a></p>
			</section>
		</div>
	</article>
<?php endforeach; ?>
</div>

<?php echo(Html::script("themes/default/media/js/jquery.infinitescroll.min.js")); ?>
<script type="text/javascript">
$(document).ready(function() {
    $('article #river_droplets').infinitescroll({
    		navSelector  	: "article .page_buttons",
    		nextSelector 	: "article .page_buttons .button_view a",
    		itemSelector 	: "article #river_droplets",
    		debug		 	: true,
    		dataType	 	: 'html'
        })
});
</script>


<div class="page_buttons">
<p class="button_view"><a href="<?php echo $view_more_url ?>">View more</a></p>
</div>
