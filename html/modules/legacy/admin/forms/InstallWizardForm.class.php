<?php
/**
 * InstallWizardForm.class.php
 * @package    Legacy
 * @version    XCL 2.3.3
 * @author     Other authors  gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';

class Legacy_InstallWizardForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.legacy.InstallWizardForm.TOKEN.' . $this->get('dirname');
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['dirname'] =new XCube_StringProperty('dirname');
        $this->mFormProperties['agree'] =new XCube_BoolProperty('agree');

        //
        // Set field properties
        //
        $this->mFieldProperties['agree'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['agree']->setDependsByArray(['min']);
        $this->mFieldProperties['agree']->addMessage('min', _AD_LEGACY_ERROR_PLEASE_AGREE);
        $this->mFieldProperties['agree']->addVar('min', '1');
    }

    public function load(&$obj)
    {
        $this->set('dirname', $obj->get('dirname'));
    }

    public function update(&$obj)
    {
        $obj->set('dirname', $this->get('dirname'));
    }
}
