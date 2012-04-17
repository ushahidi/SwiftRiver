<header class="toolbar">
	<div class="center">
		<div class="masthead">
			<h2>Curate real-time information with the power of the crowd.</h2>
			<div id="message-container">
				<div id="video-container">
					<iframe src="http://player.vimeo.com/video/38711746" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>
					<p class="button-blue button-darkblue button-video close"><a href="#">Close</a></p>
				</div>
				<p class="button-blue button-darkblue button-video"><a href="#"><span class="icon"></span>See how it works</a></p>
				<img src="/markup/images/content/home-masthead-graphic.png" />
			</div>
		</div>
	</div>
</header>

<?php if ($public_registration_enabled AND $anonymous): ?>
<?php echo Form::open(URL::site('login')); ?>
<section class="sign-up cf">
	<div class="center">
		<div class="col_3">
			<input type="email" name="new_email" placeholder="Email address" />
		</div>
		<div class="col_3">
			<p class="button-blue button-darkblue"><a href="#" onclick="submitForm(this); return false;">Get started</a></p>
		</div>
	</div>
</section>
<?php echo Form::close(); ?>
<?php endif; ?>

<article class="basics">
	<div class="center">
		<div class="message river">
			<p><strong>When you need to follow a torrential river of information,</strong> but can’t afford to overlook the most important stuff.</p>
		</div>
		<div class="message filter">
			<p><strong>SwiftRiver is a powerful filter</strong> that allows you to define exactly what gets your attention.</p>
		</div>
		<div class="message bucket">
			<p><strong>Store your hand-picked information in buckets,</strong> while collaborating, sharing and viewing everything with fresh insight.</p>
		</div>
	</div>
</article>

<article class="features cf">
	<div class="center">
		<div class="col_4">
			<h2 class="collaboration">Collaboration</h2>
			<ul>
				<li>
					<a href="/markup/images/content/home-screen-collab1.png" class="modal-trigger img"><img src="/markup/images/content/home-screen-collab1.png" /></a>
					<span class="description">Simultaneously manage a river or bucket with your peers.</span>
				<li>
					<a href="/markup/images/content/home-screen-collab2.png" class="modal-trigger img"><img src="/markup/images/content/home-screen-collab2.png" /></a>
					<span class="description">Get instant feedback from the crowd about what you find.</span>
				</li>
			</ul>
		</div>
		<div class="col_4">
			<h2 class="views">Insightful views</h2>
			<ul>
				<li>
					<a href="/markup/images/content/home-views1.png" class="modal-trigger"><img src="/markup/images/content/home-screen-views1.png" /></a>
					<span class="description">See the your information in a list, photo gallery, timeline or more.</span>
				<li>
					<a href="/markup/images/content/home-screen-views2.png" class="modal-trigger"><img src="/markup/images/content/home-screen-views2.png" /></a>
					<span class="description">Apply new views to your information, or build your own.</span>
				</li>
			</ul>
		</div>
		<div class="col_4">
			<h2 class="configuration">Configuration</h2>
			<ul>
				<li>
					<a href="/markup/images/content/home-screen-config1.png" class="modal-trigger"><img src="/markup/images/content/home-screen-config1.png" /></a>
					<span class="description">Control who can see your river or bucket.</span>
				<li>
					<a href="/markup/images/content/home-screen-config1.png" class="modal-trigger"><img src="/markup/images/content/home-screen-config2.png" /></a>
					<span class="description">Run SwiftRiver’s open source software on your own server.</span>
				</li>
			</ul>
		</div>
	</div>
</article>

<?php if ($public_registration_enabled AND $anonymous): ?>
<?php echo Form::open(URL::site('login')); ?>
<section class="sign-up white cf">
	<div class="center">
		<h1>Create an account and get started in seconds.</h1>
		<div class="col_3">
			<input type="email" name="new_email" placeholder="Email address" />
		</div>
		<div class="col_3">
			<p class="button-blue button-darkblue"><a href="#" onclick="submitForm(this); return false;">Get started</a></p>
		</div>
	</div>
</section>
<?php echo Form::close(); ?>
<?php endif; ?>