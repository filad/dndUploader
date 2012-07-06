<!DOCTYPE html>
<html lang="en-US">
<head>
	<meta charset="UTF-8">
	<link rel="stylesheet" type="text/css" href="/static/main.css" media="screen"/>
	<link rel="shortcut icon" type="image/x-icon" href="/static/favicon.ico"/>
	<title>FAQ - filkor/dndUploader</title>

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

	
	<style>
		#FAQ{
			width:400px; 
			margin: 0 auto;
			margin-top: 5em;
			margin-bottom: 200px;
			padding: 10px 20px 15px 20px;
			background-color: #222;
			border-radius: 10px;
		}
		
		#FAQ .title {
			font-size: 1.2em; 
			margin:0,auto; 
			text-align:center; 
			text-shadow: 1px 1px 15px #888;
		}
		#FAQ ul li{
			padding-top: 20px;
		}
	</style>
	
</head>

<body id="body">
<div id="root-container">
	<div id="header"><a href="/"><img width="380px" src="/static/header-0.png"></img></a></div>
	
	<div id="FAQ">
        <p class="title">dndUploader FAQ</p>
        <p>If you require any more information -> adam.filkor at gmail </p>
        <ul>
            <li>
			<u>What is 'dnd' ?</u>
			<p>You have the option to upload by drag & drop your files, too. Thats where this name comes from.</p>
			</li>
			
			<li>
				<u>Why is this website so special ?</u>
				<p>
					You can pause the upload process, even turn off your computer, and only 
					Javasript was used on client side, no Flash required.</p> 
				<p>
					This is very handy on large uploads when you suddenly lose your internet connection...or 
					just don't have time to wait until the upload ends.
				</p>
				<p>
				You can upload multiple files at once.
				</p>
			</li>
			<li>
				<u>What's the idea ?</u>
				<p>
					The idea is to slice up the files with the Javascript Blob object. This object has a slice() method. 
					<br>
					So you can send the file as little packets. They will be merged by the server at the end of the upload.
				</p>
				<p>
					..as you can see, the new Javascript File API is a very handy tool, I like it! 
					
				</p>
				<p>
					The other technology the site is using is the Javascript localStorage. To keep the files after the browser closes,
					we need some place to save the filedetails... so if you come back you don't have to restart the whole uploading process again!
					If you drop the same files when you come back, the server will know that you want to continue your uploads. 
				</p>
			</li>
			<li>
				<u>Source code ?</u>
				<p>
				Of course:)<br>
				You will find it on <a target="_blank" href="http://github.com/filad/dndUploader">github.com/filad/dndUploader</a><br>
				Enjoy.
				</p>
			</li>
			<li>
				<u>So, this site is just a demo?</u>
				<p>Yes, your files will be deleted after 1 hour you uploaded.</p>
			</li>
			<li>
				<u>Any other things ?</u>
				<p>Oh of course! </p>
				<p>Plz don't use IE :)</p>
				<p>And an important thing is: I have to mention Niklas von Hertzen here. 
				I read this 'slice the files' idea first on his website. 
				I learned a lots of things from his source codes. Please visit <a href="http://hertzen.com/">his website</a>, and his interesting experiments, too.
				</p>
			</li>
        </ul>
         
        
	</div>
	
	
</div>

<div id="footer">
	<ul>
		<li>by <a class="footer-links" href="http://filkor.org">filkor</a></li>
	</ul>

</div>
</body>

</html>
