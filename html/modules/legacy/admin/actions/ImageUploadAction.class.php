<?php

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once XOOPS_MODULE_PATH . "/legacy/class/AbstractEditAction.class.php";
require_once XOOPS_MODULE_PATH . "/legacy/admin/forms/ImageUploadForm.class.php";

class Legacy_ImageUploadAction extends Legacy_Action
{
	var $mActionForm = null;
	var $mCategory = null;
	var $mErrorMessages = array();
	var $mAllowedExts = array('gif'=>'image/gif', 'jpg'=>'image/jpeg', 'jpeg'=>'image/jpeg', 'png' =>'image/png') ;
	
	function prepare(&$controller, &$xoopsUser)
	{
		$this->mActionForm =new Legacy_ImageUploadForm();
		$this->mActionForm->prepare();
	}
	
	function getDefaultView(&$controller, &$xoopsUser)
	{
		return LEGACY_FRAME_VIEW_INPUT;
	}
	
	function _addErrorMessage($msg)
	{
		$this->mErrorMessages[] = $msg;
	}
	
	function execute(&$controller, &$xoopsUser)
	{
		$form_cancel = $controller->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
		if ($form_cancel != null) {
			return LEGACY_FRAME_VIEW_CANCEL;
		}

		$this->mActionForm->fetch();
		$this->mActionForm->validate();
		
		if ($this->mActionForm->hasError()) {
			return $this->getDefaultView($controller, $xoopsUser);
		}

		$t_imgcat_id = $this->mActionForm->get('imgcat_id');

		$formFile = $this->mActionForm->get('upload');		
		$formFileExt = $formFile->getExtension();
		$files = array();
		$targetimages = array();

		if ( strtolower($formFileExt) == "zip" ) {
		If ( !file_exists(XOOPS_ROOT_PATH . "/class/Archive_Zip.php") ) {
			return LEGACY_FRAME_VIEW_ERROR;
		}
		require_once XOOPS_ROOT_PATH . "/class/Archive_Zip.php" ;
		$zip = new Archive_Zip($formFile->_mTmpFileName) ;
		$files = $zip->extract( array( 'extract_as_string' => true ) ) ;
		if( ! is_array( @$files ) ) {
		return LEGACY_FRAME_VIEW_ERROR;
		}
		if (!$this->_fetchZipTargetImages($files, $targetimages)) {
			return LEGACY_FRAME_VIEW_ERROR;
		}		
		}//if zip end
		else { 
		require_once XOOPS_ROOT_PATH . "/class/class.tar.php";
		$tar =new tar();
		$tar->openTar($formFile->_mTmpFileName);
		if (!is_array( @$tar->files)) {
			return LEGACY_FRAME_VIEW_ERROR;
		}
		if (!$this->_fetchTarTargetImages($tar->files, $targetimages)) {
			return LEGACY_FRAME_VIEW_ERROR;
		}		
		}//end tar
						
		if (!$this->_saveTargetImages($targetimages, $t_imgcat_id)) {
			return LEGACY_FRAME_VIEW_ERROR;
		}
		return LEGACY_FRAME_VIEW_SUCCESS;

	}
	
	function _fetchZipTargetImages(&$files, &$targetimages)
	{
		foreach ($files as $file) {
		$file_pos = strrpos( $file['filename'] , '/' ) ;
		if ( $file_pos !== false ) {
		$file['filename'] = substr( $file['filename'] , $file_pos+1 );
		}
		if ( !empty($file['filename']) && preg_match("/(.*)\.(gif|jpg|jpeg|png)$/i", $file['filename'], $match) && !preg_match('/[' . preg_quote('\/:*?"<>|','/') . ']/', $file['filename'])) {
		$targetimages[] = array('name' => $file['filename'], 'content' => $file['content']);
		}
		unset($file);
		}		
		return true;
	}

	function _fetchTarTargetImages(&$files, &$targetimages)
	{
		foreach ($files as $id => $info) {
		$file_pos = strrpos( $info['name'] , '/' ) ;
		if ( $file_pos !== false ) {
		$info['name'] = substr( $info['name'] , $file_pos+1 );
		}
		if ( !empty($info['name']) && preg_match("/(.*)\.(gif|jpg|jpeg|png)$/i", $info['name'], $match) && !preg_match('/[' . preg_quote('\/:*?"<>|','/') . ']/', $info['name'])) {
		$targetimages[] = array('name' => $info['name'], 'content' => $info['file']);
		}
		unset($info);
		}		
		return true;
	}

	function _saveTargetImages(&$targetimages, $t_imgcat_id)
	{
		if (count($targetimages) == 0) {
			return true;
		}
		
		$imgcathandler =& xoops_getmodulehandler('imagecategory', 'legacy');
		$t_category = & $imgcathandler->get($t_imgcat_id);
		$t_category_type = $t_category->get('imgcat_storetype');
		$imagehandler =& xoops_getmodulehandler('image');
		
		if ( strtolower($t_category_type) == "file" ) {
		for ($i = 0; $i < count($targetimages); $i++) {
				$ext_pos = strrpos( $targetimages[$i]['name'] , '.' ) ;
				if( $ext_pos === false ) continue ;
				$ext = strtolower( substr( $targetimages[$i]['name'] , $ext_pos + 1 ) ) ;
				if( empty( $this->mAllowedExts[$ext] ) ) continue ;
				$file_name = substr( $targetimages[$i]['name'] , 0 , $ext_pos ) ;
				$save_file_name = uniqid( 'img' ) . '.' . $ext ;
				$filehandle = fopen( XOOPS_UPLOAD_PATH.'/'.$save_file_name , "w" ) ;
				if( ! $filehandle ) {
				$this->_addErrorMessage(XCube_Utils::formatMessage(_AD_LEGACY_ERROR_COULD_NOT_SAVE_IMAGE_FILE, $file_name));
				continue ;
				}
				if ( !@fwrite($filehandle, $targetimages[$i]['content']) ) {
				$this->_addErrorMessage(XCube_Utils::formatMessage(_AD_LEGACY_ERROR_COULD_NOT_SAVE_IMAGE_FILE, $file_name));
				@fclose( $filehandle ) ;
				continue;				
				};
				@fclose( $filehandle ) ;

				$image =& $imagehandler->create();
				$image->set('image_nicename', $file_name);
				$image->set('image_name', $save_file_name);
				$image->set('image_mimetype', $this->mAllowedExts[$ext]);
				$image->set('image_display', 1);
				$image->set('imgcat_id', $t_imgcat_id);

				if (!$imagehandler->insert($image)) {
					$this->_addErrorMessage(XCube_Utils::formatMessage(_AD_LEGACY_ERROR_COULD_NOT_SAVE_IMAGE_FILE, $file_name));
				}
				unset($image);
		} //end of for
		} //end of if
		elseif (strtolower($t_category_type) == "db") {
		for ($i = 0; $i < count($targetimages); $i++) {
				$ext_pos = strrpos( $targetimages[$i]['name'] , '.' ) ;
				if( $ext_pos === false ) continue ;
				$ext = strtolower( substr( $targetimages[$i]['name'] , $ext_pos + 1 ) ) ;
				if( empty( $this->mAllowedExts[$ext] ) ) continue ;
				$file_name = substr( $targetimages[$i]['name'] , 0 , $ext_pos ) ;
				$save_file_name = uniqid( 'img' ) . '.' . $ext ;
				//
				$image =& $imagehandler->create();
				$image->set('image_nicename', $file_name);
				$image->set('image_name', $save_file_name);
				$image->set('image_mimetype', $this->mAllowedExts[$ext]);
				$image->set('image_display', 1);
				$image->set('imgcat_id', $t_imgcat_id);
				$image->loadImageBody();
				if (!is_object($image->mImageBody)) {
					$image->mImageBody =& $image->createImageBody();
				}					
				$image->mImageBody->set('image_body', $targetimages[$i]['content']);

				if (!$imagehandler->insert($image)) {
					$this->_addErrorMessage(XCube_Utils::formatMessage(_AD_LEGACY_ERROR_COULD_NOT_SAVE_IMAGE_FILE, $file_name));
				}
				unset($image);
		} //end of for
		} //end of elseif 
		return true;
	}
	
	function executeViewInput(&$controller, &$xoopsUser, &$render)
	{
		$render->setTemplateName("image_upload.html");
		$render->setAttribute('actionForm', $this->mActionForm);
		//image category
		$handler =& xoops_getmodulehandler('imagecategory', 'legacy');
		$cat_id = $controller->mRoot->mContext->mRequest->getRequest('imgcat_id');
		if (isset($cat_id)) {
		$this->mCategory =& $handler->get($cat_id);
		$render->setAttribute("category", $this->mCategory);		
		}
		$categoryArr =& $handler->getObjects();
		$render->setAttribute('categoryArr', $categoryArr);
	}

	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		$controller->executeForward("./index.php?action=ImageList&imgcat_id=".$this->mActionForm->get('imgcat_id'));
	}

	function executeViewError(&$controller, &$xoopsUser, &$render)
	{
		if (count($this->mErrorMessages) == 0) {
			$controller->executeRedirect("./index.php?action=ImageList&imgcat_id=".$this->mActionForm->get('imgcat_id'), 1, _AD_LEGACY_ERROR_DBUPDATE_FAILED);
		}
		else {
			$render->setTemplateName("image_upload_error.html");
			$render->setAttribute('errorMessages', $this->mErrorMessages);
		}
	}
	
	function executeViewCancel(&$controller, &$xoopsUser, &$render)
	{
		if ( $this->mCategory ) {
		$controller->executeForward("./index.php?action=ImageList&imgcat_id=".$this->mCategory->get('imgcat_id'));
		}
		else {
		$controller->executeForward('./index.php?action=ImagecategoryList');
		}
	}
}

?>
