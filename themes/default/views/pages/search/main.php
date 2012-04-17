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
	<?php echo Form::open(URL::site('search'), array('method' => 'GET', 'id'=>'drop_search_form')); ?>
		<div class="field cf">
			<?php echo Form::input('q', '', array('class'=> "search", 'placeholder' => __("What do you want to search for?"))); ?>
			<ul class="dual-buttons">
				<?php if ( ! empty($search_scope) AND $search_scope != 'all'): ?>
				<li class="button-blue">
					<a href="#" onclick="submitForm(this)">
					<?php 
						echo __("Search this :search_scope", 
							array(':search_scope' => Session::instance()->get('search_scope'))); 
					?>
					</a>
				</li>
				<?php endif; ?>
				<?php $submit_js = ($search_scope == 'all') ? 'submitForm(this)' : ''; ?>
				<li class="button-blue" id="search_all">
					<a href="#" onclick="<?php echo $submit_js; ?>"><?php echo __("Search everything"); ?></a>
				</li>
			</ul>
		</div>
	<?php echo Form::close(); ?>
	</div>

	<?php if ($search_scope != 'all'): ?>
	<script type="text/javascript">
		$(document).ready(function() {
			var searchForm = $("#drop_search_form");
			var element = document.createElement('input');
			$(element).attr({'type': 'hidden', 'name':'search_scope', 'value': 'all'});
			$(element).appendTo(searchForm);

			// Event bindings for the "Search Everything" button
			$("#search_all > a", searchForm).live('click', function(){
				searchForm.submit();
			});
		});
	</script>
	<?php endif; ?>

</article>