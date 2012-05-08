<?php

/*
 * @author Niklas von Hertzen <niklas at hertzen.com>
 * @created 12.7.2011 
 * @website http://hertzen.com
 */

$filePath = "files/";
$packetSize = 512 * 512; // bytes, need to be same as in JavaScript
$storeFiles = true;

function throwError($error) {
    echo json_encode(array(
        "error" => $error
    ));
    exit;
}


if (isset($_POST)) {

    if (count($_GET) == 0) {

        if (isset($_POST['totalSize']) && isset($_POST['type']) && isset($_POST['fileName']) && is_numeric($_POST['totalSize'])) {

			
            $fileData = $_POST['totalSize'] . "|" . preg_replace('/[^A-Za-z0-9\/]/', '', $_POST['type']) . "|" . preg_replace('/[^A-Za-z0-9_\.]/', '', $_POST['fileName']);
		
            $fileid = time() . rand(1, 150000); //the probability of this being unique is good enough in most cases
			
            $token = md5($fileData);
			
            if (!$handle = fopen($filePath . $fileid . "-" . $token . ".txt", 'w')) {
                throwError("Unable to create new file for metadata");
            }

            if (fwrite($handle, $fileData) === FALSE) {
                throwError("Unable to write metadata for file");
            }
            fclose($handle);
            $json = array(
                "action" => "new_upload",
                "fileid" => $fileid,
                "token" => $token
            );
        } elseif (isset($_POST['fileid']) && isset($_POST['token']) && is_numeric($_POST['fileid']) && preg_match('/[A-Za-z0-9]/', $_POST['token'])) {

            $contents = @file_get_contents($filePath . $_POST['fileid'] . "-" . $_POST['token'] . ".txt");
            if (!$contents) {
                throwError("No file found for the provided ID / token");
            }

            // check if we the file has already been uploaded, merged and completed
            if (!file_exists($filePath . $_POST['fileid'])) {

                list($fileSize, $fileType, $fileName) = explode("|", $contents);

                $totalPackages = ceil($fileSize / $packetSize);

                // check that all packages exist
                if ($storeFiles) {
                    for ($package = 0; $package < $totalPackages; $package++) {
                        if (!file_exists($filePath . $_POST['fileid'] . "-" . $package)) {
                            throwError("Missing package #" . $package);
                        }
                    }

                    // open file to create final file
                    if (!$handle = fopen($filePath . $_POST['fileid'], 'w')) {
                        throwError("Unable to create new file for merging");
                    }

                    // write each package to the file
                    for ($package = 0; $package < $totalPackages; $package++) {

                        $contents = @file_get_contents($filePath . $_POST['fileid'] . "-" . $package);
                        if (!$contents) {
                            unlink($filePath . $_POST['fileid']);
                            throwError("Unable to read contents of package #" . $package);
                        }

                        if (fwrite($handle, $contents) === FALSE) {
                            unlink($filePath . $_POST['fileid']);
                            throwError("Unable to write package #" . $package . " to merge");
                        }
                    }

                    // remove the packages
                    for ($package = 0; $package < $totalPackages; $package++) {
                        if (!unlink($filePath . $_POST['fileid'] . "-" . $package)) {
                            throwError("Unable to remove package #" . $package);
                        }
                    }
                }
            }
            $json = array(
                "action" => "complete",
                "file" => $_POST['fileid']
            );
        }
    } else {
        if (isset($_GET['fileid']) && isset($_GET['token']) && isset($_GET['packet']) && is_numeric($_GET['packet']) && is_numeric($_GET['fileid'])) {
            if (file_exists($filePath . $_GET['fileid'] . "-" . $_GET['token'] . ".txt")) {
                if ($storeFiles) {
                    if (!$handle = fopen($filePath . $_GET['fileid'] . "-" . $_GET['packet'], 'w')) {
                        exit;
                    }

                    if (fwrite($handle, $GLOBALS['HTTP_RAW_POST_DATA']) === FALSE) {

                        throwError("Unable to write to package #" . $_GET['packet']);
                    }
                    fclose($handle);
                }
                
                $json = array(
                    "action" => "new_packet",
                    "result" => "success",
                    "packet" => $_GET['packet'],
                );
            }
        }
        //  print_r($_GET);
        //  echo strlen($GLOBALS['HTTP_RAW_POST_DATA']);
    }
} else {
    throwError("No post request");
}


if (isset($json)) {
    echo json_encode($json);
}


?>
