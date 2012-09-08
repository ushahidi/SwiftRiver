<!DOCTYPE html> 
<html> 
 
<head> 
    <meta charset="utf-8"> 
	<title>SwiftRiver ~ Pattern portfolio</title> 
	<link rel="shortcut icon" href="#">
	<link type="text/css" href="/themes/default/media/css/styles.css" rel="stylesheet" />
	<style>
header {
   text-align: left;
   padding: 1.046666667%;
}

header h1 {
   color: #444444;
   font-size: 3em;
}

body > nav {
   text-align: left;
   padding: 0 1.046666667%;
}

body > nav li {
   font-size: 1.1em;
   list-style-type: disc;
   padding: 3px 0;
   margin-left: 20px;
}

.sample {
   padding: 20px 0;
}

.sample > h2 {
   color: #fff;
   text-align: left;
   background: #444444;
   padding: 1.046666667%;
   margin: 10px 0;
}

.sample > h2 em {
   color: #dedede;
   font-weight: normal;
   font-size: 0.8em;
}
	</style>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
	<script type="text/javascript" src="/markup/js/global.js"></script>
</head> 
 
<body> 

<header>
	<h1>SwiftRiver pattern portfolio</h1>
</header>
	
<nav>
	<ul>
		<li><a href="#buttons">Buttons</a></li>
		<li><a href="#page-title">Page title</a></li>
		<li><a href="#page-title-bucket">Bucket page title</a></li>
		<li><a href="#page-title-user">User page title</a></li>
		<li><a href="#page-navigation">Page navigation</a></li>
		<li><a href="#container">Container</a></li>
		<li><a href="#settings-toolbar">Generic toolbar</a></li>
		<li><a href="#save-toolbar">Save toolbar</a></li>
		<li><a href="#pagination">Pagination</a></li>
		<li><a href="#drops-drop">Drop: In 'drops' view</a></li>
		<li><a href="#list-drop">Drop: In 'list' view</a></li>
		<li><a href="#photos-drop">Drop: In 'photos' view</a></li>
		<li><a href="#generic-message">Generic message</a></li>
		<li><a href="#alert-message">Alert message</a></li>
	</ul>
</nav>

<div class="sample">
	<a name="buttons"></a>
	<h2>Buttons: <em>Various styles for different functions</em></h2>
	<div class="col_2">
		<p class="button-blue"><a href="#">Blue button</a></p>
	</div>
	<div class="col_2">
		<p class="button-blue button-small"><a href="#"><span class="icon-plus"></span>Blue, small, icon</a></p>
	</div>
	<div class="col_2">
		<p class="button-blue button-big"><a href="#">Blue, big</a></p>
	</div>
	<div class="col_2">
		<p class="button-white"><a href="#">White button</a></p>
	</div>
	<div class="col_2">
		<p class="button-white"><a href="#"><span class="icon-plus"></span>White, icon</a></p>
	</div>
	<div class="col_2">
		<p class="button-white follow"><a href="#" title="now following"><span class="icon-checkmark"></span><span class="nodisplay">Follow</span></a></p>
	</div>
</div>

<div class="sample">
	<a name="page-title"></a>
	<h2>Page title: <em>Used on rivers and modal windows</em></h2>
	<hgroup class="page-title cf">
		<div class="center">
			<div class="page-h1 col_9">
				<h1>Page title</h1>
			</div>
			<div class="page-action col_3">
				<span class="button-blue"><a href="#"><i class="icon-settings"></i>Page action</a></span>
			</div>
		</div>
	</hgroup>
</div>

<div class="sample">
	<a name="page-title-bucket"></a>
	<h2>Bucket page title: <em>Used for bucket views; Displays two page actions</em></h2>
	<hgroup class="page-title bucket-title cf">
		<div class="center">
			<div class="page-h1 col_8">
				<h1>Page title</h1>
			</div>
			<div class="page-action col_4">
				<span>
				<ul class="dual-buttons">
					<li class="button-blue"><a href="/markup/bucket/discussion.php"><i class="icon-comment"></i>Discussion</a></li>
					<li class="button-blue"><a href="/markup/bucket/settings-collaborators.php"><i class="icon-settings"></i>Settings</a></li>
				</ul>
				</span>
			</div>
		</div>
	</hgroup>
</div>

<div class="sample">
	<a name="page-title-user"></a>
	<h2>User page title: <em>Used for user views; Displays following information</em></h2>
	<hgroup class="user-title cf">
		<div class="center">
			<div class="user-summary col_9">		
				<a class="avatar-wrap"><img src="/markup/images/content/avatar5.jpg" class="avatar" /></a>
				<h1>Full name</h1>
				<h2>username</h2>
			</div>
			<div class="follow-summary col_3">
				<p class="follow-count"><a href="#"><strong>28</strong> followers</a>, <a href="#"><strong>18</strong> following</a></p>
				<p class="button-score button-white follow"><a href="#" title="now following"><span class="icon-checkmark"></span>Follow</a></p>
			</div>
		</div>
	</hgroup>
</div>

<div class="sample">
	<a name="page-navigation"></a>
	<h2>Page navigation: <em>Used for pages with alternate views or child pages</em></h2>
	<nav class="page-navigation cf">
		<div class="center">
			<div class="river touchcarousel col_12">
				<ul class="touchcarousel-container">
					<li class="active"><a href="/markup/river">Drops</a></li>
					<li><a href="/markup/river/view-list.php">List</a></li>
					<li><a href="/markup/river/view-photos.php">Photos</a></li>
					<li><a href="/markup/river/view-map.php">Map</a></li>
					<li><a href="/markup/river/view-timeline.php">Timeline</a></li>
				</ul>
			</div>
		</div>
	</nav>
</div>

<div class="sample settings">
	<a name="container"></a>
	<h2>Container: <em>Used for settings and dashboard</em></h2>
	<div class="col_12">
		<article class="container base">
			<header class="cf">
				<div class="property-title col_12">
					<h1>Name</h1>
				</div>
			</header>
			<section class="property-parameters cf">
				<div class="parameter">
					<div class="field">
						<p class="field-label">Display name</p>
						<input type="text" value="Ushahidi at SXSW" />
					</div>
				</div>
				<div class="parameter">
					<div class="field">
						<p class="field-label">URL</p>
						<input type="text" value="ushahidi-at-sxsw" name="river_url" />
					</div>
				</div>
				<div class="save-toolbar col_12">
					<p class="button-blue"><a href="#">Save changes</a></p>
					<p class="button-blank cancel"><a href="#">Cancel</a></p>
				</div>
			</section>
		</article>
	</div>
</div>

<div id="content" class="sample settings">
	<a name="settings-toolbar"></a>
	<h2>Generic toolbar: <em>Used at the top of settings</em></h2>
	<div class="col_12">
		<div class="button-actions">
			<span><a href="/markup/modal-collaborators.php" class="modal-trigger"><i class="icon-users"></i>Add collaborator</a></span>
		</div>
	</div>
</div>

<div id="content" class="sample settings">
	<a name="save-toolbar"></a>
	<h2>Save toolbar: <em>Used at the bottom of settings</em></h2>
	<div class="col_12">
		<form>
			<input type="text" placeholder="Change my value to see buttons" style="width:100%;margin-bottom:10px;" />
			<div class="save-toolbar">
				<p class="button-blue"><a href="#">Save changes</a></p>
				<p class="button-blank"><a href="#">Cancel</a></p>
			</div>
		</form>
	</div>
</div>

<div class="sample">
	<a name="pagination"></a>
	<h2>Pagination: <em>Used at the bottom of a list of articles</em></h2>
	<div class="pagination col_12 cf">
		<p class="button-blue"><a href="#"><span class="icon-arrow-left"></span><span class="label">Previous</span></a></p>
		<ul>
			<li class="current"><a href="#">1</a></li>
			<li><a href="#">2</a></li>
			<li><a href="#">3</a></li>
			<li><a href="#">4</a></li>
		</ul>
		<p class="button-blue"><a href="#"><span class="label">Next</span><span class="icon-arrow-right"></span></a></p>
	</div>
</div>

<div class="sample drops cf">
	<a name="drops-drop"></a>
	<h2>Drop: <em>As seen in 'drops' view</em></h2>
	<div class="col_3">
		<article class="drop base">
			<a href="/markup/drop" class="drop-image-wrap zoom-trigger"><img src="/markup/images/content/drop-image.png" class="drop-image" /></a>
			<h1><a href="/markup/drop/" class="zoom-trigger">The Europe Roundup: Cybercrime in the UK, Ushahidi in Serbia, Big Data in Norway</a></h1>
			<div class="drop-actions cf">
				<ul class="dual-buttons score-drop">
					<li class="button-white like"><a href="#"><span class="icon-thumbs-up"></span></a></li>
					<li class="button-white dislike"><a href="#"><span class="icon-thumbs-down"></span></a></li>
				</ul>
				<ul class="dual-buttons move-drop">
					<li class="button-blue share"><a href="/markup/modal-share.php" class="modal-trigger"><span class="icon-share"></span></a></li>
					<li class="button-blue bucket"><a href="/markup/modal-bucket.php" class="modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
				</ul>
			</div>
			<section class="drop-source cf">
				<a href="#" class="avatar-wrap"><img src="/markup/images/content/avatar2.png" /></a>
				<div class="byline">
					<h2>The Global Journal</h2>
					<p class="drop-source-channel"><a href="#"><span class="icon-rss"></span>via RSS</a></p>
				</div>
			</section>
		</article>
	</div>
</div>

<div class="sample list">
	<a name="list-drop"></a>
	<h2>Drop: <em>As seen in 'list' view</em></h2>
	<div class="col_12">
		<article class="drop base cf">
			<div class="drop-content">
				<div class="drop-body">
					<a href="/markup/drop" class="drop-image-wrap zoom-trigger"><img src="/markup/images/content/drop-image.png" class="drop-image" /></a>
					<h1><a href="/markup/drop/" class="zoom-trigger">Saluting @chiefkariuki and what he's doing for Lanet Umoja Location via Twitter. You restore hope in our leadership sir! cc @ushahidi</a></h1>
					<p class="metadata discussion">4:30 p.m. Jan. 13, 2012 <a href="#"><span class="icon-comments"></span><strong>3</strong> comments</a></p>
				</div>
				<section class="drop-source cf">
					<a href="#" class="avatar-wrap"><img src="/markup/images/content/avatar1.png" /></a>
					<div class="byline">
						<h2>Nanjira Sambuli</h2>
						<p class="drop-source-channel"><a href="#"><span class="icon-twitter"></span>via Twitter</a></p>
					</div>
				</section>
			</div>
			<div class="drop-actions stacked cf">
				<ul class="dual-buttons move-drop">
					<li class="button-blue share"><a href="/markup/modal-share.php" class="modal-trigger"><span class="icon-share"></span></a></li>
					<li class="button-blue bucket"><a href="/markup/modal-bucket.php" class="modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
				</ul>
				<ul class="dual-buttons score-drop">
					<li class="button-white like"><a href="#"><span class="icon-thumbs-up"></span></a></li>
					<li class="button-white dislike"><a href="#"><span class="icon-thumbs-down"></span></a></li>
				</ul>
			</div>
		</article>
	</div>
</div>

<div class="sample photos cf">
	<a name="photos-drop"></a>
	<h2>Drop: <em>As seen in 'photos' view</em></h2>
	<div class="col_3">
		<article class="drop base cf">
			<div class="drop-content">
				<div class="drop-body">
					<a href="#" class="drop-image-wrap"><img src="/markup/images/content/drop-photo1.jpg" class="drop-image" /></a>
					<div class="drop-actions cf">
						<ul class="dual-buttons move-drop">
							<li class="button-blue share"><a href="/markup/modal-share.php" class="modal-trigger"><span class="icon-share"></span></a></li>
							<li class="button-blue bucket"><a href="/markup/modal-bucket.php" class="modal-trigger"><span class="icon-add-to-bucket"></span></a></li>
						</ul>
						<ul class="dual-buttons score-drop">
							<li class="button-white like"><a href="#"><span class="icon-thumbs-up"></span></a></li>
							<li class="button-white dislike"><a href="#"><span class="icon-thumbs-down"></span></a></li>
						</ul>
					</div>
				</div>
				<section class="drop-source cf">
					<a href="#" class="avatar-wrap"><img src="/markup/images/content/avatar1.png" /></a>
					<div class="byline">
						<h2>Nanjira Sambuli</h2>
						<p class="drop-source-channel"><a href="#"><span class="icon-twitter"></span>via Twitter</a></p>
					</div>
				</section>
			</div>
		</article>
	</div>
</div>

<div id="content" class="sample settings">
	<a name="generic-message"></a>
	<h2>Generic message: <em>Used when no content exists</em></h2>
	<div class="alert-message blue">
		<p><strong>No more channels.</strong> You can flow new channels into your river by selecting the "Add channel" button above.</p>
	</div>
</div>

<div id="content" class="sample settings">
	<a name="alert-message"></a>
	<h2>Red message: <em>Used for errors and alerts</em></h2>
	<div class="alert-message red">
		<p><strong>Uh oh.</strong> Look out. Bad stuff happened.</p>
	</div>
</div>
</body>
</html>