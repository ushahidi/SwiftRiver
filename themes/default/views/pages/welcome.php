<!DOCTYPE html> 
<html> 
 
<head>
	<title>Welcome! :: SwiftRiver</title>
	<meta charset="utf-8">
	
	<!-- Main Styles -->
	<link rel="stylesheet" href="http://swiftriver.ushahididev.com/css/styles.css" /> 
	<link rel="stylesheet" href="themes/default/media/css/splashpage.css" /> 
	
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>
	<script type="text/javascript" src="themes/default/media/js/jquery.cycle.all.latest.min.js"></script>
	<script type="text/javascript" src="themes/default/media/js/bootstrap-twipsy.js"></script>
	
	<script type="text/javascript">
	$(function(){ 
		
		/* spotlights carousel */
		$(".hero-statements").cycle({
			 fx: 'fade',
			 speed: 500,
			 timeout: 3300,
			 height: 250,
			 autostop: 1
		});
		
		// hover titles for channels
		$("#channels .tabs li").twipsy({ placement: 'left' });
		
		// remove the current step class and add the appropriate
		$('a.next, a.previous').click(function(){
			$('#sign-up-panels').removeClass("step-1 step-2 step 3").addClass($(this).attr("rel"));
			return false;
		});
		
		//step 1 "reset" link
		$("a.reset").click(function(){
		 $(".the-keywords").val("");
		 return false;
		});
		
		//show "content-ready" div
		$('a.show-content').click(function(){
			$('.ht-not-ready').hide();
			$('.ht-ready').fadeIn();
			return false; 
		});
		
	});
	</script>
	
</head>
<body class="splash">
	
	<header>
		<div class="left_bar"></div>
		<div class="center cf">
			<hgroup>
				<h1 class="logo"><a href="/"><span class="nodisplay">SwiftRiver</span> <span class="beta-tag">Beta</span></a></h1>
			</hgroup>
			<nav>
				<ul>
					<div class="account">
						<li><a href="#"><?php echo __('Who Uses It');?></a></li>
						<li><a href="#"><?php echo __('How It Works');?></a><span>|</span></li>
						<li class="login"><a href="<?php echo url::site().'login'; ?>"><?php echo __('Log in'); ?></a>  <span>|</span></li>
					</div>
				</ul>
			</nav>
		</div>
		<div class="right_bar"></div>
	</header>
	
	<div class="hero-box center cf">
		<div class="hero-statements cf">
		<p class="slide-1">A torrential <span class="blue-glow">river of information</span> flows constantly across the web...</p>
		<p class="slide-2" style="display:none">How do you <span class="blue-glow">filter out what you want</span> and <span class="blue-glow">discard what you don't want?</span></p>
		<p class="slide-3" style="display:none"><span class="blue-glow" style="text-transform:none;">SwiftRiver</span> allows you to <span class="blue-glow">discover</span>, <span class="blue-glow">filter</span> and <span class="blue-glow">present</span> the information you want in meaningful ways.</p>
		</div>
	</div>
	
	<div class="sign-up-box">
		<div id="sign-up-panels" class="center cf step-1">
			<ol class="steps cf">
				<li class="s1"><a href="#"><span>1 </span><?php echo __('What are you searching for?');?></a></li>
				<li class="s2"><a href="#"><span>2 </span><?php echo __('Where to search?');?></a></li>
				<li class="s3"><a href="#"><span>3 </span><?php echo __('View your River!');?></a></li>
			</ol>
			
			<div id="s1-box" class="sb-panel cf">
				<div class="center-align">
					<h3><?php echo __('I\'m looking for information that contains...');?></h3>
					<input class="the-keywords" type="text" />
					<p><small><?php echo __('Single words should be comma-separated. Put phrases in quotes please.');?></small></p>
				</div>
				<p class="steps-panel-nav"> <a class="next" href="#" rel="step-2"><?php echo __('Next, choose where to search');?> &raquo;</a> <span><?php echo __('or');?> <a class="reset" href="#"><?php echo __('reset');?></a>.</span></p>
			</div>
			
			<div id="s2-box" class="sb-panel cf">
				<div class="panel-left cf">
					<h3><?php echo __('Set up channels to start collecting information:');?></h3>
				
					<div id="channels">
						<div class="keywords">
							<h3><?php echo __('Keywords you\'re looking for');?></h3>
						</div>
						<div class="controls cf">
							<ul class="tabs">
									<li title="Twitter" class="button_view twitter"><a title="Twitter" href="#twitter"><span class="switch_on"></span> </a></li>
									<li title="Facebook" class="button_view facebook"><a title="Facebook" href="#facebook"><span class="switch_off"></span> </a></li>
									<li title="RSS" class="button_view rss"><a title="RSS" href="#rss"><span class="switch_off"></span> </a> </li>
									<li title="SMS" class="button_view sms"><a href="#sms"><span class="switch_off"></span> </a></li>
								<li class="more"><a href="#">Find more channels</a></li>
							</ul>				
							<div class="tab_container">
									<article id="twitter" class="tab_content">
									<div class="input">
										<h3>People</h3>
										<input type="text" />
									</div>
									<div class="input">
										<h3>Locations</h3>
										<input type="text" />
									</div>
									<p class="channel-settings"><a href="#">Twitter Settings</a></p>
								</article>
									<article id="facebook" class="tab_content">
										 <!--Content-->
									</article>
									<article id="rss" class="tab_content">
										 <!--Content-->
									</article>
									<article id="sms" class="tab_content">
										 <!--Content-->
									</article>
							</div>
						</div>
						<div class="row cf hide">
							<p class="button_go"><a href="#">Apply changes</a></p>
						</div>
					</div>
				
				</div>
				<div class="panel-right">
					<div class="help-text highlight-box">
						<h4>What is a Channel?</h4>
						<p>A Channel is just a vehicle for transporting content into SwiftRiver. Twitter, Facebook and RSS Feeds are popular channels.  However, any sort of structured data can be a channel. That means that things like text messages (SMS), JSON, GeoRSS can be channels as well.</p>
						
						<h4>Another Frequently Asked Question?</h4>
						<p>Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Cras justo odio, dapibus ac facilisis in, egestas eget quam.</p>
					</div>
				</div>
				<div style="clear:both"></div>
				<p class="steps-panel-nav cf"> <a class="next" href="#" rel="step-3">Next, view your river &raquo;</a> <span>or <a class="previous" href="#" rel="step-1">go back</a>.</span></p>
			</div>
			
			<div id="s3-box" class="sb-panel cf">
				<div class="help-text highlight-box ht-not-ready">
					<h3>One moment please! We’re scouring your channels for the content your requested.</h3>
					<p>This page will update as soon as we’ve found some content for you. <a class="show-content" href="#">Refresh manually</a><br /><br /></p>
					<p><strong>Taking too long? Save this link and come back later:</strong>  <a href="#">http://swiftly.org/r/I21es32</a></p>
					<p><strong>Want to try again?</strong> <a href="#" class="previous">Start from the beginning</a>.</p>
					<p><strong>Already have any account?</strong> <a href="#" class="previous">Login here</a>.</p>
				</div>
				<div class="help-text highlight-box ht-ready cf" style="display:none;">
					<h3>Your River of Information is now ready!</h3>
					<p class="button_go"><a href="#">View Your River &raquo;</a></p>
				</div>
				
				<h3 class="push-up"><span>In the mean time...</h3>
					
				<div class="panel-left">
					<h3>Create An Account</h3>
					<div class="login">
						<p>
							<strong>Username</strong>
							<input type="text" />
						</p>
						<p>
							<strong>Password</strong>
							<input type="password" />
						</p>
						<p class="button_go cf"><a href="#">Create My Account!</a></p>
						
						<div style="clear:both"></div>
						<div class="help-text highlight-box">
							<h3>How much does this cost?</h3>
							<p>SwiftRiver is free while we're in beta. We'll roll out paid features soon.</p>
						</div>
						<p class="or">Or</p>
					</div>
				</div>
				<div class="panel-right help-text">
					<h3>Learn More About SwiftRiver</h3>
					<p>SwiftRiver is made up of Rivers, Buckets and Droplets... 
					
					<h4>What is a Droplet?</h4>
					<p>The basic unit of content inside of SwiftRiver ie: Tweet, Facebook Update, Blogpost, SMS Message... etc.

					<h4>What is a Bucket?</h4>
					<p>A group of hand-picked droplets that are meaningful to you. </p>

					<h4>What is a River?</h4>
					<p>The torrent of droplets that come from your predefined channels.</p>
					<p><strong><a href="#">Learn More &raquo;</a></p>
					
				</div>
				
			</div>
		</div>
	</div>
	
	<footer>

	</footer>
</body>
</html>