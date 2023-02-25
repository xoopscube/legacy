<?php
/**
 *
 * @package Legacy
 * @version $Id: SearchShowallForm.class.php,v 1.3 2008/09/25 15:12:40 kilica Exp $
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

require_once XOOPS_MODULE_PATH . '/legacy/forms/SearchResultsForm.class.php';

class Legacy_SearchShowallForm extends Legacy_SearchResultsForm
{
    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['mid'] =new XCube_IntProperty('mid');
        $this->mFormProperties['andor'] =new XCube_StringProperty('andor');
        $this->mFormProperties['query'] =new XCube_StringProperty('query');
        $this->mFormProperties['start'] =new XCube_IntProperty('start');

        //
        // Set field properties
        //
        $this->mFieldProperties['andor'] =new XCube_FieldProperty($this);
        $this->mFieldProperties['andor']->setDependsByArray(['mask']);
        $this->mFieldProperties['andor']->addMessage('mask', _MD_LEGACY_ERROR_MASK, _MD_LEGACY_LANG_ANDOR);
        $this->mFieldProperties['andor']->addVar('mask', '/^(AND|OR|exact)$/i');

        $this->set('start', 0);
    }

    public function update(&$params)
    {
        $params['queries'] = $this->mQueries;
        $params['andor'] = $this->get('andor');
        $params['maxhit'] = LEGACY_SEARCH_SHOWALL_MAXHIT;
        $params['start'] = $this->get('start');
    }
}
