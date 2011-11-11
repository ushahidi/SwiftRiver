<script type="text/javascript">
$(document).ready(function() {
	$(".tab_content").hide(); //Hide all content
	$("ul.tabs li:first").addClass("active").show(); //Activate first tab
	$(".tab_content:first").show(); //Show first tab content

	$("ul.tabs li").click(function() {

		$("ul.tabs li").removeClass("active"); //Remove any "active" class
		$(this).addClass("active"); //Add "active" class to selected tab
		$(".tab_content").hide(); //Hide all tab content

		var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
		$(activeTab).fadeIn(); //Fade in the active ID content
		return false;
	});
	
	$('ul.tabs li.button_view a span').click(function() {
		$(this).toggleClass('switch_on').toggleClass('switch_off');
	});
});
</script>

<div id="channels">
	<div class="controls keywords">
		<h3>Keywords you're looking for</h3>
		<input type="text" />
	</div>
	<div class="controls tab_controls cf">
		<ul class="tabs">
			<?php
			foreach ($channels as $key => $channel)
			{
				?>
				<li class="button_view <?php echo $key; ?>"><a href="#<?php echo $key; ?>"><span class="switch_on"></span><span class="label"><?php echo $channel['name']; ?></span></a></li>
				<?php
			}
			?>
		    <li class="button_view facebook"><a href="#facebook"><span class="switch_off"></span><span class="label">Facebook</span></a></li>
			<li class="button_view rss"><a href="#rss"><span class="switch_off"></span><span class="label">RSS</span></a></li>
		    <li class="button_view sms"><a href="#sms"><span class="switch_off"></span><span class="label">SMS</span></a></li>
			<li class="more"><a href="#">More channels</a></li>
		</ul>				
		<div class="tab_container">
			<?php
			foreach ($channels as $key => $channel)
			{
				?>
				<article id="<?php echo $key; ?>" class="tab_content">
					<ul class="channel_options cf">
						<?php
						foreach ($channel['options'] as $option_key => $option_value)
						{
							?>
							<li><a href="javascript:channelOption('<?php echo $key; ?>', '<?php echo $option_key; ?>', '<?php echo $option_value; ?>')"><span></span><?php echo $option_value; ?></a></li>
							<?php	
						}
						?>
					</ul>
				</article>				
				<?php
			}
			?>
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
	<div class="row controls_buttons cf">
		<p class="button_go"><a href="#" onclick="submitForm(this)">Apply changes</a></p>
		<p class="other"><a class="close" onclick="">Cancel</a></p>
	</div>
</div>