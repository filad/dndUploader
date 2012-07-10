<?php
session_start();
require_once('../external/mysql_connect.php');
require_once('../external/functions.php');

define('FILE_PATH','../uploaded_files/');

if (!isset($_GET['id'])) {
	header('Location: /404/?=a');
	exit;
}

if (preg_match('/[^0-9]/', $_GET['id'])) {
	header('Location: /404/?=b');
	exit;
}

if (file_exists(FILE_PATH . $_GET['id'])) {
	
	$fileId 		  = $_GET['id'];
	$row 			  = filkor_getFileRowFromMysql($fileId);
	
	$originalFileName = $row['original_filename'];
	$uploadDate	  	  = $row['upload_date'];
	
	//shorten fileName
	$originalFileName = strlen($originalFileName) <= 45 ? $originalFileName : substr($originalFileName, 0, 45).'...';
	$originalFileName = htmlentities($originalFileName, ENT_QUOTES);
	
	//if file is  uploaded more than one hour then -> 404
	if ($uploadDate < (time() - 3600) && !isset($_GET['p'])) {
		header('Location: /404/?reason=oldFile');
		exit;
	}
	
	$fileSize = filesize(FILE_PATH . $fileId) / (1024 * 1024);
	$fileSize = number_format($fileSize, 2, '.', ''); // format filesize
	
	//generate token
	$_SESSION['token'] = uniqid();

} else {
	header('Location: /404/?reason=noSuchFile');
	exit;
}
?>


<!doctype html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<meta http-equiv="Refresh" content='5;url="<?php echo '/direct/?id=' . $fileId . '&token=' . $_SESSION['token']; ?>' />
	<link rel="stylesheet" type="text/css" href="/static/main.css" media="screen"/>
	<link rel="shortcut icon" type="image/x-icon" href="/static/favicon.ico"/>

	<title><?php echo $originalFileName; ?> - filkor/dndUploader</title>
	<script type="text/javascript">
		var c = 6;
		window.onload = function() {
			count();
		}
		
		function count() { 
			c -= 1;
			//If the counter is within range we put the seconds remaining to the <span> below 
			if (c >= 0) 
				document.getElementById('countdown').innerHTML = c;
			else {
				return;
			}
			
			setTimeout('count()', 1000);
		}
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
<div id="root-container">
	<div id="header"><a href="/"><img width="380px" src="/static/header-0.png"></img></a></div>

	<div id="404" style="
	font-size:1.2em;
	text-align: center;
	letter-spacing: 0.05em;
	margin-top: 8em;
	padding: 25px 0;
	background-color: #222;
	border-radius: 10px;
	">
	<p>
		<span style="font-size: 1.3em;">Downloading... </span><span style="font-size: 1.3em;" id="countdown"></span>
	</p>
	<p><?php echo $originalFileName .' - '. $fileSize . ' MB';  ?></p>
	<p><span style="font-size: 0.8em;">Your download will begin soon. <a style="text-decoration:none;" href="/direct/?id=<?php echo $fileId; ?>&token=<?php echo $_SESSION['token'];?>">Click here</a> for the direct link.</span></p>
	</div>

</div>

<div id="footer">
	<ul>
		<li>by <a class="footer-links" href="http://filkor.org">filkor</a></li>
	</ul>
</div>
</body>

</html>
