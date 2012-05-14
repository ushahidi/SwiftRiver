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
			<?php $url_params = (empty($url_params)) ? '' : '?'.$url_params; ?>
			<ul class="touchcarousel-container">
				<li id="list-navigation-link" class="touchcarousel-item <?php if ($active == 'list') echo 'active'; ?>">
					<a href="<?php echo URL::site('search/list'.$url_params); ?>"><?php echo __("ui.nav.list"); ?></a>
				</li>
				<li id="photos-navigation-link" class="touchcarousel-item <?php if ($active == 'photos') echo 'active'; ?>">
					<a href="<?php echo URL::site('search/photos'.$url_params); ?>"><?php echo __("ui.nav.photos"); ?></a>
				</li>

				<?php if ($search_scope === 'all') : ?>
				<li class="touchcarousel-item <?php if ($active == 'rivers') echo 'active'; ?>">
					<a href="<?php echo URL::site('search/rivers'.$url_params); ?>">
						<?php echo __("ui.nav.rivers"); ?>
					</a>
				</li>
				<li class="touchcarousel-item <?php if ($active == 'buckets') echo 'active'; ?>">
					<a href="<?php echo URL::site('search/buckets'.$url_params); ?>">
						<?php echo __("ui.nav.buckets"); ?>
					</a>
				</li>
				<li class="touchcarousel-item <?php if ($active == 'users') echo 'active'; ?>">
					<a href="<?php echo URL::site('search/users'.$url_params); ?>">
						<?php echo __("ui.nav.users"); ?>
					</a>
				</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</nav>
<?php endif; ?>

<?php echo $sub_content; ?>
