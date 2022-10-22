<?php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/user/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/user/admin/forms/AvatarUploadForm.class.php';

class User_AvatarUploadAction extends User_Action
{
    public $mActionForm = null;
    public $mErrorMessages = [];
    public $mAllowedExts = ['gif' =>'image/gif', 'jpg' =>'image/jpeg', 'jpeg' =>'image/jpeg', 'png' =>'image/png'];

    //public function prepare(&$controller, &$xoopsUser)
    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        $this->mActionForm =new User_AvatarUploadForm();
        $this->mActionForm->prepare();
    }

    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return USER_FRAME_VIEW_INPUT;
    }

    public function _addErrorMessage($msg)
    {
        $this->mErrorMessages[] = $msg;
    }

    public function execute(&$controller, &$xoopsUser)
    {
        $form_cancel = $controller->mRoot->mContext->mRequest->getRequest('_form_control_cancel');
        if (null != $form_cancel) {
            return USER_FRAME_VIEW_CANCEL;
        }

        $this->mActionForm->fetch();
        $this->mActionForm->validate();

        if ($this->mActionForm->hasError()) {
            return $this->getDefaultView($controller, $xoopsUser);
        }

        $formFile = $this->mActionForm->get('upload');
        $formFileExt = $formFile->getExtension();
        $files = [];
        $avatarimages = [];

        if ('zip' == strtolower($formFileExt)) {
            if (!file_exists(XOOPS_ROOT_PATH . '/class/Archive_Zip.php')) {
                return USER_FRAME_VIEW_ERROR;
            }
            require_once XOOPS_ROOT_PATH . '/class/Archive_Zip.php';
            $zip = new Archive_Zip($formFile->_mTmpFileName) ;
            $files = $zip->extract(['extract_as_string' => true]) ;
            if (! is_array(@$files)) {
                return USER_FRAME_VIEW_ERROR;
            }
            if (!$this->_fetchZipAvatarImages($files, $avatarimages)) {
                return USER_FRAME_VIEW_ERROR;
            }
        }//if zip end
        else {
            require_once XOOPS_ROOT_PATH . '/class/class.tar.php';
            $tar =new tar();
            $tar->openTar($formFile->_mTmpFileName);
            if (!is_array(@$tar->files)) {
                return USER_FRAME_VIEW_ERROR;
            }
            if (!$this->_fetchTarAvatarImages($tar->files, $avatarimages)) {
                return USER_FRAME_VIEW_ERROR;
            }
        }//end tar

        if (!$this->_saveAvatarImages($avatarimages)) {
            return USER_FRAME_VIEW_ERROR;
        }
        return USER_FRAME_VIEW_SUCCESS;
    }

    public function _fetchZipAvatarImages(&$files, &$avatarimages)
    {
        foreach ($files as $file) {
            $file_pos = strrpos($file['filename'], '/') ;
            if (false !== $file_pos) {
                $file['filename'] = substr($file['filename'], $file_pos+1);
            }
            if (!empty($file['filename']) && preg_match("/(.*)\.(gif|jpg|jpeg|png)$/i", $file['filename'], $match) && !preg_match('/[' . preg_quote('\/:*?"<>|', '/') . ']/', $file['filename'])) {
                $avatarimages[] = ['name' => $file['filename'], 'content' => $file['content']];
            }
            unset($file);
        }
        return true;
    }

    public function _fetchTarAvatarImages(&$files, &$avatarimages)
    {
        foreach ($files as $id => $info) {
            $file_pos = strrpos($info['name'], '/') ;
            if (false !== $file_pos) {
                $info['name'] = substr($info['name'], $file_pos+1);
            }
            if (!empty($info['name']) && preg_match("/(.*)\.(gif|jpg|jpeg|png)$/i", $info['name'], $match) && !preg_match('/[' . preg_quote('\/:*?"<>|', '/') . ']/', $info['name'])) {
                $avatarimages[] = ['name' => $info['name'], 'content' => $info['file']];
            }
            unset($info);
        }
        return true;
    }

    public function _saveAvatarImages(&$avatarimages)
    {
        if (0 == count($avatarimages)) {
            return true;
        }

        $avatarhandler =& xoops_getmodulehandler('avatar');

        for ($i = 0; $i < count($avatarimages); $i++) {
            $ext_pos = strrpos($avatarimages[$i]['name'], '.') ;
            if (false === $ext_pos) {
                continue ;
            }
            $ext = strtolower(substr($avatarimages[$i]['name'], $ext_pos + 1)) ;
            if (empty($this->mAllowedExts[$ext])) {
                continue ;
            }
            $file_name = substr($avatarimages[$i]['name'], 0, $ext_pos) ;
            $save_file_name = uniqid('savt') . '.' . $ext ;
            $filehandle = fopen(XOOPS_UPLOAD_PATH.'/'.$save_file_name, 'w') ;
            if (! $filehandle) {
                $this->_addErrorMessage(XCube_Utils::formatString(_AD_USER_ERROR_COULD_NOT_SAVE_AVATAR_FILE, $file_name));
                continue ;
            }
            if (!@fwrite($filehandle, $avatarimages[$i]['content'])) {
                $this->_addErrorMessage(XCube_Utils::formatString(_AD_USER_ERROR_COULD_NOT_SAVE_AVATAR_FILE, $file_name));
                @fclose($filehandle) ;
                continue;
            };
            @fclose($filehandle) ;

            $avatar =& $avatarhandler->create();
            $avatar->set('avatar_name', $file_name);
            $avatar->set('avatar_file', $save_file_name);
            $avatar->set('avatar_display', 1);
            $avatar->set('avatar_weight', 0);
            $avatar->set('avatar_type', 'S');
            $avatar->set('avatar_mimetype', $this->mAllowedExts[$ext]);

            if (!$avatarhandler->insert($avatar)) {
                $this->_addErrorMessage(XCube_Utils::formatString(_AD_USER_ERROR_COULD_NOT_SAVE_AVATAR_FILE, $file_name));
            }
            unset($avatar);
        }

        return true;
    }

    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('avatar_upload.html');
        $render->setAttribute('actionForm', $this->mActionForm);
    }

    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=AvatarList');
    }

    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        if (0 == count($this->mErrorMessages)) {
            $controller->executeRedirect('./index.php?action=AvatarList', 1, _AD_USER_ERROR_DBUPDATE_FAILED);
        } else {
            $render->setTemplateName('avatar_upload_error.html');
            $render->setAttribute('errorMessages', $this->mErrorMessages);
        }
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=AvatarList');
    }
}
