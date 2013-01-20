<article class="modal">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1><a href="#group-name" class="modal-transition">Edit media</a></h1>
			</div>
			
			<div class="modal-body">
				<div class="view-table base">
					<ul>
						<li class="static cf">
							<span class="remove icon-cancel"></span>
							<img src="http://omwenga.files.wordpress.com/2012/09/raila_at_ease1.jpg?w=645" class="metadata-media" />
						</li>
						<li class="add"><a href="#add-media" class="modal-transition">Add media</a></li>
					</ul>
				</div>			
			</div>
		</div>
		<div id="modal-secondary" class="modal-view">

			<!-- START: Add media -->				
			<div id="add-media" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Add media</h1>
				</div>
				
				<div class="modal-body modal-tabs-container">
					<div class="base">
						<ul class="modal-tabs-menu">
							<li class="active"><a href="#add-photo"><span class="channel-icon icon-photo"></span></a></li>
							<li><a href="#add-video"><span class="channel-icon icon-video"></span></a></li>
						</ul>
						<div class="modal-tabs-window">
							<!-- ADD Photo -->
							<div id="add-photo" class="active">
								<div class="modal-field">
									<h3 class="label">Upload a photo</h3>
									<input type="file" />
								</div>
								<span class="option">or</span>
								<div class="modal-field">
									<h3 class="label">Link to a photo</h3>
									<input type="text" placeholder="Enter URL for photo" />
								</div>								
							</div>

							<!-- ADD Video -->
							<div id="add-video">
								<div class="modal-field">
									<h3 class="label">Upload a video</h3>
									<input type="file" />
								</div>							
								<span class="option">or</span>
								<div class="modal-field">
									<h3 class="label">Link to a video</h3>
									<input type="text" placeholder="Enter URL for video" />
								</div>
							</div>																				
						</div>
					</div>
					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-close">Done</a>				
					</div>					
				</div>	
			</div>
							
		</div>
	</div>
</article>