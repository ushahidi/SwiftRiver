<div class="container select data">
	<div class="controls edit-advanced">
		<div class="row cf">
			<p class="button-go edit-single"><a href="#">Edit team</a></p>
			<p class="button_view edit_multiple"><a href="<?php echo URL::site()?>dashboard/edit_multiple_teams">Edit multiple</a></p>
			<p class="button_view"><a href="<?php echo URL::site()?>dashboard/filter_rivers">Filter</a></p>
			<p class="button-go create-new"><a href="#">Create new</a></p>
		</div>
		<div class="detail cf"></div>
	</div>
	
	<article class="item cf">
		<div class="content">
			<div class="checkbox"><input type="checkbox" /></div>
			<h1><a href="/team/" class="title">Team 1</a></h1>
		</div>
		<div class="summary">
			<section class="actions">
				<div class="button">
					<p class="button-change"><a class="delete" onclick=""><span class="icon"></span><span class="nodisplay">Leave team</span></a></p>
					<div class="clear"></div>
					<div class="dropdown container">
						<p>Are you sure you want to leave Team 1?</p>
						<ul>
							<li class="confirm"><a onclick="">Yep.</a></li>
							<li class="cancel"><a onclick="">No, nevermind.</a></li>
						</ul>
					</div>
				</div>
			</section>
		</div>
	</article>
	
	<article class="item cf">
		<div class="content">
			<div class="checkbox"><input type="checkbox" /></div>
			<h1><a href="/team/" class="title">Team 2</a></h1>
		</div>
		<div class="summary">
			<section class="actions">
				<div class="button">
					<p class="button-change"><a class="delete" onclick=""><span class="icon"></span><span class="nodisplay">Leave team</span></a></p>
					<div class="clear"></div>
					<div class="dropdown container">
						<p>Are you sure you want to leave Team 2?</p>
						<ul>
							<li class="confirm"><a onclick="">Yep.</a></li>
							<li class="cancel"><a onclick="">No, nevermind.</a></li>
						</ul>
					</div>
				</div>
			</section>
		</div>
	</article>
</div>