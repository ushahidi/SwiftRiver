<article class="modal">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1><a href="#edit-name" class="modal-transition">Speech type</a></h1>
			</div>
			
			<div class="modal-body">
				
				<div class="view-table base">
					<h2 class="label">Fields</h2>
					<ul>
						<li>
							<a href="#custom-field-1" class="modal-transition">
							<span class="remove icon-cancel"></span>
							Lorem ipsum
							</a>
						</li>
						<li>
							<a href="#custom-field-2" class="modal-transition">						
							<span class="remove icon-cancel"></span>
							Lorem ipsum dolor
							</a>
						</li>
						<li class="add"><a href="#add-field" class="modal-transition">Add field</a></li>
					</ul>
				</div>				

				<div class="base">
					<h2 class="label">Collaborators</h2>											
					<ul class="view-table">
						<li class="static user cf">
							<span class="remove icon-cancel"></span>
							<img src="https://si0.twimg.com/profile_images/2525445853/TweetLandPhoto_normal.jpg" class="avatar">Juliana Rotich
						</li>
						<li class="static user cf">
							<span class="remove icon-cancel"></span>
							<img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_normal.jpeg" class="avatar">Nathaniel Manning
						</li>
						<li class="add"><a href="#add-collaborator" class="modal-transition">Add collaborator</a></li>
					</ul>													
				</div>
				
				<div class="modal-toolbar">
					<a href="#" class="button-destruct button-secondary">Delete</a>
					<a href="#" class="button-submit button-primary modal-close">Done</a>				
				</div>				
			</div>
		</div>
		<div id="modal-secondary" class="modal-view">

			<!-- START: Add field -->				
			<div id="add-field" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Add field</h1>
				</div>
				
				<div class="modal-body modal-tabs-container">

					<div class="base">
						<ul class="modal-tabs-menu">
							<li class="active"><a href="#add-custom-text"><span class="label">Text input</span></a></li>
							<li><a href="#add-custom-textarea"><span class="label">Text area</span></a></li>
							<li><a href="#add-custom-checkbox"><span class="label">Checkbox</span></a></li>
							<li><a href="#add-custom-list"><span class="label">List</span></a></li>
						</ul>
						<div class="modal-tabs-window">

							<!-- ADD Custom text input -->
							<div id="add-custom-text" class="active">
								<div class="modal-field">
									<h3 class="label">Text input label</h3>
									<a href="#" class="add-field"><span class="icon-plus"></span></a>
									<input type="text" placeholder="Enter input description..." />
									<label>
								</div>																
							</div>

							<!-- ADD Custom text input -->
							<div id="add-custom-textarea">
								<div class="modal-field">
									<h3 class="label">Text area label</h3>
									<a href="#" class="add-field"><span class="icon-plus"></span></a>
									<input type="text" placeholder="Enter text area description..." />
									<label>
								</div>																
							</div>						

							<!-- ADD Checkbox rule -->
							<div id="add-custom-checkbox">
								<div class="modal-field">
									<h3 class="label">Checkbox label</h3>
									<a href="#" class="add-field"><span class="icon-plus"></span></a>
									<input type="text" placeholder="Enter new checkbox description..." />								
									<input type="text" value="Sample checkbox addition" />
								</div>
							</div>

							<!-- ADD List field -->
							<div id="add-custom-list">
								<div class="modal-field">
									<h3 class="label">List label</h3>
									<input type="text" placeholder="Enter new list description..." />								
								</div>
								<div class="modal-field">
									<h3 class="label">List options</h3>
									<a href="#" class="add-field"><span class="icon-plus"></span></a>
									<input type="text" placeholder="Enter new list option..." />
									<input type="text" value="Sample list option addition" />							
								</div>
							</div>																											
						</div>						
					</div>

					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-back">Add field</a>				
					</div>
				</div>
			</div>
			
			<!-- START: Edit Custom form name -->				
			<div id="edit-name" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Edit custom form name</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-field">
							<input type="text" value="Speech type" />
						</div>
					</div>
				</div>
			</div>

			<!-- START: Edit custom field 1 -->				
			<div id="custom-field-1" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Edit field</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-field">
							<h3 class="label">Text input label</h3>
							<input type="text" value="Lorem ipsum" />
						</div>
					</div>
				</div>
			</div>

			<!-- START: Edit custom field 2 -->				
			<div id="custom-field-2" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Edit field</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-field">
							<h3 class="label">Checkbox label</h3>
							<input type="text" value="Lorem ipsum dolor" />
						</div>
					</div>
				</div>
			</div>
			
			<!-- START: Add collaborator -->				
			<div id="add-collaborator" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Add collaborator</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-search-field">
							<input type="text" placeholder="Find a user..." />
							<a href="#" class="button-submit"><span class="icon-search"></span></a>
						</div>
						
						<div class="modal-search-results">
							<ul class="view-table">
								<li class="static selected user cf">
									<span class="select icon-plus"></span>
									<img src="https://si0.twimg.com/profile_images/2525445853/TweetLandPhoto_normal.jpg" class="avatar">Juliana Rotich
								</li>
								<li class="static user cf">
									<span class="select icon-plus"></span>
									<img src="https://si0.twimg.com/profile_images/2448693999/emrjufxpmmgckny5frdn_normal.jpeg" class="avatar">Nathaniel Manning
								</li>
							</ul>													
						</div>
					</div>
					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-back">Done</a>				
					</div>
				</div>
			</div>			
							
		</div>
	</div>
</article>