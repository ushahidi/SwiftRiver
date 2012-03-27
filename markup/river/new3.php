<?php
	$page_title = "Create a river";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="page-title cf">
		<div class="center">
			<h1>Create a river</h1>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li><a href="/markup/river/new.php">1. Name your river</a></li>
			<li><a href="/markup/river/new2.php">2. Open channels</a></li>
			<li class="active"><a href="/markup/river/new3.php">3. View your river</a></li>
		</ul>
	</nav>

	<div id="content" class="settings channels cf">
		<div class="center">
			<div class="col_12">
				<div class="alert-message blue">
					<p><strong>Your river will be full shortly.</strong> SwiftRiver is processing the drops in your river. You can view them in just a moment. 
					<br />In the meantime, you can <a href="/markup/river">jump to your river's web page</a> or <a href="/">explore today's popular rivers</a>.</p>
				</div>
			</div>
		</div>
	</div>

<div id="modal-container">
	<div class="modal-window"></div>
</div>

</body>
</html>