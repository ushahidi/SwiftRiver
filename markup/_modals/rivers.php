<article class="modal">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1><i class="icon-river"></i>Rivers</h1>
			</div>
			
			<div class="modal-body">			
				<div class="base">
					<h2 class="label">Managing</h2>
					<ul class="view-table">
						<li><a href="/markup/river">Ushahidi at SXSW</a></li>
						<li><a href="#">River 2</a></li>
						<li><a href="#">River 3</a></li>
						<li><a href="#">River 4</a></li>
						<li class="add"><a href="#create-river" class="modal-transition">Create a new river</a></li>
					</ul>													
				</div>
				<div class="base">
					<h2 class="label">Following</h2>
					<ul class="view-table">
						<li><a href="#">River 5</a></li>
						<li><a href="#">River 6</a></li>
						<li><a href="#">River 7</a></li>
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
		
		</div>
	</div>
</article>