<p class="pagination blue">

	<?php if ($first_page !== FALSE): ?>
		<a href="<?php echo HTML::chars($page->url($first_page)) ?>" rel="first" class="number">&lt;&lt;</a>
	<?php else: ?>
		<a href="<?php echo HTML::chars($page->url($first_page)) ?>" rel="first" class="number disable">&lt;&lt;</a>
	<?php endif ?>

	<?php if ($previous_page !== FALSE): ?>
		<a href="<?php echo HTML::chars($page->url($previous_page)) ?>" rel="prev" class="number">&lt;</a>
	<?php else: ?>
		<a href="<?php echo HTML::chars($page->url($previous_page)) ?>" rel="prev" class="number disable">&lt;</a>
	<?php endif ?>

	<?php for ($i = 1; $i <= $total_pages; $i++): ?>

		<?php if ($i == $current_page): ?>
			<a href="<?php echo HTML::chars($page->url($i)) ?>" class="number current"><?php echo $i ?></a>
		<?php else: ?>
			<a href="<?php echo HTML::chars($page->url($i)) ?>" class="number"><?php echo $i ?></a>
		<?php endif ?>

	<?php endfor ?>

	<?php if ($next_page !== FALSE): ?>
		<a href="<?php echo HTML::chars($page->url($next_page)) ?>" rel="next" class="number">&gt;</a>
	<?php else: ?>
		<a href="<?php echo HTML::chars($page->url($next_page)) ?>" rel="next" class="number disable">&gt;</a>
	<?php endif ?>

	<?php if ($last_page !== FALSE): ?>
		<a href="<?php echo HTML::chars($page->url($last_page)) ?>" rel="last" class="number">&gt;&gt;</a>
	<?php else: ?>
		<a href="<?php echo HTML::chars($page->url($last_page)) ?>" rel="last" class="number disable">&gt;&gt;</a>
	<?php endif ?>

</p><!-- .pagination -->