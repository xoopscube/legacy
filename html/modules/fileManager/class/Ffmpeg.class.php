<?php
if (!defined('XOOPS_ROOT_PATH')) exit();

class Ffmpeg {

	// image capture
	function imageCapture($file) {
		$root =& XCube_Root::getSingleton();
		$mConfig = $root->mContext->mModuleConfig;
		// get dirctory permission
		$dirPermission = fileperms(dirname(XOOPS_UPLOAD_PATH . $file)) ;
		// dirctory permission is 0777
		if($dirPermission =='16895') {
			// check is this movie file ?
			if (Ffmpeg::checkMovie($file)) {
				$captureFilePath = XOOPS_UPLOAD_PATH . $file ;
				$fineName = basename($file, substr( strrchr( $file, "." ), 0));
				$imageFileName = Ffmpeg::getBaseFile($captureFilePath) .'.jpg' ;
				// ffmpeg command path
				if ($mConfig['ffmpegpath'] != '') {
					putenv("PATH=".$mConfig['ffmpegpath']."");
				}
				// image capture
				system("ffmpeg -i ".$captureFilePath." -ss ".$mConfig['ffmpegcapture']." -vcodec mjpeg -vframes 1 -an -f rawvideo -y ". $imageFileName);
				return true;
			} else {
				// error : file extension
				return false;
			}
		} else {
			// error : dirctory permission
			return false;
		}
	}

	// media convert
	function mediaConvert($file) {
		$root =& XCube_Root::getSingleton();
		$mConfig = $root->mContext->mModuleConfig;
		// get dirctory permission
		$dirPermission = fileperms(dirname(XOOPS_UPLOAD_PATH . $file)) ;
		// dirctory permission is 0777
		if($dirPermission =='16895') {
			// check is this movie file ?
			if (Ffmpeg::checkMovie($file)) {
				// you need check movie file
				$captureFilePath = XOOPS_UPLOAD_PATH . $file ;
				$fineName        = basename($file, substr( strrchr( $file, "." ), 0));
				$convertFileName = Ffmpeg::getBaseFile($captureFilePath) .'.flv' ;
				$imageFileName   = Ffmpeg::getBaseFile($captureFilePath) .'.jpg' ;
				// ffmpeg command path
				if ($mConfig['ffmpegpath'] != '') {
					putenv("PATH=".$mConfig['ffmpegpath']."");
				}
				ini_set('max_execution_time', 3600);
				// file convert options
				system("ffmpeg -i ". $captureFilePath. " -ar 44100 -s qvga -y ". $convertFileName);  // qvga - defult
				// etc options
				// system("ffmpeg -i ". $captureFilePath. " -ar 44100 -b 1150k -s vga -y ". $convertFileName);  // vga bitrate 128k
				// system("ffmpeg -i ". $captureFilePath. " -ar 44100 -b 128k -s vga -y ". $convertFileName);   // vga bitrate 128k
				// system("ffmpeg -i ". $captureFilePath. " -ar 44100 -s vga -y ". $convertFileName);           // vga
				// system("ffmpeg -i ". $captureFilePath. " -ar 44100 -s qvga -y ". $convertFileName ." > /dev/null &");;  // qvga
				// image capture
				system("ffmpeg -i ". $convertFileName. " -ss ". $mConfig['ffmpegcapture']. " -vcodec mjpeg -vframes 1 -an -f rawvideo -y ". $imageFileName);
				return true;
			} else {
				// error : file extension
				return false;
			}

		} else {
			// error : dirctory permission
			return false;
		}
	}

	// check movie file
	function checkMovie($fileName = '') {
		if ($fileName == '') {
			return false;
		}
		// $movie file extension white list
		// you need chenge your server's settings
		$movieTypeWhitelist = "flv|avi|mwv|mov|MOV|mpg|qt|mov|3gp|3gp2|mp4"; 
		$fileExtension = substr( strrchr( $fileName, "." ), 1);
		if (eregi($movieTypeWhitelist, $fileExtension)) {
			return true;
		}
		return false;
	}

	//  delete file extension
	// $path : /var/www/html/uploads/elmm/akb48/AKB48-sukart_hirari.flv
	// to    : /var/www/html/uploads/elmm/akb48/AKB48-sukart_hirari
	function getBaseFile($path) {
		$fileNameCount = strlen($path) - strlen(strrchr( $path, "." )) ;
		return  substr($path, 0, $fileNameCount); 
	}

	// get path
	// $path : /var/www/html/uploads/elmm/akb48/AKB48-sukart_hirari.flv
	// to    : /var/www/html/uploads/elmm/akb48/
	function getBasePath($path) {
		$fileNameCount = strlen($path) - strlen(strrchr( $path, "/" )) +1 ;
		return  substr($path, 0, $fileNameCount); 
	}

}
?>