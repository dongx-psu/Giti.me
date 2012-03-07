<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title><?php echo SITE_TITLE; ?></title>
	
	<link href="./themes/default/css/gitme.css" media="screen" rel="stylesheet" type="text/css">
	
	<script src="./themes/default/js/jquery-1.7.1.min.js" type="text/javascript"></script>
	<script src="./themes/default/js/jquery.bpopup-0.6.0.min.js" type="text/javascript"></script>
	<script src="./themes/default/js/base64-encode.js" type="text/javascript"></script>
	<script src="./themes/default/js/git.js" type="text/javascript"></script>
</head>
<body>
	<div class="site clearfix">
		<div class="container">
			<div id="header"></div>
			<div class="pjax-loading hidden-first">
				<img src = "./themes/default/img/spinner.gif" alt="Loading">
			</div>
			<div class="page-settings">
				<div id="edit_box" class="add-form show"></div>
				<div class="hint-box">
					<div class="messages" id="warning-text">
						Hint box.
						<span class="close">close</span>
					</div>
					<div class="messages error-messages" id="error-text">
						Errors.
						<span class="close">close</span>
					</div>	
				</div>
				<div class="sidebar" id="settings=nav">
					<ul class="accordion">
						<li class="section">
							<a class="section-head" href="#">Username</a>
							<ul class="expanded section-nav">
								<li class="active"><a href="#">SSH</a></li>
								<li class=""><a href="./login/logout.php">Logout</a></li>
							</ul>
						</li>
					</ul>
				</div>
				<div class="settings-content">
					<div class="setting-box">
						<h3>SSH Keys</h3>
						
						<div class="setting-box-inner">
							<ul class="setting-box-list standalone"></ul>
						</div>
					</div>
					<div id="add-form" class="add-form">
						<dl class="form">
							<dt><label>Title</label></dt>
							<dd>
								<input name="ssh_title" id="ssh_title" type="text" size="30"/>			
							</dd>
						</dl>
						<dl class="form">
							<dt><label>Key</label></dt>
							<dd>
								<textarea name="ssh_key" id="ssh_key" rows="20" cols="40"></textarea>
							</dd>
						</dl>
						<div class="form-actions">
							<button class="minibutton" type="submit"><span>Add Key</span></button>
							<span>or</span>
							<a id="cancel_add_key" href="#"><span>Cancel</span></a>
						</div>
					</div>
					<p>
						<a id="add_key_action" class="addlink button classy" href="#"><span>Add New SSH Key</span></a>
					</p>
				</div>
			</div>
		</div>
	</div>
</body>
</html>

<!-- The page is design by yinger650(yinger650@gmail.com) @ 2012/2/29-->