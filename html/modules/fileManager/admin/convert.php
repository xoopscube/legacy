<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/
require_once "../../../mainfile.php";
require_once XOOPS_MODULE_PATH. '/fileManager/class/Ffmpeg.class.php';

$root =& XCube_Root::getSingleton();

$fileName   = isset($_GET['file']) ? trim($_GET['file']) : "";
$returnPath = isset($_GET['path']) ? trim($_GET['path']) : "";

if ($returnPath != '') {
	$url = 'index.php?path='. $returnPath ;
} else {
	$url = 'index.php' ;
}

$moduleConfig = $root->mContext->mModuleConfig;

// TODO more check

// is ffmpeg use
if ($moduleConfig['ffmpeguse']> 0) {
	if (file_exists(XOOPS_UPLOAD_PATH . $fileName)) {
		// TODO check upload file your server max size
		// get dirctory permission
		$dirPermission = fileperms(dirname(XOOPS_UPLOAD_PATH . $fileName)) ;
		// dirctory permission is 0777
		if($dirPermission =='16895') {
			// check is this movie file ?
			if (Ffmpeg::checkMovie($fileName) ) {
				// media convert
				system("test.php > /dev/null &");
				Ffmpeg::mediaConvert($fileName) ;
			}
		}
	}
}
$root->mController->executeForward($url);
?>
