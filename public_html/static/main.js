/**
 * @author Adam Filkor <adam.filkor at gmail.com>
 * @created 08.05.2012 
 * @website http://filkor.org
 */

$(document).ready(function() {	
	
	initMoreInfo();
	
    if (isBrowserBad() !== true) {
		initDirectUpload();
		initDnD();
	}
});

function isBrowserBad() {
	//basic check for required features
	if (!("addEventListener" in window) || !("FileReader" in window) || !("Blob" in window) || !("FormData" in window)) {
		$("#browser-warning").fadeIn(125);
		return true;
	}
	return false;
}


//init the little information table (slideToggle)
function initMoreInfo(){
	$("#more-info-link").click(function(){
		$("#more-info").slideToggle("fast");
	});
	
}

function initDirectUpload() {
	var fileInput = document.getElementById("filepicker-input");
	document.getElementById("direct-upload-text").onclick = function(e){
		fileInput.click();
	}
	
	fileInput.onchange = function(e) {
	//basically same as in ondrop
		$("#dropped-files").html("");
		
		var files = e.target.files;
		
		createPreviewElements(files);
		
		//show the 'start upload' button
		var uploadButton = document.getElementById('uploadbutton');
		uploadButton.style.display = 'inline-block';
		
		//add an onclick property to the upload button, this will trigger the main upload process
		uploadButton.onclick = function(e){
			uploadButton.onclick = null; //disable the onclick event once it happened
			document.getElementById('ajax-loader').style.display = 'inline';
			setTimeout(function(){$('#ajax-loader').fadeOut()}, 2000);  //fade out loader after 2 sec
			startupload(files);
		};
	} 
}

//init Drag and Drop
function initDnD() {
	// Add drag handling to target elements
	document.getElementById("body").addEventListener("dragenter", onDragEnter, false);
	document.getElementById("drop-box-overlay").addEventListener("dragleave", onDragLeave, false);
	document.getElementById("drop-box-overlay").addEventListener("dragover", noopHandler, false);
	
	// Add drop handling
	document.getElementById("drop-box-overlay").addEventListener("drop", onDrop, false);
}

function noopHandler(e) {
	e.stopPropagation();
	e.preventDefault();
}

function onDragEnter(e) {
	$("#drop-box-overlay").fadeIn(125);
	$("#drop-box-prompt").fadeIn(125);
}

function onDragLeave(e) {
	/*
	 * We have to double-check the 'leave' event state because this event stupidly
	 * gets fired by JavaScript when you mouse over the child of a parent element;
	 * instead of firing a subsequent enter event for the child, JavaScript first
	 * fires a LEAVE event for the parent then an ENTER event for the child even
	 * though the mouse is still technically inside the parent bounds. If we trust
	 * the dragenter/dragleave events as-delivered, it leads to "flickering" when
	 * a child element (drop prompt) is hovered over as it becomes invisible,
	 * then visible then invisible again as that continually triggers the enter/leave
	 * events back to back. Instead, we use a 10px buffer around the window frame
	 * to capture the mouse leaving the window manually instead. (using 1px didn't
	 * work as the mouse can skip out of the window before hitting 1px with high
	 * enough acceleration).
	 */
	if(e.pageX < 10 || e.pageY < 10 || $(window).width() - e.pageX < 10  || $(window).height - e.pageY < 10) {
		$("#drop-box-overlay").fadeOut(125);
		$("#drop-box-prompt").fadeOut(125);
	}
}

function onDrop(e) {
	// Consume the event.
	noopHandler(e);
	
	// Hide overlay
	$("#drop-box-overlay").fadeOut(0);
	//$("#drop-box-prompt").fadeOut(0);
	
	// Empty logs and preview and reset sizes
	$("#dropped-files").html("");
	
	// Get the dropped files.
	var files = e.dataTransfer.files;
	
	// If anything is wrong with the dropped files, exit.
	if(typeof files == "undefined" || files.length == 0)
		return;
	
	
	createPreviewElements(files);
	
	//show the 'start upload' button
	var uploadButton = document.getElementById('uploadbutton');
	uploadButton.style.display = 'inline-block';
	
	//add an onclick property to the upload button, this will trigger the main upload process
	uploadButton.onclick = function(e){
		uploadButton.onclick = null; //disable the onclick event once it happened
		document.getElementById('ajax-loader').style.display = 'inline';
		setTimeout(function(){$('#ajax-loader').fadeOut()}, 2000);  //fade out loader after 2 sec
		startupload(files);
	};
}


/*
The following function will generate this <li> item:
<li id="file-item-0">
	<span class="filename"></span>
	<div id="pausebutton-0" class="pauseButton small button green">Pause</div>
	<div id="progressbar-0" class="progressbar"></div>
	<div id="log-link-0" class="log-link">Open log v</div>
	<div id="log-0" class="log">#Log...<div>
</li>
*/
function createPreviewElements(files){
	this.files = files;
	
	for(var i = 0; i < this.files.length; i++) {
		
		this.fileName = this.files[i].name;
		
		//shorten long filenames
		if (this.fileName.length > 45)
			this.fileName = this.fileName.substr(0, 45) + '...';
		
		this.fileName = htmlEscape(this.fileName);
		var droppedFiles = document.getElementById('dropped-files');
		
		//create <li> item
		var item = document.createElement('li');
		item.id  = 'file-item-' + i;
		droppedFiles.appendChild(item);
		
		//create "filename"
		var filename 	   = document.createElement('span');
		filename.className = 'filename';
		filename.innerHTML = this.fileName;
		item.appendChild(filename);
		
		//create space for download link
		var downloadLink 	   = document.createElement('a');
		downloadLink.id 	   = 'downloadLink-' + i;
		downloadLink.className = 'downloadLink';
		downloadLink.target    = '_blank';
		item.appendChild(downloadLink);
		
		//add pause button
		var pause 		= document.createElement('div');
		pause.id		= 'pausebutton-' + i;
		pause.className = 'pauseButton small button green';
		pause.innerHTML = 'Pause';
			//custom property
			pause.uploadState = 'uploading';
		item.appendChild(pause);
		
		//create progressbar
		var progress 	   = document.createElement('div');
		progress.id 	   = 'progressbar-' + i;
		progress.className = 'progressbar';
		item.appendChild(progress);
		$("#progressbar-" + i).progressbar({ value: 0.01 }); //initalize the jquery progressbar 
	
		//create the "open log" link
		var loglink 	  = document.createElement('div');
		loglink.id  	  = 'log-link-' + i;
		loglink.className = 'log-link';
		loglink.innerHTML = 'Open log >';
		item.appendChild(loglink);
		
		//create the logger element
		var log 	  = document.createElement('div');
		log.id 		  = 'log-' + i;
		log.className = 'log';
		log.style.display = 'none';
		log.innerHTML = '#Log...<br>';
		item.appendChild(log);
		
		
		//-add event listener to to onclick to show the log
			(function(i, loglink){
				loglink.onclick = function(){
					$('#log-' + i ).slideToggle('fast');
					
					if(loglink.innerHTML == 'Close log v') {
						loglink.innerHTML = 'Open log >';
						
					} else {
						loglink.style.display = 'block';
						loglink.innerHTML = 'Close log v';	
					}
				};
			})(i, loglink);
		
		//Update the preview of resumed uploads, passing the elements we want to change, like progressbar, pausebutton..
		updateResumedItems(this.files[i], progress, pause, downloadLink);
		
	//end for loop
	}
}

function startupload(files){
	for(var i = 0; i<files.length; i++) {
		(function(i){
		new jsUpload(
		{
			file: files[i],
			logger: function(message){
				document.getElementById("log-" + i).innerHTML = document.getElementById("log-" + i).innerHTML + message + "<br />";
			},
			
			progressHandler: function(percent, serverFileId){
				//$("#progressbar-" + i).progressbar({ value: percent }); //default progressbar animation
				
				//some sugar to animate progressbar
				$('#progressbar-' + i +' .ui-progressbar-value').addClass('ui-corner-right').stop(true).animate({
					width: percent + '%'
					
					}, 600, function(){
						//add green succeed tick
						//console.log('fileid' + serverFileId);
						//console.log('percent' + percent);
						if(percent == 100) {
							//replace pause button with a green succed image
							var pauseButton = document.getElementById('pausebutton-' + i); 
							var succeedImg 	= document.createElement('img');
							succeedImg.src  = 'static/succeed-tick.png';
							succeedImg.style.cssFloat = 'right';
							succeedImg.width = 40;
							
							pauseButton.parentNode.replaceChild(succeedImg, pauseButton);
							
							//show download link, we use the serverFileId parameter here 
							var downloadLink 	   = document.getElementById('downloadLink-' + i);
							downloadLink.innerHTML = 'Download link';
							downloadLink.href 	   = 'http://dnduploader.filkor.org/d/?id=' + serverFileId;
						}	
					});				
			},
			
			//pass the reference to pauseButton element 
			pauseButton: document.getElementById('pausebutton-' + i)
		});
		})(i);
	}
}

/**
 * Update the preview of resumed uploads, like fix initial progressbar value or show success tick when the upload is done before
 *
 */
function updateResumedItems(file, progressElement, pauseButton, downloadLink) {
	
	var fileName  = file.name;
	var type   	  = file.type;
	var totalSize = file.size;
	
	var fileId = fileName +'|'+ type + '|' + totalSize;
	
	
	//check if it already exists in localStorage, so whether to resume uploading
	var fileData = localStorage[fileId];
	
	if (fileData) {
		var fileParts 	   = fileData.split('|');
		
		//get the timeStamp when uploaded, if older the 1 hour then delete 
		var timeUploaded   = fileParts[3]; //could be undefined
		var currentTime    = Math.round(new Date().getTime() / 1000);

		if ('undefined' != typeof timeUploaded && (currentTime - 3600) > timeUploaded) {
			localStorage.removeItem(fileId);
			return;
		}
		
		
		var currentPackage = fileParts[2]; // the third element in the array is the currentPackage number
		
		//if its already uploaded then show success image and set progressbar to 100%
		if (currentPackage == 'alldone') {
			var progressPercent = 100;
			
			//set success tick instead of pause button			
			var succeedImg 	= document.createElement('img');
			succeedImg.src  = 'static/succeed-tick.png';
			succeedImg.style.cssFloat = 'right';
			succeedImg.width = 40;
			
			pauseButton.parentNode.replaceChild(succeedImg, pauseButton);
			
			//show download link
			var serverFileId       = fileParts[0];
			downloadLink.innerHTML = 'Download link';
			downloadLink.href 	   = 'http://dnduploader.filkor.org/d/?id=' + serverFileId;			
			
		} else {
			//else if not uploaded the whole then get the current package number, and return the percent		
			var packetSize 	    = 512 * 512; //bytes, should be a global value in reality
			var totalPackages   = Math.ceil(totalSize / packetSize);
			
			var progressPercent =  (currentPackage / totalPackages) * 100;			
		}
		
		//some sugar to animate progressbar, and FIX its right corner
		$(progressElement).find('.ui-progressbar-value').addClass('ui-corner-right').stop(true).animate({
			width: progressPercent + '%'
		}, 400);		
		
		
	}
}


function htmlEscape(str) {
    return String(str)
            .replace(/&/g, '&amp;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;');
}
