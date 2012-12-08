<article class="modal">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1>Create new</h1>
			</div>
			
			<div class="modal-body">			
				<div class="base">
					<ul class="view-table">
						<li><a href="#create-river" class="modal-transition"><span class="transition icon-arrow-right"></span><i class="icon-river"></i>River</a></li>
						<li><a href="#create-bucket" class="modal-transition"><span class="transition icon-arrow-right"></span><i class="icon-bucket"></i>Bucket</a></li>
						<li><a href="#create-custom-form" class="modal-transition"><span class="transition icon-arrow-right"></span><i class="icon-form"></i>Custom form</a></li>
					</ul>													
				</div>
			</div>

		</div>
		<div id="modal-secondary" class="modal-view">

			<!-- START: Create new river -->				
			<div id="create-river" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Create a new river</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-field">
							<h3 class="label">Name</h3>
							<input type="text" placeholder="Name your new river" />
						</div>
					</div>

					<div class="base modal-tabs-container">
						<h2 class="label">Open channels</h2>
						<ul class="modal-tabs-menu">
							<li><a href="#add-twitter"><span class="channel-icon icon-twitter"></span></a></li>
							<li><a href="#add-facebook"><span class="channel-icon icon-facebook"></span></a></li>
							<li><a href="#add-rss"><span class="channel-icon icon-rss"></span></a></li>
							<li><a href="#add-email"><span class="channel-icon icon-mail"></span></a></li>
						</ul>
						<div class="modal-tabs-window">
							<div class="active"></div>
							
							<!-- ADD Twitter -->
							<div id="add-twitter">
								<div class="modal-field modal-field-tabs-container">
									<ul class="modal-field-tabs-menu">
										<li class="active"><a href="#input-keywords">Keywords</a></li>
										<li><a href="#input-users">Users</a></li>
										<li><a href="#input-location">Location</a></li>
									</ul>
									<div class="modal-field-tabs-window">
										<div id="input-keywords" class="active">
											<a href="#" class="add-field"><span class="icon-plus"></span></a>									
											<input type="text" placeholder="Enter keywords, separated by commas" />
										</div>
										<div id="input-users">
											<a href="#" class="add-field"><span class="icon-plus"></span></a>									
											<input type="text" placeholder="Enter usernames, separated by commas" />
										</div>
										<div id="input-location">
											<a href="#" class="add-field"><span class="icon-plus"></span></a>									
											<input type="text" placeholder="Enter location" />
											<select style="display:block;">
												<option>within 100km</option>
												<option>within 1000km</option>
											</select>
										</div>																				
									</div>
									
									<!-- IF: Parameter added
									<div class="modal-field-parameter">									
										<select style="display:block;">
											<option>AND</option>
											<option>OR</option>
										</select>
										
										<input type="text" value="SXSW" />
									</div>								
									-->
								</div>
							</div>

							<!-- ADD Facebook -->
							<div id="add-facebook">
								<div class="modal-field">
									<h3 class="label">Facebook Page name</h3>
									<a href="#" class="add-field"><span class="icon-plus"></span></a>
									<input type="text" placeholder="Enter the name of a Facebook page" />
									<!-- IF: Parameter added
									<div class="modal-field-parameter">									
										<select style="display:block;">
											<option>AND</option>
											<option>OR</option>
										</select>
										
										<input type="text" value="SXSW" />
									</div>								
									-->
								</div>
							</div>
							
							<!-- ADD RSS -->
							<div id="add-rss">
								<div class="modal-field">
									<h3 class="label">RSS URL</h3>
									<a href="#" class="add-field"><span class="icon-plus"></span></a>
									<input type="text" placeholder="Enter the address to an RSS feed" />
								</div>
							</div>

							<!-- ADD EMAIL -->
							<div id="add-email">
								<div class="modal-field">
									<h3 class="label">Email address</h3>
									<input type="text" placeholder="Enter your full email address" />
								</div>
								<div class="modal-field">
									<h3 class="label">Password</h3>
									<input type="password" />
								</div>								
							</div>																					
						</div>
					</div>
					
					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-close">Create river</a>				
					</div>					
				</div>
			</div>

			<!-- START: Create new bucket -->				
			<div id="create-bucket" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Create a new bucket</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<div class="modal-field">
							<h3 class="label">Name</h3>
							<input type="text" placeholder="Name your new bucket" />
						</div>
					</div>
					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-close">Create bucket</a>				
					</div>					
				</div>
			</div>
			
			<!-- START: Create new filter group -->				
			<div id="create-filter-group" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Create a new filter group</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<h2 class="label">Basics</h2>
						<div class="modal-field">
							<h3 class="label">Group name</h3>
							<input type="text" placeholder="Name this filter group" />
						</div>
						<div class="modal-field">
							<h3 class="label">Who can see this group</h3>
							<select>
								<option>Everyone</option>
								<option>Only you</option>
							</select>
						</div>
					</div>

					<div class="modal-tabs-container">
						<div class="base">
							<h2 class="label">Filters</h2>
							<ul class="modal-tabs-menu">
								<li><a href="#add-keyword"><span class="channel-icon icon-pencil"></span></a></li>
								<li><a href="#add-date"><span class="channel-icon icon-calendar"></span></a></li>
							</ul>
							<div class="modal-tabs-window">
								<div class="active"></div>
								<!-- ADD Keyword filter -->
								<div id="add-keyword">
									<div class="modal-field">
										<h3 class="label">Keyword</h3>
										<input type="text" placeholder="Enter keywords..." />
									</div>
								</div>
	
								<!-- ADD Date filter -->
								<div id="add-date">
									<div class="modal-field">
										<h3 class="label">From</h3>
										<input type="date" placeholder="Start date" />
									</div>
									<div class="modal-field">
										<h3 class="label">To</h3>
										<input type="date" placeholder="End date" />
									</div>								
								</div>																				
							</div>
						</div>
					</div>					
					
					<div class="modal-toolbar">
						<a href="#" class="button-submit button-primary modal-close">Create filter group</a>				
					</div>					
				</div>
			</div>							

			<!-- START: Create custom form -->				
			<div id="create-custom-form" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Create a new custom form</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<h2 class="label">Basics</h2>
						<div class="modal-field">
							<h3 class="label">Form name</h3>
							<input type="text" placeholder="Name this custom form" />
						</div>
					</div>

					<div class="base">
						<h2 class="label">Fields</h2>
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
					
					<div class="base">
						<h2 class="label">Collaborators</h2>						
						<div class="modal-search-field">
							<input type="text" placeholder="Find a person..." />
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
						<a href="#" class="button-submit button-primary modal-close">Create custom form</a>				
					</div>					
				</div>
			</div>
		
		</div>
	</div>
</article>