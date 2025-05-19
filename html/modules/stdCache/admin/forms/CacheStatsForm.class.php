<?php
/**
 * Standard cache - Module for XCL
 * CacheStatsForm.class.php
 *
 * @package    stdCache
 * @author     Nuno Luciano (aka gigamaster) XCL/PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    2.5.0 Release: XCL
 * @link       http://github.com/xoopscube/
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';

class CacheStatsForm extends XCube_ActionForm
{
    /**
     * If the request is GET, never return token name
     * This allows an action to have multiple views
     */
    public function getTokenName()
    {
        // This form is used for the stats page
        // adding a form on the stats page and submit via POST
        // requires a token for CSRF protection
        if ('POST' == xoops_getenv('REQUEST_METHOD')) {
            return 'module.stdCache.CacheStatsForm.TOKEN';
        } else {
            // For GET requests (just viewing the stats page), no token is needed
            return null;
        }
    }

    /**
     * Get the error message for token validation failure
     * Returning null means the framework's default token error handling is used
     * or return a specific message like _MD_LEGACY_ERROR_TOKEN,
     * but that would require loading the legacy language file.
     */
    public function getTokenErrorMessage()
    {
        return null; // Use default framework token error message if any
         // Alternative if load legacy language
        // return defined('_MD_LEGACY_ERROR_TOKEN') ? _MD_LEGACY_ERROR_TOKEN : 'Token error.';
    }

    public function prepare()
    {
        //
        // Set form properties
        //
        $this->mFormProperties['refresh'] = new XCube_BoolProperty('refresh');
        $this->mFormProperties['submit'] = new XCube_BoolProperty('submit');
    }
    
}
