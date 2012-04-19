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
			<?php $filter_params = ( ! empty($search_filters)) ? '?'.$search_filters : '';  ?>
			<ul class="touchcarousel-container">
				<li class="touchcarousel-item <?php if ($active == 'drops') echo 'active'; ?>">
					<a href="<?php echo URL::site('search/drops'.$filter_params); ?>"><?php echo __("Drops"); ?></a>
				</li>

				<?php $search_scope = Session::instance()->get('search_scope'); ?>
				<?php if ($search_scope == 'all'): ?>
				<li class="touchcarousel-item <?php if ($active == 'rivers') echo 'active'; ?>">
					<a href="<?php echo URL::site('search/rivers'.$filter_params); ?>">
						<?php echo __("Rivers"); ?>
					</a>
				</li>
				<li class="touchcarousel-item <?php if ($active == 'buckets') echo 'active'; ?>">
					<a href="<?php echo URL::site('search/buckets'.$filter_params); ?>">
						<?php echo __("Buckets"); ?>
					</a>
				</li>
				<li class="touchcarousel-item <?php if ($active == 'users') echo 'active'; ?>">
					<a href="<?php echo URL::site('search/users'.$filter_params); ?>">
						<?php echo __("Users"); ?>
					</a>
				</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</nav>
<?php endif; ?>

<?php echo $sub_content; ?>
