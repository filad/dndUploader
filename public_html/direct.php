<?php
session_start();
require_once('../external/mysql_connect.php');
require_once('../external/functions.php');

define('FILE_PATH','../uploaded_files/');

/**
 *	Pushes large file to the user
 */
function filkor_pushFile($fileId, $originalFileName) 
{
	header('Contect-Type: application/octet-stream');
	header('Content-disposition: attachment; filename="'.$originalFileName.'"');
	header('Content-Length: ' . filesize(FILE_PATH . $fileId));
	
	
	
	if (!$handle = fopen(FILE_PATH . $fileId, 'r')) {
		error_log('fopen error', 0);
		header('Location: /404/');
		exit;
	}
	
	//force download
	// pick a bufsize that makes you happy (8192 has been suggested).
	$bufsize = 8192;
	$buff = '';
	while (!feof($handle)) {
		$buff = fread($handle, $bufsize);
		echo $buff;
		flush();
	}
	fclose($handle);
}


if (!isset($_GET['id'], $_GET['token'])) {
	header('Location: /404/');
	exit;
}

//check token
if ($_SESSION['token'] != $_GET['token']) {
	error_log('Bad token provided to direct.php' , 0);
	header('Location: /404/');
	exit;

} 

if (preg_match('/[^0-9]/', $_GET['id'])) {
	header('Location: /404/');
	exit;
}

if (file_exists(FILE_PATH.$_GET['id'])) {
	
	$fileId = $_GET['id'];
	$originalFileName = filkor_getFileNameFromMysql($fileId);
	filkor_pushFile($fileId, $originalFileName);
	
} else {
	header('Location: /404/');
	exit;	
}

?>