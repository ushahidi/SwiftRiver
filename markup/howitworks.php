<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
	<title>SwiftRiver ~ Curate real-time information with the power of the crowd</title>
	<link rel="shortcut icon" href="#">
	<link type="text/css" href="/markup/_css/styles.css" rel="stylesheet" />
	<link type="text/css" href="/markup/_css/home.css" rel="stylesheet" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="/markup/_js/jquery.outside.js"></script>
	<script type="text/javascript" src="/markup/_js/global.js"></script>
	<script>
	$(document).ready(function() {
		$('.button-video a').live('click', function(e) {
			$('#message-container').children('p,img').fadeTo('fast', 0.35);
			$('#video-container').fadeIn('fast');
			e.preventDefault();
		});
		$('#video-container .close a').live('click', function(e) {
			$('#message-container').children('p,img').fadeTo('fast', 1);
			$('#video-container').fadeOut('fast');
			e.preventDefault();
		});
		$('article.features ul li a').toggle(
			function(e) {
				var thumbImg = $(this).children('img').attr('src');
				var zoomImg = $(this).attr('href');
				$(this).addClass('zoom');
				$('body').addClass('noscroll');
				$(this).attr('href', thumbImg);
				$(this).children('img').attr('src', zoomImg);
				e.preventDefault();
			},
			function(e) {
				var thumbImg = $(this).attr('href');
				var zoomImg = $(this).children('img').attr('src');
				$(this).removeClass('zoom');
				$('body').removeClass('noscroll');
				$(this).attr('href', zoomImg);
				$(this).children('img').attr('src', thumbImg);
				e.preventDefault();
		});
	});
	</script>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
</head>

<body>
	<header class="toolbar">
		<div class="center">
			<div class="col_4">
				<h1 class="logo"><a href="/markup/"><span class="nodisplay">SwiftRiver</span></a></h1>
				<ul class="global-menu">
					<li class="home"><a href="/"><span class="icon"></span></a></li>
					<li class="search"><a href="/markup/modal-search.php" class="modal-trigger"><span class="icon"></span></a></li>
				</ul>
			</div>

			<div class="masthead">
				<h2>Curate real-time information with the power of the crowd.</h2>
				<div id="message-container">
					<img src="/markup/images/content/home-masthead-graphic.png" />
				</div>
			</div>
		</div>
	</header>

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
						<a href="/markup/images/content/home-screen-collab1FULL.png"><img src="/markup/images/content/home-screen-collab1.png" /></a>
						<span class="description">Simultaneously manage a river or bucket with your peers.</span>
					</li>
				</ul>
			</div>
			<div class="col_4">
				<h2 class="views">Insightful views</h2>
				<ul>
					<li>
						<a href="/markup/images/content/home-screen-views1FULL.png"><img src="/markup/images/content/home-screen-views1.png" /></a>
						<span class="description">See the your information in a list, photo gallery, timeline or more.</span>
					</li>
				</ul>
			</div>
			<div class="col_4">
				<h2 class="configuration">Configuration</h2>
				<ul>
					<li>
						<a href="/markup/images/content/home-screen-config1FULL.png"><img src="/markup/images/content/home-screen-config1.png" /></a>
						<span class="description">Control who can see your river or bucket.</span>
					</li>
				</ul>
			</div>
		</div>
	</article>

	<footer>
		<div class="center">
			<h1 class="logo"><a href="/markup/"><span class="nodisplay">SwiftRiver</span></a></h1>
			<h2>Made by Ushahidi in <strong>Nairobi, Kenya</strong></h2>
		</div>
	</footer>

</body>
</html>