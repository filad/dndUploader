<?php

/**
 * @author Adam Filkor <adam.filkor@gmail.com>
 * @created 2.05.2012
 * @website http://filkor.org
 */

require_once('../external/mysql_connect.php');

define('FILE_PATH', '../uploaded_files/');
define('PACKET_SIZE', 512 * 512); // bytes, need to be same as in JavaScript
define('STORE_FILES', true); //whether store files or not

function throwError($error)
{
    echo json_encode(array(
        "error" => $error
    ));
    exit;
}

function sendAsJSON($array)
{
	echo json_encode($array);
	exit;
}

if (!isset($_POST)) {
	throwError("No post request");
}


function newUpload() 
{	
    $fileData = $_POST['totalSize'] . "|" . preg_replace('/[^A-Za-z0-9\/]/', '', $_POST['type']) . "|" . preg_replace('/[^A-Za-z0-9_\.]/', '', $_POST['fileName']);
    $originalFileName = $_POST['fileName'];
    $token 	  = md5($fileData);
	
	do {
		//the probability of this being unique is good enough in most cases
		//2^31 - 1 is the max int on 32 bit systems
		$fileid   = time() . mt_rand(5, pow(2, 31) - 1); 		
		
		$query = sprintf("INSERT INTO files (id, fileData, fileid, token, original_filename, upload_date) VALUES(NULL, '%s', ". $fileid . ", '" . $token . "', '%s', 0)",
		mysql_real_escape_string($fileData),
		mysql_real_escape_string($originalFileName)
		);
		

		mysql_query($query);
		
		define("MYSQL_CODE_DUPLICATE_KEY", 1062); // @see http://dev.mysql.com/doc/refman/5.1/en/error-messages-server.html
	} while (mysql_errno() == MYSQL_CODE_DUPLICATE_KEY); //we dont like  duplicate keys
    
    sendAsJSON(array(
        "action" => "new_upload",
        "fileid" => $fileid,
        "token"  => $token
    ));	
}


function mergeFiles() 
{

	$sql = mysql_query("SELECT fileData FROM files WHERE fileid = '" . $_POST['fileid'] . "' AND token = '" . $_POST['token']."'");
	$row = mysql_fetch_assoc($sql);
	
    if ($row === FALSE) {
        throwError("No file found in the database for the provided ID / token");
    }

    // check if we the file has already been uploaded, merged and completed
    if (!file_exists(FILE_PATH . $_POST['fileid'])) {
		
        list($fileSize, $fileType, $fileName) = explode("|", $row['fileData']);

        $totalPackages = ceil($fileSize / PACKET_SIZE);

        // check that all packages exist
        for ($package = 0; $package < $totalPackages; $package++) {
            if (!file_exists(FILE_PATH . $_POST['fileid'] . "-" . $package)) {
                throwError("Missing package #" . $package);
            }
        }

        // open file to create final file
        if (!$handle = fopen(FILE_PATH . $_POST['fileid'], 'w')) {
            throwError("Unable to create new file for merging");
        }

        // write each package to the file
        for ($package = 0; $package < $totalPackages; $package++) {

            $contents = @file_get_contents(FILE_PATH . $_POST['fileid'] . "-" . $package);
            if (!$contents) {
                unlink(FILE_PATH . $_POST['fileid']);
                throwError("Unable to read contents of package #" . $package);
            }

            if (fwrite($handle, $contents) === FALSE) {
                unlink(FILE_PATH . $_POST['fileid']);
                throwError("Unable to write package #" . $package . " to merge");
            }
        }

        // remove the packages
        for ($package = 0; $package < $totalPackages; $package++) {
            if (!unlink(FILE_PATH . $_POST['fileid'] . "-" . $package)) {
                throwError("Unable to remove package #" . $package);
            }
        }
    }
	
	//on success, update the uploaded date in mysql
	mysql_query("UPDATE files SET upload_date = " . time() ." WHERE fileid = '".$_POST['fileid']."'");
	
    sendAsJSON(array(
        "action" => "complete",
        "file" => $_POST['fileid']
    ));
}


/**
 * After initialized the upload, we can start receiving the packets (or 'slices')
 */
function getPacket()
{
	$sql = mysql_query("SELECT fileid FROM files WHERE fileid = '" . $_GET['fileid'] . "' AND token = '" . $_GET['token']."'");
	$rowExists = is_resource($sql) && (mysql_num_rows($sql) > 0);
	//die (var_dump($_GET). 'rows: '.mysql_num_rows($sql));
    if ($rowExists) {
        if (STORE_FILES) {
            if (!$handle = fopen(FILE_PATH . $_GET['fileid'] . "-" . $_GET['packet'], 'w')) {
                throwError("Unable to open package handle");
            }

            if (fwrite($handle, $GLOBALS['HTTP_RAW_POST_DATA']) === FALSE) {

                throwError("Unable to write to package #" . $_GET['packet']);
            }
            fclose($handle);
        }
        
        sendAsJSON(array(
            "action" => "new_packet",
            "result" => "success",
            "packet" => $_GET['packet'],
        ));
    }
}



if (count($_GET) == 0) {
	if (isset($_POST['totalSize']) && isset($_POST['type']) && isset($_POST['fileName']) && is_numeric($_POST['totalSize'])) {
		newUpload();
	} else if (isset($_POST['fileid']) && isset($_POST['token']) && is_numeric($_POST['fileid']) && preg_match('/[A-Za-z0-9]/', $_POST['token'])) { 
		mergeFiles();
	}
} else {
	if (isset($_GET['fileid']) && isset($_GET['token']) && isset($_GET['packet']) && is_numeric($_GET['packet']) && is_numeric($_GET['fileid'])) {
		getPacket();
	}
}



?>
