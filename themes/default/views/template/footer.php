	<div id="zoom-container">
		<div class="modal-window"></div>
	</div>

	<div id="modal-container">
		<div class="modal-window"></div>
	</div>

	<div id="confirmation-container">
		<div class="modal-window"></div>
	</div>

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