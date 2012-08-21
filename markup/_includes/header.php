<!DOCTYPE html> 
<html> 
 
<head> 
    <meta charset="utf-8"> 
	<title><?php print $page_title; ?> ~ SwiftRiver</title> 
	<link rel="shortcut icon" href="#">
	<link type="text/css" href="/themes/default/media/css/styles.css" rel="stylesheet" />
	<link type="text/css" href="/markup/css/touch.css" rel="stylesheet" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="/markup/js/jquery.outside.js"></script>
	<script type="text/javascript" src="/markup/js/global.js"></script>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
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
					<li class="rivers"><a href="/markup/modal-rivers.php" class="modal-trigger"><span class="icon-river"></span><span class="label">Rivers</span></a></li>
					<li class="bucket"><a href="/markup/modal-buckets.php" class="modal-trigger"><span class="icon-bucket"></span><span class="label">Buckets</span></a></li>
					<li class="user popover">
						<a href="#" class="popover-trigger">
							<span class="icon-arrow-down"></span>
							<span class="avatar-wrap"><span class="notification">3</span>
	<img src="/markup/images/content/avatar4.jpg" /></span>
							<span class="nodisplay">Brandon Rosage</span>
						</a>
						<ul class="popover-window base header-toolbar">
							<li><a href="/">Dashboard</a></li>
							<li class="group"><a href="/markup/user">Profile</a></li>
							<li><a href="/markup/user/settings.php">Account settings</a></li>
							<li><a href="/markup/settings.php">Website settings</a></li>
							<li><a href="#"><em>Log out</em></a></li>
						</ul>
					</li>
				</ul>
			</div>
		</div>
	</header>