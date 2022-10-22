<?php
/**
 * @package legacyRender
 * @version $Id: TplsetEditForm.class.php,v 1.2 2007/06/07 05:27:57 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class LegacyRender_TplsetEditForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.legacyRender.TplsetEditForm.TOKEN' . $this->get('tplset_id');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['tplset_id'] =new XCube_IntProperty('tplset_id');
        $this->mFormProperties['tplset_desc'] =new XCube_StringProperty('tplset_desc');
        $this->mFormProperties['tplset_credits'] =new XCube_TextProperty('tplset_credits');

        //
        // Set field properties
        //
        $this->mFieldProperties['tplset_id'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['tplset_id']->setDependsByArray(['required']);
        $this->mFieldProperties['tplset_id']->addMessage('required', _AD_LEGACYRENDER_ERROR_REQUIRED, _AD_LEGACYRENDER_LANG_TPLSET_ID);

        $this->mFieldProperties['tplset_desc'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['tplset_desc']->setDependsByArray(['maxlength']);
        $this->mFieldProperties['tplset_desc']->addMessage('maxlength', _AD_LEGACYRENDER_ERROR_MAXLENGTH, _AD_LEGACYRENDER_LANG_TPLSET_DESC, '191');
        $this->mFieldProperties['tplset_desc']->addVar('maxlength', 191);
    }

    public function load(&$obj)
    {
        $this->set('tplset_id', $obj->get('tplset_id'));
        $this->set('tplset_desc', $obj->get('tplset_desc'));
        $this->set('tplset_credits', $obj->get('tplset_credits'));
    }

    public function update(&$obj)
    {
        $obj->set('tplset_id', $this->get('tplset_id'));
        $obj->set('tplset_desc', $this->get('tplset_desc'));
        $obj->set('tplset_credits', $this->get('tplset_credits'));
    }
}
