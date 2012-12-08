<article class="modal">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1><a href="#edit-name" class="modal-transition">Move to Bucket 3</a></h1>
			</div>
			
			<div class="modal-body">
				
				<div class="view-table base">
					<h2 class="label">Conditions</h2>
					<ul>
						<li>
							<a href="#custom-condition-1" class="modal-transition">
							<span class="remove icon-cancel"></span>
							Source is <em>Brandon Rosage</em>
							</a>
						</li>
						<li>
							<a href="#custom-condition-2" class="modal-transition">						
							<span class="remove icon-cancel"></span>
							Content contains <em>blues, music, austin</em>
							</a>
						</li>
						<li class="add"><a href="#add-condition" class="modal-transition">Add condition</a></li>
					</ul>
				</div>
				
				<div class="view-table base">
					<h2 class="label">Actions</h2>
					<ul>
						<li class="static"><span class="remove icon-cancel"></span>Add to Bucket 3</li>
						<li class="add"><a href="#add-actions" class="modal-transition">Add actions</a></li>
					</ul>
				</div>
				
				<div class="modal-toolbar">
					<a href="#" class="button-destruct button-secondary">Delete rule</a>
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
			
			<!-- START: Edit rule name -->				
			<div id="edit-name" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Edit rule name</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-field">
							<input type="text" value="Move to Bucket 3" />
						</div>
					</div>
				</div>
			</div>

			<!-- START: Edit custom condition 1 -->				
			<div id="custom-condition-1" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Edit condition</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-field">
							<select>
								<option>Source</option>
								<option>Title</option>
								<option>Content</option>
								<option>Lorem ipsum</option>
							</select>
							
							<select>
								<option>is</option>
								<option>contains</option>
								<option>does not contain</option>
							</select>							

							<input type="text" value="Brandon Rosage" />
						</div>
					</div>
				</div>
			</div>

			<!-- START: Edit custom condition 2 -->				
			<div id="custom-condition-2" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Edit condition</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-field">
							<select>
								<option>Content</option>
								<option>Title</option>
								<option>Source</option>
								<option>Lorem ipsum</option>
							</select>
							
							<select>
								<option>contains</option>
								<option>is</option>
								<option>does not contain</option>
							</select>							

							<input type="text" value="blues, music, austin" />
						</div>
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
							<li class="static selected cf">
								<span class="select icon-plus"></span>
								Bucket 3
							</li>
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