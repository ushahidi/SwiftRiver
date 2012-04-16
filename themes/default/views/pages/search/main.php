<?php if ( ! empty($search_term)): ?>
<hgroup class="page-title cf">
	<div class="center">
		<div class="page-h1 col_9">
			<h1><?php echo __("Search results "); ?> <em><?php echo $search_term; ?></em></h1>
		</div>
	</div>
</hgroup>

<nav class="page-navigation cf">
	<div class="center">
		<div id="page-views" class="river touchcarousel col_12">
			<ul class="touchcarousel-container">
				<li class="touchcarousel-item active"><a href="#"><?php echo __("Drops"); ?></a></li>
				<li class="touchcarousel-item"><a href="#"><?php echo __("Rivers"); ?></a></li>
				<li class="touchcarousel-item"><a href="#"><?php echo __("Buckets"); ?></a></li>
				<li class="touchcarousel-item"><a href="#"><?php echo __("Users"); ?></a></li>
			</ul>
		</div>
	</div>
</nav>

<?php echo $droplets_view; ?>
<?php else: ?>

<article class="modal">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1><?php echo __("Search"); ?></h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon"></span>
					<?php echo __("Close"); ?>
				</a>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body search">
	<?php echo Form::open(URL::site('search'), array('method' => 'GET')); ?>
		<?php echo Form::hidden('scope', Session::instance()->get('search_scope')); ?>
		<div class="field cf">
			<?php echo Form::input('q', '', array('class'=> "search", 'placeholder' => __("What do you want to search for?"))); ?>
			<ul class="dual-buttons">
				<?php if (Session::instance()->get('search_scope') !== 'all'): ?>
				<li class="button-blue">
					<a href="#" onclick="submitForm(this)">
					<?php 
						echo __("Search this :search_scope", 
							array(':search_scope' => Session::instance()->get('search_scope'))); 
					?>
					</a>
				</li>
				<?php endif; ?>
				<li class="button-blue">
					<a href="#" onclick="submitForm(this)"><?php echo __("Search everything"); ?></a>
				</li>
			</ul>
		</div>
	<?php echo Form::close(); ?>
	</div>
</article>

<?php endif; ?>