<div class="arrow top"><span></span></div>
<div class="canyon cf">
	<aside>
		<div class="item actions cf">
			<p class="button_delete"><a>Delete droplet</a></p>
			<div class="clear"></div>
			<ul id="delete_droplet" class="dropdown left">
				<p>Are you sure you want to delete this droplet?</p>
				<li class="confirm"><a href="/droplet/delete/<?php echo $droplet->id ?>" onclick="">Yep.</a></li>
				<li class="cancel"><a onclick="">No, nevermind.</a></li>
			</ul>
		</div>
		
		<div class="item cf">
			<h2>Tags</h2>
			<ul class="tags cf">
				<?php foreach ($droplet->tags->find_all() as $tag): ?>
				    <li><a href="#"><?php echo $tag->tag ?></a></li>
				<?php endforeach; ?>
			</ul>
			<p class="button_change"><a>Add tag</a></p>
		</div>
	
		<div class="item cf">
			<h2>Location</h2>
			<?php foreach ($droplet->places->find_all() as $place): ?>
			    <p class="edit"><span class="edit_trigger" title="place" id="edit_<?php echo $place->id; ?>" onclick=""><?php echo $place->place_name ?></span></p>
			<?php endforeach; ?>
			<p class="button_change"><a>Add location</a></p>
		</div>
	
		<div class="item cf">
			<h2>Links</h2>
			<?php foreach ($droplet->links->find_all() as $link): ?>
			    <p class="edit"><span class="edit_trigger" title="link" id="edit_<?php echo $link->id; ?>" onclick=""><?php echo $link->link ?></span></p>
			<?php endforeach; ?>
			<p class="button_change"><a>Add links</a></p>
		</div>
	
		<div class="item cf">
			<p class="button_change"><a>Add attachment</a></p>
		</div>																					
	</aside>
	
	<div class="right_column">
		<article class="fullstory">
			<hgroup>
				<h2>Full story</h2>
				<h1 class="edit"><span class="edit_trigger" title="droplet" id="edit_<?php echo $droplet->id; ?>" onclick=""><?php echo $droplet->droplet_title ?></span></h1>
			</hgroup>
			<div class="edit">
			<span class="edit_trigger" title="droplet" id="edit_<?php echo $droplet->id; ?>" onclick="">
			<?php echo $droplet->droplet_content ?>
			</span>
			</div>
		</article>
		
		<section class="discussion">
			<hgroup>
				<h2>Related discussion</h2>
			</hgroup>
			<article class="droplet cf">
				<div class="summary">
					<section class="source sms">
						<a href="/user"><img src="/images/content/avatar3.gif" /></a>
						<div class="actions">
							<span class="type"></span>
							<p class="button_change score"><a onclick=""><span>45</span></a><p>
							<div class="clear"></div>
							<ul class="dropdown left">
								<li class="confirm"><a onclick="">This is useful</a></li>
								<li class="not_useful"><a onclick="">This is not useful</a></li>
							</ul>
						</div>
					</section>
					<section class="content">
						<div class="title">
							<p class="date">July 4, 2011</p>
							<h1>Adam Tinworth</h1>
						</div>
						<div class="body">
							<p>OK, the Ushahidi section of this afternoon's #likeminds post should now be more link rich and comprehensible: <a href="#">t.co/D2lk9lRg</a></p>
							<p class="button_go"><a href="/droplet">View more</a></p>
						</div>
					</section>
					<section class="actions">
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
			</article>
			
			<article class="droplet add_reply cf">
				<div class="summary">
					<section class="source">
						<div class="avatar">
							<img src="/images/content/avatar1.gif" />
							<div class="swiftriver"><span></span></div>
						</div>
					</section>
					<section class="content">
						<textarea rows="10" cols="60"></textarea>
						<p class="button_go"><a href="#">Add reply</a></p>
					</section>
				</div>
			</article>
		</section>
	</div>
</div>
<div class="arrow bottom"><a class="close" onclick="">Hide detail</a></div>