<!doctype html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="/static/main.css" media="screen"/>
	<link rel="stylesheet" type="text/css" href="/static/button.css" media="screen"/>
	<link rel="stylesheet" type="text/css" href="/static/jquery-ui-1.8.19.custom.css" media="screen"/>
	<link rel="shortcut icon" type="image/x-icon" href="/static/favicon.ico"/>
	<title>filkor/dndUploader</title>
	
	
	<!-- private from here --> 
	
	<!-- Open Graph meta tags for sharing (Google Plus, Facebook) -->
	<meta property="og:title" content="'Pause and resume' uploader - demo"/>
	<meta property="og:type" content="website"/>
	<meta property="og:url" content="http://dnduploader.filkor.org/"/>
	<meta property="og:image" content="http://dnduploader.filkor.org/static/dnd-icon.png"/>
	<meta property="og:site_name" content="filkor/dndUploader"/>
	<meta property="og:description" content="Resumable uploads with only Javascript and PHP."/>	
	
	<!-- Gooogle +1 -->
	
	<!-- Update your html tag to include the itemscope and itemtype attributes -->
	<html itemscope itemtype="http://schema.org/experiment">

	<!-- Add the following three tags inside head -->
	<meta itemprop="name" content="filkor/dndUploader - demo">
	<meta itemprop="description" content="Resumable uploads with only Javascript and PHP">
	<meta itemprop="image" content="http://dnduploader.filkor.org/static/dnd-icon.png">	
	
	<script type="text/javascript">
	  (function() {
		var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
		po.src = 'https://apis.google.com/js/plusone.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
	  })();
	</script>
	
	<!-- Google Analytics -->
	<script type="text/javascript">

	  var _gaq = _gaq || [];
	  _gaq.push(['_setAccount', 'UA-31462044-2']);
	  _gaq.push(['_trackPageview']);

	  (function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	  })();

	</script>
</head>

<body id="body">

	<div id="sharing">
		<ul>
			<li class="first"><g:plusone size="medium" annotation="none"></g:plusone></li>
			<li><a href="https://twitter.com/share" class="twitter-share-button" data-text="Resumable uploads with only Javascript on client side." data-count="none">Tweet</a>
				<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>				</li>
		</ul>
		<div class="clearFix"></div>
	</div>

<div id="root-container">
	<div id="header"><a href="/"><img width="380px" src="/static/header-0.png"></img></a></div>
		
		<div id="browser-warning">
			It seems your browser doesn't meet the minimum requirements. So you can't use this demo site. 
			<br>Try to update your browser if possible. I suggest <a href="http://www.google.com/intl/en/chrome/">Chrome</a> or <a href="http://www.mozilla.org/en-US/firefox/new/">Firefox</a> :)
		</div>

		<!--dierct upload input ("fakeinput")-->
		<input type="file" id="filepicker-input" multiple="true"/>  	
		
		<div id="welcome">
		Drag and Drop your files or <span id="direct-upload-text">click here to upload directly.</span> <small id="more-info-link"><u>More info..</u></small>
		</div>

		<div id="more-info">
			<ul>
				<li>You can upload by drag and drop files from your folders, or click above to upload in the usual way. Multiple files are allowed.</li>
				<li>You can pause the uploads (you can even close your browser, or turn off your computer.. you don't have to restart again from the beginning). </li>
				<li>The maximum file sizes could be hundreds of MB-s.</li>
				<li>This is just a demonstration, the files will be deleted after 1 hour.</li>
				<li>For more information, see the <a href="/FAQ/">FAQ</a>.
			</ul>
		</div>
		
		<img id="dropbox" src="/static/dropbox.png" alt=""/>
		

		<div id="preview">
			<ul id="dropped-files">			
			</ul>
			
			<!--see button.css-->
			<center><a id="uploadbutton" class="large button green">Start Upload</a><center>
			<img id="ajax-loader" src="/static/ajax-loader.gif" alt=""/>
			
		</div>
</div>

<div id="footer">
	<ul>
		<li>by <a class="footer-links" href="http://filkor.org">filkor</a></li>
	</ul>

</div>
<div id="drop-box-overlay">
	<h1>Drop files anywhere to upload...</h1>
</div>


<script src="static/jquery-1.7.2.min.js"></script>
<script src="static/jquery-ui-1.8.19.custom.min.js"></script>

<script src="static/jsUpload.js"></script>
<script src="static/main.js"></script>
</body>

</html>
