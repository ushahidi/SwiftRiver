<article class="modal">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1><?php echo __("Search"); ?></h1>
		</div>
		<div class="page-action col_3">
			<h2 class="close">
				<span class="button-white"><a href="#"><i class="icon-cancel"></i>Close</a></span>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body search">
	<?php echo Form::open(URL::site('search'), array('method' => 'GET', 'id'=>'drop_search_form')); ?>
		<div class="field cf">
			<?php echo Form::input('q', '', array('class'=> "search", 'placeholder' => __("What do you want to search for?"))); ?>		
		</div>
		<div class="save-toolbar">
			<p class="button-blue">
				<?php $submit_js = ($search_scope !== 'all') ? '' : 'onClick="submitForm(this)"'; ?>
				<a href="#" <?php echo $submit_js; ?>><?php echo __("Search everything"); ?></a>			
			</p>
			<?php if ( ! empty($search_scope) AND $search_scope !== 'all'): ?>			
			<p class="button-blue">
				<a href="#">
				<?php 
					echo __("Search this :search_scope", 
						array(':search_scope' => $search_scope)); 
				?>
				</a>
			</p>
			<?php endif; ?>
		</div>
	<?php echo Form::close(); ?>
	</div>

	<?php if ($search_scope !== 'all'): ?>
	<script type="text/javascript">
		$(document).ready(function() {
			var searchForm = $("#drop_search_form");
			var element = document.createElement('input');
			$(element).attr({
				'type': 'hidden', 
				'name':'search_scope', 
				'value': '<?php echo $search_scope; ?>'
			});

			$(element).appendTo(searchForm);

			// Event bindings for the "Search this xxxx" button
			$("#specific_search > a", searchForm).click(function(){
				searchForm.submit();
			});

			// Event binding for the "Search Everything" button
			$("#all_search > a", searchForm).click(function(){
				$(element).val('all');
				searchForm.submit();
			});
		});
	</script>
	<?php endif; ?>

</article>