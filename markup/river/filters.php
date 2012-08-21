<article class="modal">
	<hgroup class="page-title cf">
		<div class="page-h1 col_9">
			<h1>Add filter</h1>
		</div>
		<div class="page-actions col_3">
			<h2 class="close">
				<a href="#">
					<span class="icon-cancel"></span>
					Close
				</a>
			</h2>
		</div>
	</hgroup>

	<div class="modal-body modal-containers link-list">
		<!-- IF NO FILTERS APPLIED //
		<ul>
			<li><a href="#">+ Channel</a></li>
			<li><a href="#">+ Date</a></li>
			<li><a href="#">+ Keyword</a></li>
			<li><a href="#">+ Tag</a></li>
		</ul>
		// END: IF NO FILTERS APPLIED -->

		<div class="settings-toolbar">
			<p class="button-blue button-small has-icon create"><a href="/markup/modal-filters.php" class="modal-trigger"><span class="icon-plus"></span>Add filter</a></p>
		</div>

		<article class="container base">
			<header class="cf">
				<a href="#" class="remove-large"><span class="icon-cancel"></span><span class="nodisplay">Remove</span></a>
				<div class="property-title">
					<h1>Date</h1>
				</div>
			</header>
			<section class="property-parameters">
				<div class="parameter">
						<input type="date" name="date_range-start" />
						<span class="combine">to</span>
						<input type="date" name="date_range-end" />
					</label>
				</div>
			</section>
		</article>

		<select class="boolean-operator">
			<option>and</option>
			<option>or</option>
		</select>

		<article class="container base">
			<header class="cf">
				<a href="#" class="remove-large"><span class="icon"></span><span class="nodisplay">Remove</span></a>
				<div class="property-title">
					<h1>Keyword</h1>
					<p class="button-white has-icon add add-parameter"><a href="#"><span class="icon"></span>Add keyword</a></p>
				</div>
			</header>
			<section class="property-parameters">
				<div class="parameter">
					<label for="twitter_keyword">
						<p class="field">Keyword</p>
						<input type="text" name="twitter_keyword" />
						<p class="remove-small actions"><span class="icon"></span><span class="nodisplay">Remove</span></p>
					</label>
				</div>
			</section>
		</article>
	</div>
</article>