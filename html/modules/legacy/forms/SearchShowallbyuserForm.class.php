<?php
/**
 *
 * @package Legacy
 * @version $Id: SearchShowallbyuserForm.class.php,v 1.4 2008/09/25 15:12:39 kilica Exp $
 * @copyright  (c) 2005-2025 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

require_once XOOPS_MODULE_PATH . '/legacy/forms/SearchShowallForm.class.php';

class Legacy_SearchShowallbyuserForm extends Legacy_SearchShowallForm
{
    public function prepare()
    {
        parent::prepare();

        //
        // Set form properties
        //
        $this->mFormProperties['uid'] =new XCube_IntProperty('uid');
        $this->mFormProperties['mid'] =new XCube_IntProperty('mid');
        $this->mFormProperties['start'] =new XCube_IntProperty('start');

        //
        // Set field properties
        //
        $this->mFieldProperties['uid'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['uid']->setDependsByArray(['required']);
        $this->mFieldProperties['uid']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_UID);

        $this->mFieldProperties['mid'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['mid']->setDependsByArray(['required']);
        $this->mFieldProperties['mid']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _MD_LEGACY_LANG_MID);
    }

    public function update(&$params)
    {
        $params['uid'] = $this->get('uid');
        $params['start'] = $this->get('start');

        if (defined('LEGACY_SEARCH_SHOWALL_MAXHIT')) {
            $params['maxhit'] = LEGACY_SEARCH_SHOWALL_MAXHIT;
        } else {
            $params['maxhit'] = 20;
        }
    }
}
