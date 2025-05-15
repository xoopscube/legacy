<?php
/**
 * Standard cache - Module for XCL
 * CacheStatsForm.class.php
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8 
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    Release: XCL v2.5.0
 * @link       http://github.com/xoopscube/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';

class CacheStatsForm extends XCube_ActionForm
{
    /**
     * TODO consolidate actionForm across views
     * If the request is GET, never return token name.
     * This allows an action to have multiple views.
     */
    public function getTokenName()
    {
        if ('POST' == xoops_getenv('REQUEST_METHOD')) {
            return 'module.stdCache.CacheStatsForm.TOKEN';
        } else {
            return null;
        }
    }

    /**
     * Display the confirmation page, don't show CSRF error.
     * Always return null.
     */
    public function getTokenErrorMessage()
    {
        return null;
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['cache_limit'] = new XCube_IntProperty('cache_limit');
        $this->mFormProperties['refresh'] = new XCube_BoolProperty('refresh');
        $this->mFormProperties['submit'] = new XCube_BoolProperty('submit');
        
        //
        // Set field properties
        //
        $this->mFieldProperties['cache_limit'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['cache_limit']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['cache_limit']->addMessage('required', _MD_LEGACY_ERROR_REQUIRED, _AD_STDCACHE_CACHE_LIMIT);
        $this->mFieldProperties['cache_limit']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_STDCACHE_CACHE_LIMIT);
        $this->mFieldProperties['cache_limit']->addVar('min', '1');
        $this->mFieldProperties['cache_limit']->addVar('max', '10');
    }
}
