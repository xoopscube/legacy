<?php
/*=====================================================================
	(C)2007 BeaBo Japan by Hiroki Seike
	http://beabo.net/
=====================================================================*/
if (!defined('XOOPS_ROOT_PATH')) exit();

// template header module infomation
function getModuleInfo() {
	$moduleHader = array();
	$root =& XCube_Root::getSingleton();
	$moduleHader['module_id']   = $root->mContext->mXoopsModule->getvar('mid');
	$moduleHader['module_name'] = $root->mContext->mXoopsModule->get('name');
	$moduleHader['module_path'] = $root->mContext->mXoopsModule->get('dirname');
	return $moduleHader;
}

// get XCL system using images for hide files
function getSyetemImages($hideFile) {
	$root =& XCube_Root::getSingleton();
	// image
	$root->mController->executeHeader();
	$handler =& xoops_getmodulehandler('image','legacy');
	$mObjects =& $handler->getObjects();
	foreach ($mObjects as $key => $val) {
		foreach ( array_keys($val->gets()) as $var_name ) {
			$hideFile[] = $val->getShow('image_name');
		}
	}
	// smailes
	$handler =& xoops_getmodulehandler('smiles','legacy');
	$mObjects =& $handler->getObjects();
	foreach ($mObjects as $key => $val) {
		foreach ( array_keys($val->gets()) as $var_name ) {
			$hideFile[] = $val->getShow('smile_url');
		}
	}
	// user users
	$handler =& xoops_getmodulehandler('users','user');
	$mCriteria =& new CriteriaCompo();
	$mCriteria->add(new Criteria('user_avatar', 'blank.gif','<>' ));
	$mObjects =$handler->getObjects($mCriteria);
	foreach ($mObjects as $key => $val) {
		foreach ( array_keys($val->gets()) as $var_name ) {
			$hideFile[] = $val->getShow('user_avatar');
		}
	}
	// user ranks
	$handler =& xoops_getmodulehandler('ranks','user');
	$mObjects =& $handler->getObjects();
	foreach ($mObjects as $key => $val) {
		foreach ( array_keys($val->gets()) as $var_name ) {
		$hideFile[] = $val->getShow('rank_image');
		}
	}
	// user avater
	$handler =& xoops_getmodulehandler('avatar','user');
	$mObjects =& $handler->getObjects();
	foreach ($mObjects as $key => $val) {
		foreach ( array_keys($val->gets()) as $var_name ) {
		$hideFile[] = $val->getShow('avatar_file');
		}
	}
	return $hideFile;
}


// FileSystemUtilty is filepath , filename utility
Class FileSystemUtilty {

	// base -> http://labs.uechoco.com/blog/2007/08/page/2
	// get file size
	function bytes($filesize) {
		if (!$filesize) {
			return null;
		}
		$unim = array("Bytes","KB","MB","GB","TB","PB");
		$count = 0;
		while ($filesize>=1024) {
			$count++;
			$filesize = $filesize/1024;
		}
		return number_format($filesize,($count ? 2 : 0),".",",")." ".$unim[$count];
	}

	// NOT USE
	// get_file_size is set direct file
	function get_file_size($filename) {
		if (!file_exists($filename)) {
			return null;
		}
		$size = filesize($filename);
		$sizes = array('Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB');
		$ext = $sizes[0];
		for ($i=1; (($i <count($sizes)) && ($size>= 1024)); $i++) {
			$size = $size / 1024;
			$ext = $sizes[$i];
		}
		return round($size, 2). $ext;
	}

	// check dirctory name and permission
	function checkFolder($folderPath='') {
		$isWriteble = false;    // defult
		if ($folderPath!='') {
			// is exists ?
			if (!file_exists($folderPath)) {
				return false;
			}
			// is dirctory ?
			if (!is_dir($folderPath)) {
				return false;
			}
			// dirctory permission is 777 ?
			if ( fileperms($folderPath) == '16895') {
				return true;
			}
		} else {
			return false;
		}
	}

	// NOT USE
	// file name check
	function is_filename($text) {
		if (preg_match("/^[a-zA-Z0-9_~\-]+$/",$text)) {
			return true;
		} else {
			return false;
		}
	}

	// file name
	function getFileName($fileName='') {
		$fileNameCount = strlen($fileName) - strlen(strrchr( $fileName, "." )) ;
		return substr($fileName, 0, $fileNameCount);
	}

	// file extension
	function getFileExtension($fileName='') {
		return substr( strrchr( $fileName, "." ), 1);
	}

	// get folder List
	// 
	// 引数 $dirpath は、中身を見たいディレクトリの相対パスまたは絶対パス。お尻にスラッシュは不要。
	// $invisible : check invisible files (不可視ファイルをリストに含める)
	function getDirlLst($dirpath='' , $invisible = true ){
		if ( strcmp($dirpath,'')==0 ) {
			$file_list = array();
			$dir_list = array();
			if( ($dir = @opendir($dirpath) ) == FALSE ) {
				die( "dir {$dirpath} not found.");
			}
			while ( ($file=readdir( $dir )) !== FALSE ){
				if ( is_dir( "$dirpath/$file" ) ){ 
					if( strpos( $file ,'.' ) !== 0 ){      
						$dir_list["$file"] = getdirlist( "$dirpath/$file" , $invisible );  
					}
				}else {
					if( $invisible ){
						array_push($file_list, $file);
					}else{
						if( strpos( $file , '.' )!==0 ) array_push( $file_list , $file);
					}
				}
			}
			return array( "file"=>$file_list , "dir"=>$dir_list);
		} else {
			return false;
		}
	}

	// NOT USE
	// make new file name
	// check file name and length
	function getNewFileName($fileName = '',$maxLength = 200 ){

		// filename is null
		if (is_null($fileName)) {
			return false;
		}
		
		// Allowed file extensions
		$extension_whitelist = "flv|avi|mwv|mov|MOV|mpg|qt|mov|3gp|3gp2|mp4"; 
		$fileName = htmlSpecialChars(trim($fileName), ENT_QUOTES );

		// trim path name
		$baseFaileName = basename($fileName);

		// replace word 'x'
		$newFileName = preg_replace('/[^.A-Z0-9_ !@#$%^&()+={}\[\]\',~`-]|\.+$/i', "x", $baseFaileName);

		// get extension
		$fileExtension = substr( strrchr( $newFileName, "." ), 1);
		// extension type check
		if ( ereg("[ \t]",$fileExtension) or is_null($fileExtension)) {
			// extension is tab or space or null
			return false;
		} else {
			if (!eregi($extension_whitelist,  $fileExtension)) {
				// extension is no math white list
				return false;
			}
		}
		// file name length check
		if (strlen($newFileName) > $maxLength) {
			// rename file name
			$trimName = substr($newFileName, 0, strlen($newFileName) - strlen(strrchr( $newFileName, "." ))); 
			// trim to timestamp space
			$trimName = substr($trimName , 0 ,$maxLength - 13);
			// insert timestamp
			$newFileName = $trimName. "_". date("YmdHi"). '.'. $fileExtension;
		}

		return $newFileName;
	}


}

?>