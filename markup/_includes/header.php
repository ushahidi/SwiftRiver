<!DOCTYPE html> 
<html> 
 
<head> 
    <meta charset="utf-8"> 
	<title><?php print $page_title; ?> ~ SwiftRiver</title> 
	<link rel="shortcut icon" href="#">
	<link type="text/css" href="/markup/css/styles.css" rel="stylesheet" />
	<link type="text/css" href="/markup/css/touch.css" rel="stylesheet" />
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="/markup/js/jquery.outside.js"></script>
	<script type="text/javascript" src="/markup/js/global.js"></script>
	<meta name="viewport" content="width=device-width; initial-scale=1.0">
</head> 
 
<body> 
	<header class="toolbar">
		<div class="center">
			<h1 class="logo"><a href="/markup/"><span class="nodisplay">SwiftRiver</span></a></h1>
			<ul class="toolbar-menu">
				<li class="search"><a href="/markup/modal-search.php" class="modal-trigger"><span class="icon"></span><span class="label">Search</span></a></li>
				<li class="create"><a href="/markup/modal-create.php" class="modal-trigger"><span class="icon"></span><span class="label">Create</span></a></li>
				<li class="user popover">
					<a href="#" class="popover-trigger">
						<span class="dropdown-arrow"></span>
						<span class="avatar-wrap"><span class="notification">3</span>
<img src="/markup/images/content/avatar4.jpg" /></span>
						<span class="nodisplay">Brandon Rosage</span>
					</a>
					<ul class="popover-window base header-toolbar">
						<li><a href="#">Rivers</a></li>
						<li><a href="#">Buckets</a></li>
						<li class="group"><a href="/markup/user">Profile</a></li>
						<li><a href="/markup/user/settings.php">Account settings</a></li>
						<li><a href="/markup/settings.php">Website settings</a></li>
						<li><a href="#"><em>Log out</em></a></li>
					</ul>
				</li>
			</ul>
		</div>
	</header>