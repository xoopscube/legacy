<?php
/**
 * @package legacyRender
 * @version $Id: TplfileCloneForm.class.php,v 1.1 2007/05/15 02:34:40 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_MODULE_PATH . '/legacyRender/admin/forms/TplfileEditForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class LegacyRender_TplfileCloneForm extends LegacyRender_TplfileEditForm
{
    public function getTokenName()
    {
        return 'module.legacyRender.TplfileCloneForm.TOKEN';
    }

    public function prepare()
    {
        parent::prepare();
        
        //
        // Set form properties
        //
        $this->mFormProperties['tpl_tplset'] =new XCube_StringProperty('tpl_tplset');
    
        //
        // Set field properties
        //
        $this->mFieldProperties['tpl_tplset'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['tpl_tplset']->setDependsByArray(['required']);
        $this->mFieldProperties['tpl_tplset']->addMessage('required', _AD_LEGACYRENDER_ERROR_REQUIRED, _AD_LEGACYRENDER_LANG_TPL_TPLSET);
    }
    
    public function validateTpl_tplset()
    {
        $tplset = $this->get('tpl_tplset');

        $handler =& xoops_getmodulehandler('tplset', 'legacyRender');
        $criteria =new Criteria('tplset_name', $this->get('tpl_tplset'));
        $objs =& $handler->getObjects($criteria);
        
        if (0 == (is_countable($objs) ? count($objs) : 0)) {
            $this->addErrorMessage(_AD_LEGACYRENDER_ERROR_TPLSET_WRONG);
        }
    }
    
    public function load(&$obj)
    {
        parent::load($obj);
        $this->set('tpl_tplset', $obj->get('tpl_tplset'));
    }

    public function update(&$obj)
    {
        $obj->loadSource();

        $obj->set('tpl_desc', $this->get('tpl_desc'));
        $obj->set('tpl_lastmodified', time());

        $obj->Source->set('tpl_source', $this->get('tpl_source'));
    }
}
