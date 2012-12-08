<article class="modal">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1>Channels</h1>
			</div>
			
			<div class="modal-body">
				<div class="view-table base">
					<ul>
						<li>
							<a href="#edit-channel-1" class="modal-transition">
							<span class="remove icon-cancel"></span>
							<i class="channel-icon icon-twitter"></i>
							@Mainamshy, @rkulei, @Mainamshy, @rkulei
							</a>
						</li>
						<li>
							<a href="#edit-channel-2" class="modal-transition">
							<span class="remove icon-cancel"></span>
							<i class="channel-icon icon-facebook"></i>
							DailyNation, KTNKenya
							</a>
						</li>
						<li>
							<a href="#edit-channel-3" class="modal-transition">
							<span class="remove icon-cancel"></span>
							<i class="channel-icon icon-rss"></i>
							The Kenyan Post
							</a>
						</li>
						<li>
							<a href="#edit-channel-3" class="modal-transition">
							<span class="remove icon-cancel"></span>
							<i class="channel-icon icon-rss"></i>
							African Press
							</a>
						</li>
						<li>
							<a href="#edit-channel-3" class="modal-transition">
							<span class="remove icon-cancel"></span>
							<i class="channel-icon icon-rss"></i>
							Standard Media
							</a>
						</li>												
						<li class="add"><a href="#add-channel" class="modal-transition">Add channel</a></li>
					</ul>
				</div>

				<div class="modal-toolbar">
					<a href="#" class="button-submit button-primary modal-close">Done</a>				
				</div>				
			</div>
		</div>
		<div id="modal-secondary" class="modal-view">

			<!-- START: Add channel -->				
			<div id="add-channel" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Add channel</h1>
				</div>
				
				<div class="modal-body modal-tabs-container">
					<div class="base">
						<ul class="modal-tabs-menu">
							<li><a href="#add-twitter"><span class="channel-icon icon-twitter"></span></a></li>
							<li><a href="#add-facebook"><span class="channel-icon icon-facebook"></span></a></li>
							<li><a href="#add-rss"><span class="channel-icon icon-rss"></span></a></li>
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
						<a href="#" class="button-submit button-primary modal-back">Add channel</a>				
					</div>					
				</div>
			</div>

			<!-- START: Edit channel 1 -->				
			<div id="edit-channel-1" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Edit channel</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<h2 class="label">Twitter</h2>

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
							
							<!-- IF: Parameter added -->
							<div class="modal-field-parameter">									
								<select style="display:block;">
									<option>AND</option>
									<option>OR</option>
								</select>
								
								<input type="text" value="SXSW" />
							</div>
	
							<div class="modal-field-parameter">									
								<select style="display:block;">
									<option>AND</option>
									<option>OR</option>
								</select>
								
								<input type="text" value="Austin, TX" />
								<select style="display:block;">
									<option>within 100km</option>
									<option>within 1000km</option>
								</select>
							</div>														
							<!-- -->
						</div>
					</div>			
				</div>
			</div>
			
			<!-- START: Edit channel 2 -->				
			<div id="edit-channel-2" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Edit channel</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<h2 class="label">Facebook</h2>
						<div class="modal-field">
							<h3 class="label">Facebook Page name</h3>
							<a href="#" class="add-field"><span class="icon-plus"></span></a>
							<input type="text" placeholder="Enter the name of a Facebook page" />
							<!-- IF: Parameter added -->
							<div class="modal-field-parameter">									
								<select style="display:block;">
									<option>AND</option>
									<option>OR</option>
								</select>
								
								<input type="text" value="SwiftRiver" />
							</div>
	
							<div class="modal-field-parameter">									
								<select style="display:block;">
									<option>AND</option>
									<option>OR</option>
								</select>
								
								<input type="text" value="SXSW" />
							</div>													
							<!-- -->
						</div>
					</div>			
				</div>
			</div>
			
			<!-- START: Edit channel 3 -->				
			<div id="edit-channel-3" class="modal-segment">
				<div class="modal-title cf">
					<a href="#" class="modal-back button-white"><span class="icon-arrow-left"></span></a>
					<h1>Edit channel</h1>
				</div>
				
				<div class="modal-body">
					<div class="base">
						<h2 class="label">RSS</h2>
						<div class="modal-field">
							<h3 class="label">RSS URL</h3>
							<input type="text" value="http://mashable.com/rss" />
						</div>
					</div>			
				</div>
			</div>						
		
		</div>
	</div>
</article>