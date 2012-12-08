<article class="modal">
	<div id="modal-viewport">
		<div id="modal-primary" class="modal-view">
			<div class="modal-title cf">
				<a href="#" class="modal-close button-white"><i class="icon-cancel"></i>Close</a>
				<h1><a href="#group-name" class="modal-transition">Add search filter</a></h1>
			</div>
			
			<div class="modal-body modal-tabs-container">
				<div class="base">
					<ul class="modal-tabs-menu">
						<li class="active"><a href="#add-keyword"><span class="channel-icon icon-pencil"></span></a></li>
						<li><a href="#add-date"><span class="channel-icon icon-calendar"></span></a></li>
					</ul>
					<div class="modal-tabs-window">
						<!-- ADD Keyword filter -->
						<div id="add-keyword" class="active">
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
				
				<div class="modal-toolbar">
					<a href="#" class="button-submit button-primary modal-close">Add filter</a>				
				</div>					
			</div>
		</div>
		<div id="modal-secondary" class="modal-view"></div>						
	</div>
</article>