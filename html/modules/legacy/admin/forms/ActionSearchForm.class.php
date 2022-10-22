<?php
/**
 * ActionSearchForm.class.php
 * @package    Legacy
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Kilica, 2008/09/25
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    GPL 2.0
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';

class Legacy_ActionSearchForm extends XCube_ActionForm
{
    public $mState = null;

    public function prepare()
    {
        $this->mFormProperties['keywords']=new XCube_StringProperty('keywords');

        // set fields
        $this->mFieldProperties['keywords']=new XCube_FieldProperty($this);
        $this->mFieldProperties['keywords']->setDependsByArray(['required']);
        $this->mFieldProperties['keywords']->addMessage('required', _AD_LEGACY_ERROR_SEARCH_REQUIRED);
    }

    public function fetch()
    {
        parent::fetch();
        $this->set('keywords', trim($this->get('keywords')));
    }
}
