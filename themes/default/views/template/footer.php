	<div id="zoom-container">
		<div class="modal-window">
			<div id="modal-viewport">
			</div>
		</div>
	</div>

	<div id="modal-container">
		<div class="modal-window">
			<div id="modal-viewport">
			</div>
		</div>
		<div class="modal-window-secondary"></div>
	</div>

	<div id="confirmation-container">
		<div class="modal-window"></div>
	</div>
	
	<article class="system-message" id="system-message-template">
		<div class="center">
			<a href="#" class="system-message-close"><span class="icon-cancel"></span></a>
			<p><strong></strong> </p>
		</div>
	</article>
	
	
	<footer class="center"></footer>
	<?php
	// SwiftRiver Plugin Hook
	Swiftriver_Event::run('swiftriver.footer');
	?>
</body> 
</html>
<?php
// Uncomment to profile
// echo View::factory('profiler/stats');
?>