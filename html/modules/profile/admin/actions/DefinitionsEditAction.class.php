<?php
/**
 * @package    profile
 * @version    XCL 2.3.3
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Original Author Kilica
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/profile/class/AbstractEditAction.class.php';

class Profile_Admin_DefinitionsEditAction extends Profile_AbstractEditAction
{
    public array $mTypeArr = [];
    public array $mValidationArr = [];

    /**
     * @protected
     */
    public function _getId()
    {
        return (int)xoops_getrequest('field_id');
    }

    /**
     * @protected
     */
    public function &_getHandler()
    {
        $handler =& $this->mAsset->load('handler', 'definitions');
        return $handler;
    }

    /**
     * @protected
     */
    public function _setupActionForm()
    {
        // $this->mActionForm =new Profile_Admin_DefinitionsEditForm();
        $this->mActionForm =& $this->mAsset->create('form', 'admin.edit_definitions');
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
     * @param $render
     */
    public function executeViewInput(&$render)
    {
        $gHandler =& xoops_gethandler('group');

        $render->setTemplateName('definitions_edit.html');
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
     * @param $controller
     * @param $render
     */
    public function executeViewSuccess(&$controller, &$render)
    {
        $this->mRoot->mController->executeForward('./index.php?action=DefinitionsList');
    }

    /**
     * @public
     * @param $render
     */
    public function executeViewError(&$render)
    {
        $this->mRoot->mController->executeRedirect('./index.php?action=DefinitionsList', 1, _MD_PROFILE_ERROR_DBUPDATE_FAILED);
    }

    /**
     * @public
     * @param $render
     */
    public function executeViewCancel(&$render)
    {
        $this->mRoot->mController->executeForward('./index.php?action=DefinitionsList');
    }
}
