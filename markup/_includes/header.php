<!DOCTYPE html> 
<html> 
 
<head> 
    <meta charset="utf-8"> 
	<title><?php print $page_title; ?> ~ SwiftRiver</title> 
	<link rel="shortcut icon" href="#">
	<link type="text/css" href="/markup/_css/styles.css" rel="stylesheet" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="/markup/_js/jquery.outside.js"></script>
	<script type="text/javascript" src="/markup/_js/global-ck.js"></script>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head> 
 
<body> 
	<header class="toolbar">
		<div class="center">
			<div class="col_4">
				<h1 class="logo"><a href="/markup/"><span class="nodisplay">SwiftRiver</span></a></h1>
				<ul class="global-menu">
					<li class="home"><a href="/"><span class="icon-home"></span></a></li>
					<li class="search"><a href="/markup/modal-search.php" class="modal-trigger"><span class="icon-search"></span></a></li>
				</ul>
			</div>

			<div class="col_8">
				<ul class="user-menu">
					<li class="rivers"><a href="/markup/_modals/rivers.php" class="modal-trigger"><span class="icon-river"></span><span class="label">Rivers</span></a></li>
					<li class="bucket"><a href="/markup/_modals/buckets.php" class="modal-trigger"><span class="icon-bucket"></span><span class="label">Buckets</span></a></li>
					<!-- IF: User logged in -->
					<li class="user popover">
						<a href="#" class="popover-trigger">
							<span class="icon-arrow-down"></span>
							<span class="avatar-wrap"><span class="notification">3</span>
							<img src="http://www.ushahidi.com/uploads/people/team_Emmanuel-Kala.jpg" /></span>
							<span class="nodisplay">Emmanuel Kala</span>
						</a>
						<ul class="popover-window base header-toolbar">
							<li><a href="/markup">Your activity</a></li>
							<li class="group"><a href="/markup/home/settings.php">Account settings</a></li>
							<li><a href="/markup/settings.php">Website settings</a></li>
							<li><a href="#"><em>Log out</em></a></li>
						</ul>
					</li>
					<!-- ELSE
					<li class="login"><a href="/markup/login.php" class="modal-trigger"><span class="icon-login"></span><span class="label">Log in</span></a></li>
					END:IF -->
				</ul>
			</div>
		</div>		
	</header>