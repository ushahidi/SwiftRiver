<article class="modal">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1>Add rule</h1>
			</div>
			
			<div class="modal-body">
				<div class="base">
					<h2 class="label">Name</h2>
					<div class="modal-field">
						<input type="text" placeholder="Name your new rule" />
					</div>					
				</div>
				
				<div class="view-table base">
					<h2 class="label">Conditions</h2>
					<ul>
						<li class="add"><a href="#add-condition" class="modal-transition">Add condition</a></li>
					</ul>
				</div>
				
				<div class="view-table base">
					<h2 class="label">Actions</h2>
					<ul>
						<li class="add"><a href="#add-actions" class="modal-transition">Add actions</a></li>
					</ul>
				</div>
				
				<div class="modal-toolbar">
					<a href="#" class="button-submit button-primary modal-close">Done</a>				
				</div>				
			</div>
		</div>
		<div id="modal-secondary" class="modal-view">

			<!-- START: Add condition -->				
			<div id="add-condition" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Add condition</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-field">
							<select>
								<option>Title</option>
								<option>Content</option>
								<option>Source</option>
								<option>Lorem ipsum</option>
							</select>
							
							<select>
								<option>contains</option>
								<option>is</option>
								<option>does not contain</option>
							</select>
							
							<input type="text" placeholder="Enter keywords..." />
						</div>																
					</div>
					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-back">Add rule</a>				
					</div>						
				</div>
			</div>
			
			<!-- START: Add actions -->				
			<div id="add-actions" class="modal-segment">						
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Add actions</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<h2 class="label">Remove</h2>
						<div class="modal-field">
							<label>
								<input type="checkbox" /> Remove from river
							</label>
						</div>																
					</div>

					<div class="base">
						<h2 class="label">Change status</h2>
						<div class="modal-field">
							<label>
								<input type="checkbox" /> Mark as read
							</label>
						</div>
					</div>

					<div class="base">
						<h2 class="label">Add to buckets</h2>
						<ul class="view-table">
							<li class="static cf">
								<span class="select icon-plus"></span>
								Kenya elections hate speech
							</li>
							<li class="static cf">
								<span class="select icon-plus"></span>
								Ushahidi design inspiration
							</li>
						</ul>
					</div>
					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-back">Done</a>				
					</div>					
				</div>
			</div>
							
		</div>
	</div>
</article>