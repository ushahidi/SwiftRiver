<?php
	$page_title = "Nat Manning";
	$template_type = "user";
	include $_SERVER['DOCUMENT_ROOT'].'/markup/_includes/header.php';
?>

	<hgroup class="user-title cf">
		<div class="center">
			<div class="user-summary col_9">		
				<a class="avatar-wrap"><img src="/markup/images/content/avatar4.jpg" class="avatar" /></a>
				<h1>Brandon Rosage</h1>
				<h2>brosage</h2>
			</div>
		</div>
	</hgroup>

	<nav class="page-navigation cf">
		<ul class="center">
			<li><a href="/">Dashboard</a></li>
			<li class="active"><a href="/user/settings.php">Account settings</a></li>
		</ul>
	</nav>

	<div id="content" class="settings cf">
		<div class="center">
			<div class="col_12">
				<article class="container base">
					<header class="cf">
						<div class="property-title">
							<h1>About you</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="name_nickname">
								<p class="field">Full name</p>
								<input type="text" name="name_fulllname" placeholder="Enter full name" />
							</label>
						</div>
						<div class="parameter">
							<label for="name_nickname">
								<p class="field">Username</p>
								<input type="text" name="name_username" placeholder="Enter username" />
							</label>
						</div>
						<div class="parameter">
							<label for="email">
								<p class="field">Email address</p>
								<input type="email" name="email" placeholder="Enter your email address" />
							</label>
						</div>
					</section>
				</article>
	
				<article class="container base">
					<header class="cf">
						<div class="property-title">
							<h1>Password</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter">
							<label for="password">
								<p class="field">Password</p>
								<input type="password" name="password" />
							</label>
						</div>
						<div class="parameter cf">
							<label for="password">
								<p class="field">Confirm password</p>
								<input type="password" name="password" />
							</label>
						</div>
					</section>
				</article>

				<article class="container base">
					<header class="cf">
						<div class="property-title">
							<h1>Photo</h1>
						</div>
					</header>
					<section class="property-parameters">
						<div class="parameter cf">
							<a class="avatar-wrap"><img src="/markup/images/content/avatar4.jpg" class="avatar" /></a>
							<p class="button-blue button-small"><a href="http://gravatar.com">Use a different photo</a></p>
						</div>
					</section>
				</article>

				<div class="save-toolbar">
					<p class="button-blue"><a href="#">Save changes</a></p>
					<p class="button-blank"><a href="#">Cancel</a></p>
				</div>
			</div>
		</div>
	</div>

<div id="modal-container">
	<div class="modal-window"></div>
	<div class="modal-fade"></div>
</div>

</body>
</html>