<?php
/**
 * SWFUpload: http://www.swfupload.org, http://swfupload.googlecode.com
 *
 * mmSWFUpload 1.0: Flash upload dialog - http://profandesign.se/swfupload/,  http://www.vinterwebb.se/
 *
 * SWFUpload is (c) 2006-2007 Lars Huring, Olov Nilzén and Mammon Media and is released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * SWFUpload 2 is (c) 2007-2008 Jake Roberts and is released under the MIT License:
 * http://www.opensource.org/licenses/mit-license.php
 *
 * modifed Hiroki Seike
 * TODO Error handring
 */

require_once "../../mainfile.php";
$root =& XCube_Root::getSingleton();


// check ticket 
$auth = false;

if (isset($_POST["ticket"])) {
	$ticket = $_POST["ticket"];
	// fileManeger token
	$handler =& xoops_getmodulehandler('token','fileManager');
	$obj = $handler->get($ticket);
	$dbToken   = $obj->getShow('token');
	$expire    = $obj->getShow('expire');
	$ipAddress = $obj->getShow('ipaddress');
	$now = time();
	// check ticket and ip address
	if ($_POST['ticket'] == $dbToken and getenv("REMOTE_ADDR") == $ipAddress ) {
		$auth = true;
	}
	// ticket time out
	if ($expire < $now) {
		// delete ticket
		$handler->deleteToken($ticket);
		$auth = false;
	}
}

// ticket error
if (!$auth) {
	header("HTTP/1.1 500 Internal Server Error");
	echo "Bat Reqest.";
	exit(0);
}

// Chack upload path
if (isset($_POST["path"])) {
	$path = $_POST["path"];
	// Relative path check
	if (preg_match ("/\.\//", $path)) {
		header("HTTP/1.1 500 Internal Server Error");
		echo "Bat Reqest.";
		exit(0);
	}

	// check uploads path
	$save_path = XOOPS_ROOT_PATH . "/uploads/". $path ."/";
	if (!file_exists($save_path)) {
		header("HTTP/1.1 500 Internal Server Error");
		echo "Bat Reqest.";
		exit(0);
	}
	
} else {
	header("HTTP/1.1 500 Internal Server Error");
	echo "Bat Reqest.";
	exit(0);
}

// get file extensions whitelist
$config_handler = &xoops_gethandler('config');
$moduleConfig =& $config_handler->getConfigsByDirname('fileManager');
$extension_whitelist = explode('|', $moduleConfig['extensions']);

// Check post_max_size (http://us3.php.net/manual/en/features.file-upload.php#73762)
$POST_MAX_SIZE = ini_get('post_max_size');
$unit = strtoupper(substr($POST_MAX_SIZE, -1));
$multiplier = ($unit == 'M' ? 1048576 : ($unit == 'K' ? 1024 : ($unit == 'G' ? 1073741824 : 1)));

if ((int)$_SERVER['CONTENT_LENGTH'] > $multiplier*(int)$POST_MAX_SIZE && $POST_MAX_SIZE) {
	header("HTTP/1.1 500 Internal Server Error");
	echo "POST exceeded maximum allowed size.";
	exit(0);
}

// The path were we will save the file (getcwd() may not be reliable and should be tested in your environment)
$upload_name = "Filedata";
$max_file_size_in_bytes = 21474836470; // 2GB in bytes

// Characters allowed in the file name (in a Regular Expression format)
$valid_chars_regex = '.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-';

// Other variables
$MAX_FILENAME_LENGTH = 260;
$file_name = "";
$file_extension = "";
$uploadErrors = array(
  0=>"There is no error, the file uploaded with success",
  1=>"The uploaded file exceeds the upload_max_filesize directive in php.ini",
  2=>"The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form",
  3=>"The uploaded file was only partially uploaded",
  4=>"No file was uploaded",
  6=>"Missing a temporary folder"
);

// Validate the upload
if (!isset($_FILES[$upload_name])) {
	HandleError("No upload found in \$_FILES for " . $upload_name);
	exit(0);
} else if (isset($_FILES[$upload_name]["error"]) && $_FILES[$upload_name]["error"] != 0) {
	HandleError($uploadErrors[$_FILES[$upload_name]["error"]]);
	exit(0);
} else if (!isset($_FILES[$upload_name]["tmp_name"]) || !@is_uploaded_file($_FILES[$upload_name]["tmp_name"])) {
	HandleError("Upload failed is_uploaded_file test.");
	exit(0);
} else if (!isset($_FILES[$upload_name]['name'])) {
	HandleError("File has no name.");
	exit(0);
}

// Validate the file size (Warning: the largest files supported by this code is 2GB)
$file_size = @filesize($_FILES[$upload_name]["tmp_name"]);
if (!$file_size || $file_size > $max_file_size_in_bytes) {
	HandleError("File exceeds the maximum allowed size");
	exit(0);
}

if ($file_size <= 0) {
	HandleError("File size outside allowed lower bound");
	exit(0);
}

// Validate file name (for our purposes we'll just remove invalid characters)
$file_name = preg_replace('/[^'.$valid_chars_regex.']|\.+$/i', "", basename($_FILES[$upload_name]['name']));

// multi bvte filename is error
$trimName = substr($file_name, 0, strlen($file_name) - strlen(strrchr( $file_name, "." ))); 
if ($trimName =='') {
	HandleError("Invalid file name");
	exit(0);
}

if (strlen($file_name) == 0 || strlen($file_name) > $MAX_FILENAME_LENGTH) {
	HandleError("Invalid file name");
	exit(0);
}

// Validate that we won't over-write an existing file
if (file_exists($save_path . $file_name)) {
	HandleError("File with this name already exists");
	exit(0);
}

// Validate file extension
$path_info = pathinfo($_FILES[$upload_name]['name']);
$file_extension = $path_info["extension"];
$is_valid_extension = false;
foreach ($extension_whitelist as $extension) {
	if (strcasecmp($file_extension, $extension) == 0) {
		$is_valid_extension = true;
		break;
	}
}
if (!$is_valid_extension) {
	HandleError("Invalid file extension");
	exit(0);
}

if (!@move_uploaded_file($_FILES[$upload_name]["tmp_name"], $save_path.$file_name)) {
	HandleError("File could not be saved.");
	exit(0);
}

// Return output to the browser (only supported by SWFUpload for Flash Player 9)
echo "File Received";
exit(0);

/* Handles the error output.  This function was written for SWFUpload for Flash Player 8 which
cannot return data to the server, so it just returns a 500 error. For Flash Player 9 you will
want to change this to return the server data you want to indicate an error and then use SWFUpload's
uploadSuccess to check the server_data for your error indicator. */
function HandleError($message) {
	header("HTTP/1.1 500 Internal Server Error");
	echo $message;
}
?>
