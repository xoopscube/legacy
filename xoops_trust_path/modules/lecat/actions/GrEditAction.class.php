<?php
/**
 * @file
 * @package lecat
 * @version $Id$
**/

if(!defined('XOOPS_ROOT_PATH'))
{
    exit;
}

require_once LECAT_TRUST_PATH . '/class/AbstractEditAction.class.php';

/**
 * Lecat_GrEditAction
**/
class Lecat_GrEditAction extends Lecat_AbstractEditAction
{
    /**
     * _getId
     * 
     * @param   void
     * 
     * @return  int
    **/
    protected function _getId()
    {
        return $this->mRoot->mContext->mRequest->getRequest('gr_id');
    }

    /**
     * &_getHandler
     * 
     * @param   void
     * 
     * @return  Lecat_GrHandler
    **/
    protected function &_getHandler()
    {
        $handler =& $this->mAsset->getObject('handler', 'gr');
        return $handler;
    }

    /**
     * &isAdminOnly
     * 
     * @param   void
     * 
     * @return  bool
    **/
	function isAdminOnly()
	{
		return true;
	}

    /**
     * _setupActionForm
     * 
     * @param   void
     * 
     * @return  void
    **/
    protected function _setupActionForm()
    {
        // $this->mActionForm =& new Lecat_GrEditForm();
        $this->mActionForm =& $this->mAsset->getObject('form', 'gr',false,'edit');
        $this->mActionForm->prepare();
    }

    /**
     * prepare
     * 
     * @param   void
     * 
     * @return  void
    **/
	public function prepare()
	{
		parent::prepare();
	
		if ($this->mObject->isNew()) {
			$actionArr[0]['key'] = "viewer";
			$actionArr[0]['title'] = _MD_LECAT_LANG_VIEWER;
			$actionArr[0]['default'] = 1;
			$actionArr[1]['key'] = "poster";
			$actionArr[1]['title'] = _MD_LECAT_LANG_POSTER;
			$actionArr[1]['default'] = 1;
			$actionArr[2]['key'] = "manager";
			$actionArr[2]['title'] = _MD_LECAT_LANG_MANAGER;
			$actionArr[2]['default'] = "";
		}
		else{
			$actionArr = $this->mObject->getDefaultPermissionList();
		}
	
		$this->mActions = $actionArr;
	}


    /**
     * executeViewInput
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewInput(/*** XCube_RenderTarget ***/ &$render)
    {
        $render->setTemplateName($this->mAsset->mDirname . '_gr_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
	
		$render->setAttribute('actions', $this->mActions);
    }

    /**
     * setHeaderScript
     * 
     * @param   void
     * 
     * @return  void
    **/
    public function setHeaderScript()
	{
		$headerScript = $this->mRoot->mContext->getAttribute('headerScript');
		$headerScript->addStylesheet($this->_getStylesheet());
		$headerScript->addScript('actionsCounter='. count($this->mActions). ';',false);
		$headerScript->addScript('function addActionKeyForm() {$("#permitOptions").append("<tr><td><input type=\'text\' id=\'legacy_xoopsform_actions_key["+actionsCounter+"]\' value=\'\' name=\'actions_key["+actionsCounter+"]\'></td><td><input type=\'text\' id=\'legacy_xoopsform_actions_title["+actionsCounter+"]\' value=\'\' name=\'actions_title["+actionsCounter+"]\'></td><td><input type=\'checkbox\' id=\'legacy_xoopsform_actions_default["+actionsCounter+"]\' value=\'1\' name=\'actions_default["+actionsCounter+"]\'></td></tr>");actionsCounter++;}', false);
	}

    /**
     * executeViewSuccess
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewSuccess(/*** XCube_RenderTarget ***/ &$render)
    {
        $this->mRoot->mController->executeForward('./index.php?action=GrView&gr_id='.$this->mObject->getShow('gr_id'));
    }

    /**
     * executeViewError
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewError(/*** XCube_RenderTarget ***/ &$render)
    {
        $this->mRoot->mController->executeRedirect('./index.php?action=GrList', 1, _MD_LECAT_ERROR_DBUPDATE_FAILED);
    }

    /**
     * executeViewCancel
     * 
     * @param   XCube_RenderTarget  &$render
     * 
     * @return  void
    **/
    public function executeViewCancel(/*** XCube_RenderTarget ***/ &$render)
    {
        $this->mRoot->mController->executeForward('./index.php?action=GrList');
    }
}

?>
