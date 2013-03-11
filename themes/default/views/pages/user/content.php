<div id="filters-trigger" class="col_12 cf">
	<a href="#" class="button button-primary filters-trigger"><i class="icon-filter"></i>Filters</a>
</div>
			
<section id="filters" class="col_3">
	<div class="modal-window">
		<div class="modal">		
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1>Filters</h1>
			</div>

			<ul class="filters-primary">
				<li class="active"><a href="#all" class="modal-close"><?php echo __("All your stuff"); ?></a></li>
				<li><a href="#river" class="modal-close"><?php echo __("Rivers"); ?></a></li>
				<li><a href="#bucket" class="modal-close"><?php echo __("Buckets"); ?></a></li>
			</ul>

			<div class="modal-toolbar">
				<a href="#" class="button-submit button-primary modal-close">Done</a>
			</div>
		</div>
	</div>
</section>

<div class="col_9">
	<div class="container base">
		<div class="container-tabs">
			<ul class="container-tabs-menu cf">
				<li class="active"><a href="#all"><?php echo __("All"); ?></a></li>
				<li><a href="#managing"><?php echo __("Managing"); ?></a></li>
				<li><a href="#following"><?php echo __("Following"); ?></a></li>
			</ul>

			<div class="container-toolbar cf">
				<span class="button">
					<a href="#" class="button-white delete-asset"><?php echo __("Delete"); ?></a>
				</span>
				<span class="button" class="button-white">
					<a href="#" class="button-white"><?php echo __("Duplicate"); ?></a>
				</span>
				<?php if ($owner): ?>
				<span class="button create-new">
					<a href="#" class="button-primary modal-trigger">
						<i class="icon-plus"></i>
						<?php echo __("Create new"); ?>
					</a>
				</span>
				<?php endif; ?>
			</div>

			<div class="container-tabs-window">
				<!-- NO RESULTS: Clear filters
				<div class="null-message">
					<h2>No rivers or buckets to show.</h2>
					<p>Clear active filters.</p>
				</div>
				-->

				<table>
					<tbody id="asset-list">
						<!-- List of river, buckets go here -->
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<script type="text/template" id="asset-template">
	<td class="select-toggle">
	<% if (is_owner) { %>
		<input type="checkbox" />
	<% } %>
	</td>
	<td class="item-type">
		<span class="icon-<%= asset_type %>"></span>
	</td>										
	<td class="item-summary">
		<h2><a href="<%= url %>"><%= display_name %></a></h2>
		<% if (asset_type == "river") { %>
		<div class="metadata">
			<%= drop_count %> drops
			<% percent_full = (drop_count >0) ? Math.round(drop_count/drop_quota) : 0 %>			
			<span class="quota-meter">
				<span class="quota-meter-capacity">
					<span class="quota-total" style="width:<%= percent_full %>%;"></span>
				</span>
				<%= percent_full %>% full
			</span>
		</div>	
		<% } %>
	</td>
	<td class="item-categories">
	</td>
</script>

<script type="text/template" id="create-asset-modal-template">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white">
					<i class="icon-cancel"></i>
					<?php echo __("Close"); ?>
				</a>
				<h1><?php echo __("Create new"); ?></h1>
			</div>
			
			<div class="modal-body">
				<div class="base">
					<ul class="view-table">
						<li>
							<a href="#river" class="modal-transition">
								<span class="transition icon-arrow-right"></span>
								<i class="icon-river"></i>
								<?php echo __("River"); ?>
							</a>
						</li>
						<li>
							<a href="#bucket" class="modal-transition">
								<span class="transition icon-arrow-right"></span>
								<i class="icon-bucket"></i>
								<?php echo __("Bucket"); ?>
							</a>
						</li>
						<?php 
						/** 
						 * Custom forms not yet implemented
						 * <li>
						 * 	<a href="#create-custom-form" class="modal-transition">
						 * 		<span class="transition icon-arrow-right"></span>
						 * 		<i class="icon-form"></i>
						 * 		<?php echo __("Custom form"); ?>
						 * 	</a>
						 * </li>
						 */
						?>
					</ul>
				</div>
			</div>

		</div>
		<div id="modal-secondary" class="modal-view">
	
		</div>
	</div>
</script>