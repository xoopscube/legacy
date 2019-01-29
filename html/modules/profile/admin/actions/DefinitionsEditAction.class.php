<?php
/**
 * @file
 * @package profile
 * @version $Id$
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . "/profile/class/AbstractEditAction.class.php";

class Profile_Admin_DefinitionsEditAction extends Profile_AbstractEditAction
{
    public $mTypeArr = array();
    public $mValidationArr = array();

    /**
     * @protected
     */
    public function _getId()
    {
        return intval(xoops_getrequest('field_id'));
    }

    /**
     * @protected
     */
    public function &_getHandler()
    {
        $handler =& $this->mAsset->load('handler', "definitions");
        return $handler;
    }

    /**
     * @protected
     */
    public function _setupActionForm()
    {
        // $this->mActionForm =new Profile_Admin_DefinitionsEditForm();
        $this->mActionForm =& $this->mAsset->create('form', "admin.edit_definitions");
        $this->mActionForm->prepare();
    }

    /**
     * _setHeaderScript
     * 
     * @param   void
     * 
     * @return  void
    **/
    protected function _setHeaderScript()
    {
        $headerScript = $this->mRoot->mContext->getAttribute('headerScript');
        $type = $this->mActionForm->get('type');
        $headerScript->addStylesheet('/modules/profile/style.css');
        $headerScript->addScript('
$(".optionField input, .optionField select, .optionField textarea").attr("disabled", "disabled");
$(".optionField").addClass("hideOption");
$("#fieldtype_'. $type .'").removeClass("hideOption");
$("#fieldtype_'. $type .' input, #fieldtype_'. $type .' select, #fieldtype_'. $type .' textarea").removeAttr("disabled");
$("#legacy_xoopsform_type").change(function(){
  $(".optionField").addClass("hideOption");
  $("#fieldtype_"+$(this).val()).removeClass("hideOption");
  $("#fieldtype_"+$(this).val()+" input, #fieldtype_"+$(this).val()+" select, #fieldtype_"+$(this).val()+" textarea").removeAttr("disabled");
});'
        );
    }

    /**
     * @public
     */
    public function prepare()
    {
        parent::prepare();
        $handler =& $this->_getHandler();
        $this->mTypeArr = $handler->getTypeList();
        $this->mValidationArr = $handler->getValidationList();
    }

    /**
     * @public
     */
    public function executeViewInput(&$render)
    {
        $gHandler =& xoops_gethandler('group');
    
        $render->setTemplateName("definitions_edit.html");
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
        $render->setAttribute('groupArr', $gHandler->getObjects());
        $render->setAttribute('accessArr', explode(',', $this->mObject->get('access')));
        $render->setAttribute('typeArr', $this->mTypeArr);
        $render->setAttribute('validationArr', $this->mValidationArr);
        $this->_setHeaderScript();
    }

    /**
     * @public
     */
    // !Fix compatibility with Profile_AbstractAction::executeViewSuccess(&$controller, &$render) in file /modules/profile/class/AbstractionAction.class.php line 62
    public function executeViewSuccess(&$controller, &$render) 
    // public function executeViewSuccess(&$render)
    {
        $this->mRoot->mController->executeForward("./index.php?action=DefinitionsList");
    }

    /**
     * @public
     */
    public function executeViewError(&$render)
    {
        $this->mRoot->mController->executeRedirect("./index.php?action=DefinitionsList", 1, _MD_PROFILE_ERROR_DBUPDATE_FAILED);
    }

    /**
     * @public
     */
    public function executeViewCancel(&$render)
    {
        $this->mRoot->mController->executeForward("./index.php?action=DefinitionsList");
    }
}
