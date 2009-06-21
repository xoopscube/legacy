<?php
/*=====================================================================
  (C)2007 BeaBo Japan by Hiroki Seike
  http://beabo.net/
=====================================================================*/

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH. '/fileManager/class/AbstractListAction.class.php';
require_once XOOPS_MODULE_PATH. '/fileManager/admin/forms/IndexFilterForm.class.php';
require_once XOOPS_MODULE_PATH. '/fileManager/admin/include/functions.php';

require_once XOOPS_MODULE_PATH. '/fileManager/class/Ffmpeg.class.php';

class FileManager_indexAction extends FileManager_AbstractListAction
{
	var $mConfig = array();

	var $currentPath  = null;
	var $mPagenavi    = null;
	var $setSortkey   = null;
	var $dirList      = array();
	var $filesList    = array();
	var $filesCount   = 0;
	var $parentPath   = null;
	var $isOpenFolder = false;
	var $filter       = null;

	var $pathLink ='';
	var $pathNameArray    = array();

	// user config
	var $imageThumbSize  = 100;
	var $systemImageLock = true;

	// for menu
	var $breadCrumbs     = array();
	var $confirmMssage   = null;
	var $moduleHeader    = null;

	// per page
	var $perpage = 20 ;

	// set start
	function _getStart()
	{
		return xoops_getrequest('start');
	}

	// sort
	function _getSort()
	{
		return xoops_getrequest('sort');
	}

	// change dirctory name
	function _getPath()
	{
		return xoops_getrequest('path');
	}

	function _getAction()
	{
		return xoops_getrequest('action');
	}

	// current pass
	function _getCurrentPath()
	{
		return xoops_getrequest('current_path');
	}


	function &_getHandler()
	{
		return 0;
	}

	// _getFilterForm Overrid
	function &_getFilterForm()
	{
		$filter =& new FileManager_IndexFilterForm($this->_getPageNavi());
		return $filter;
	}

	function _getBaseUrl()
	{
		return './index.php';
	}

	// _getPageNavi Overrid
	function &_getPageNavi()
	{
		$navi =& parent::_getPageNavi();
		$navi->setStart($this->_getStart());
		$navi->setPerpage($this->perpage);
		$navi->setTotalItems($this->filesCount);
		return $navi;
	}

	function prepare(&$controller, &$xoopsUser, $moduleConfig)
	{
		$this->mConfig = $moduleConfig;

		// file delete
		if ($this->_getAction() =='delete') {
			if (isset($_POST['check'])) {
				// TODO
				// cheeck Security
				// useing DeleteAction ?
				while (list($name, $value) = each ($_POST['check'])) {
					if (file_exists(XOOPS_UPLOAD_PATH .$value)) {
						// get dirctory permission
						$dirPermission = fileperms(dirname(XOOPS_UPLOAD_PATH . $value)) ;
						// dirctory permission is 0777
						if($dirPermission =='16895') {
							// file delete
							unlink (XOOPS_UPLOAD_PATH . $value);
						} else {
							// error
							$this->confirmMssage = sprintf(_AD_FILEMANAGER_ERROR_DELETE_FOR_PERMISSION , $value);
						}
					} else {
						return CONTENTS_FRAME_VIEW_ERROR;
					}
				}
			}
		}

		// is ffmpeg use
		if ($this->mConfig['ffmpeguse']> 0) {
			// capture action
			if ($this->_getAction() =='capture') {
				// check files
				if (isset($_POST['check'])) {
					while (list($name, $value) = each ($_POST['check'])) {
						if (file_exists(XOOPS_UPLOAD_PATH . $value)) {
							// get dirctory permission
							$dirPermission = fileperms(dirname(XOOPS_UPLOAD_PATH . $value)) ;
							// dirctory permission is 0777
							if($dirPermission =='16895') {
								// make capter image
								Ffmpeg::imageCapture($value);
							} else {
								// error
								$this->confirmMssage = sprintf(_AD_FILEMANAGER_ERROR_FILE_PERMISSION , $value);
							}
						} else {
							return CONTENTS_FRAME_VIEW_ERROR;
						}
					}
				}
			}
		}
		// TODO extensions parameter check
	}

	function getDefaultView(&$controller, &$xoopsUser)
	{
		// Initial setting
		$root =& XCube_Root::getSingleton();
		$this->imageThumbSize = $this->mConfig['thumbsize'] ;

		if ($this->_getCurrentPath() != '') {
			$this->currentPath = $this->_getCurrentPath() ;
		} else {
			$this->currentPath = $this->_getPath() ;
		}

		// Relative path check
		if (preg_match ("/\.\//", $this->currentPath)) {
			return CONTENTS_FRAME_VIEW_ERROR;
		} 

		// get path check
		if (!file_exists(XOOPS_UPLOAD_PATH . $this->currentPath)) {
			return CONTENTS_FRAME_VIEW_ERROR;
		}

		// array to directory name 
		$dirNameArray = preg_split('/\//', substr($this->currentPath, 1));

		$pathArray =  array();


		if ($dirNameArray[0]<>"") {
			foreach($dirNameArray as $pathName) {
				$this->parentPath .= '/'. $pathName ;
				$this->pathLink = $this->parentPath ;
				$pathArray['name'] = $pathName;
				$pathArray['link'] = $this->pathLink;
				array_push($this->pathNameArray, $pathArray) ;
			}
		}

		// $pathNameArray = Array ( [0] => Array ( [name] => temp [link] => /temp  ) [1] => Array ( [name] => aaaa [link] => /temp /aaaa  ) ) 


		if ($this->currentPath == "" || $this->currentPath == "/") {
			$this->setSortkey = '';
			$filesUploadPath = XOOPS_UPLOAD_PATH ;
			$filesUploadUrl  = XOOPS_UPLOAD_URL ;
			$isFileHome = true;
		} else {
			$this->setSortkey = $this->currentPath;
			$filesUploadPath = XOOPS_UPLOAD_PATH . $this->currentPath ;
			$filesUploadUrl  = XOOPS_UPLOAD_URL . $this->currentPath ;
			$isFileHome = false;
		}

		// get parent directory path
		$dirsNameArray = explode('/',$this->currentPath);
		$d = array_pop($dirsNameArray);
		$parentPath = implode("/", $dirsNameArray) ;

		// get dirctory permission
		$this->isOpenFolder = (fileperms($filesUploadPath) =='16895') ;

		$hideFile = array();    // hide files array
		$fileArray = $pathArray =  array();
		$fileMediaType = $fileIcon = $pluginsUrl = $imageInfo = '';
		$imageThumb = false;

		// hide files name & file extension
		$systemFile = 'blank.gif|.htaccess';    // System files
		$hiddenType = "html|htm|php|js";        // Hidden File ext

		if ($isFileHome) {
			// functions.php
			// get system images array
			$hideFile= getSyetemImages($hideFile) ;
		}

		// TODO check using MAC plugin player
		// TODO need pdf , xcl ,csv ... other file type and viewr or link
		$imageType       = "gif|jpe?g|png|bmp";      // Image
		$flvType         = "flv";                    // FLVplayer
		$quicktimeType   = "qt|mov|3gp|3gp2|mp4";    // Quick Time
		$mp3Type         = "mp3";                    // Quick Time Sound
		$flashType       = "swf";                    // Flash
		$realplayerType  = "rm|ra";                  // RealPlayer
		$mediaplayerType = "avi|asf|wav|wma|wmv|mid|avi|mpe|mpg";  // WindowsMediaPlayer

		// $appliType = "txt|csv|pdf|xls|ods|doc|odt";             // application
		// Open a known directory, and proceed to read its contents
		if (is_dir($filesUploadPath)) {
			if ($dh = opendir($filesUploadPath)) {
				while (($file = readdir($dh)) !== false) {
					$mFileType = filetype($filesUploadPath .'/' .$file) ;
					// is directory
					if ($mFileType == 'dir') {
						// directory list
						$fileIcon = './../images/icon/folder.png';

						if ($file == '..') {
							// parent dirctory
							$dirArray['folderhandle'] = false;
							$dirArray['is_writable'] =is_writable($filesUploadPath .'/' .$file);
							$dirArray['linkpath'] = $parentPath;
							$dirArray['name'] = $file;
							$dirArray['type'] = _AD_FILEMANAGER_FOLDER ;
							$dirArray['icon'] = './../images/icon/folder_up.png';
							$stat = stat($filesUploadPath .'/' .$file);
							$timestamp = $stat['mtime'];
							// TODO get directory size ... file access speed is slow
							// TODO files count for speed up
							// $dirArray['count'] = count(scandir($filesUploadPath.'/' .$file)) - 2 ;
							$dirArray['time'] = $timestamp;
							//$dirArray['disk_space'] = disk_total_space($filesUploadPath .'/' .$file);
							$dirArray['upload_link'] = $file;
							$this->dirList[] = & $dirArray;
						}

						// Excluding the parent(..) and the current directory(.)
						if ($file != '.' && $file != "..") {
							$dirArray['folderhandle'] = (fileperms($filesUploadPath .'/' .$file) == '16895');
							$dirArray['is_writable'] =is_writable($filesUploadPath .'/' .$file);
							$dirArray['linkpath'] =$this->currentPath. '/' .$file;
							$dirArray['name'] = $file;
							$dirArray['type'] = _AD_FILEMANAGER_FOLDER ;
							$dirArray['icon'] = $fileIcon;
							$stat = stat($filesUploadPath .'/' .$file);
							$timestamp = $stat['mtime'];
							// TODO files count for speed up
							// $dirArray['count'] = count(scandir($filesUploadPath.'/' .$file)) - 2 ;
							$dirArray['time'] = $timestamp;
							// TODO get directory size ... file access speed is slow
							//$dirArray['disk_space'] = disk_total_space($filesUploadPath .'/' .$file);
							$dirArray['upload_link'] = $file;
							$this->dirList[] = & $dirArray;
						}
					} else {
						// file list
						// XOOPS using images
						if (!in_array ($file, $hideFile)) {
							// system file
							if (!eregi($systemFile, $file)) {
								// select type to file extension
								$fileExtension = substr( strrchr( $file, "." ), 1);
								// is not hidden files
								if (!eregi($hiddenType, $fileExtension)) {
									// set embed option and file icon
									if (eregi($imageType, $fileExtension)) {
										// Image
										$fileMediaType = 'IMAGE';
										$fileIcon = './../images/icon/photo.png';
										$imageSize = getimagesize($filesUploadPath .'/' .$file);
										$imageInfo = $imageSize[0] . ' x ' . $imageSize[1];
										if ($imageSize[0] > $this->imageThumbSize || $imageSize[1] > $this->imageThumbSize) { 
											// image thumb flag
											$imageThumb = true;
										}
									} elseif (eregi($flvType, $fileExtension)) {
										// FLV Player
										$fileMediaType = 'FLV';
										$fileIcon = './../images/icon/video.png';
									} elseif (eregi($mediaplayerType, $fileExtension)) {
										// WindowsMediaPlayer
										$fileMediaType = 'MEDIAPLAYER';
										$fileIcon = './../images/icon/video.png';
									} else if (eregi($quicktimeType, $fileExtension)) {
										// Quick Time Player
										$fileMediaType = 'QUICKTIME';
										$fileIcon = './../images/icon/video.png';
									} else if (eregi($mp3Type, $fileExtension)) {
										// Quick Time Sound
										$fileMediaType = 'MP3';
										$fileIcon = './../images/icon/music.png';
									} else if (eregi($realplayerType, $fileExtension)) {
										// RealPlayer
										$fileMediaType = 'REALPLAYER';
										$fileIcon = './../images/icon/video.png';
									} else if (eregi($flashType, $fileExtension)) {
										// Flash
										$fileMediaType = 'FLASH';
										$fileIcon = './../images/icon/page_white_actionscript.png';
									} else {
										$fileMediaType = 'ETC';
										$fileIcon = "";
									}
									// get file infomation
									$stat = stat($filesUploadPath .'/' .$file);
									// view file infofation
									$fileArray['file'] = $this->setSortkey. '/'. $file ;
									$fileArray['file_name']   = $file;
									$fileArray['file_url']    = $this->currentPath .'/' .$file ;
									$fileArray['file_size']   = FileSystemUtilty::bytes($stat['size']);
									$fileArray['file_statsize']   = $stat['size'];
									$fileArray['file_media']   = $fileMediaType ;
									$fileArray['file_type']   = $fileExtension ;
									$fileArray['time_stamp']  = filemtime($filesUploadPath .'/' .$file);
									$fileArray['icon']        = $fileIcon;
									$fileArray['file_info']  = $imageInfo;
									$fileArray['image_thumb'] = $imageThumb;
									$this->filesList[] = & $fileArray;
								}
							}
						}
					}
					// clear for loop
					$imageThumb = false;
					$fileMediaType = $fileIcon = $imageInfo = '';
					unset($fileArray);
					unset($dirArray);
				}
			// close directory
			closedir($dh);
			}
		}

		// sort directory
		sort($this->dirList);
		// file count
		$this->filesCount = count($this->filesList);

		// page navi
		$this->mFilter =& $this->_getFilterForm();
		$this->mFilter->fetch();
		// get sort array
		foreach ($this->filesList as $key => $row) {
		    $file_name[$key]  = $row['file_name'];
		    $file_type[$key]  = $row['file_type'];
		    $time_stamp[$key] = $row['time_stamp'];
		    $file_statsize[$key]  = $row['file_statsize'];
		}
		// sort file
		// TODO more simple
		if ($this->mFilter->getSort()=='file_statsize') {
			if ($this->mFilter->getOrder()=='ASC'){
				array_multisort( $file_statsize, SORT_ASC , $file_name, SORT_ASC, $this->filesList);
			} else {
				array_multisort( $file_statsize, SORT_DESC , $file_name, SORT_ASC, $this->filesList);
			}
		} elseif ($this->mFilter->getSort()=='file_type') {
			if ($this->mFilter->getOrder()=='ASC'){
				array_multisort( $file_type, SORT_ASC , $file_name, SORT_ASC, $this->filesList);
			} else {
				array_multisort( $file_type, SORT_DESC , $file_name, SORT_ASC, $this->filesList);
			}
		} elseif ($this->mFilter->getSort()=='file_time') {
			if ($this->mFilter->getOrder()=='ASC'){
				array_multisort( $time_stamp, SORT_ASC , $file_name, SORT_ASC, $this->filesList);
			} else {
				array_multisort( $time_stamp, SORT_DESC , $file_name, SORT_ASC, $this->filesList);
			}
		} else {
			if ($this->mFilter->getOrder()=='ASC'){
				// default file_name
				sort($this->filesList);
			} else {
				array_multisort($file_name, SORT_DESC, $this->filesList);
			}
		}
		$this->filesList = array_slice ($this->filesList, $this->_getStart(), $this->perpage);

		if ( $this->perpage < $this->filesCount ) {
			// page navi
			$this->mPagenavi = $this->_getPageNavi();
			// add sort parameter
			if ( abs($this->_getSort()) > 0) {
				$this->mPagenavi->addExtra('sort', $this->_getSort());
			}
			// add sort parameter
			if ( $this->currentPath != '') {
				$this->mPagenavi->addExtra('path', $this->currentPath);
			}
		}

		// add moduleHeader
		$this->moduleHeader .='<link rel="stylesheet" type="text/css" href="'. XOOPS_URL. '/modules/fileManager/js/lightview/lightview.css" />'."\n";
		$this->moduleHeader .='<script type="text/javascript" src="'. XOOPS_URL. '/modules/fileManager/js/lightview/prototype.js"></script>'."\n";
		$this->moduleHeader .='<script type="text/javascript" src="'. XOOPS_URL. '/modules/fileManager/js/lightview/lightview.js"></script>'."\n";
		$this->moduleHeader .='<script type="text/javascript" src="'. XOOPS_URL. '/modules/fileManager/js/scriptaculous/scriptaculous.js"></script>'."\n";

		return CONTENTS_FRAME_VIEW_INDEX;
	}

	function executeViewIndex(&$controller, &$xoopsUser, &$render)
	{
		// for menu

		// Initial setting
		$root =& XCube_Root::getSingleton();
		$this->menuDescription = _AD_FILEMANAGER_MAIN_DSC ;

		// set template
		$render->setTemplateName('fileManager_index.html');
		$render->setAttribute('config'  , $this->mConfig);

		// module info ( /admin/include/functions.php )
		$render->setAttribute('module_info'   , getModuleInfo());

		// haeder menu
		$render->setAttribute('moduleHeader'  , $this->moduleHeader);
		$render->setAttribute('bread_crumbs'  , $this->breadCrumbs);
		$render->setAttribute('confirm_mssage', $this->confirmMssage);

		// page navi & sort navi
		$render->setAttribute("sortNavi"      , $this->_getPageNavi());
		$render->setAttribute("sortNaviAdd"   , $this->_getPath());
		$render->setAttribute('pageNavi'      , $this->mPagenavi);

		// use ffmpeg flag
		$render->setAttribute('ffmpeguse'     , $this->mConfig['ffmpeguse']);

		// parh & file
		$render->setAttribute('isopen_folder' , $this->isOpenFolder);
		$render->setAttribute('current_path'  , $this->currentPath);
		$render->setAttribute('dir_list'      , $this->dirList);
		$render->setAttribute('files_list'    , $this->filesList);
		$render->setAttribute('files_count'   , $this->filesCount);
		$render->setAttribute('thumb_size'    , $this->imageThumbSize);
		$render->setAttribute('path_array'    , $this->pathNameArray);
	}

	// TODO this function is reary need ??
	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeRedirect('index.php', 1, _AD_FILEMANAGER_NOTFOUND);
	}

}
?>