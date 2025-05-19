<?php
/**
 * Standard cache - Module for XCL
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

class stdCache_CacheClearForm extends XCube_ActionForm
{
    /**
     * Get the token name for CSRF protection.
     * Use a static token name for this form type.
     *
     * @return string Token name
     */
    public function getTokenName()
    {
        // Return a static token name for this form type
        return 'module.stdCache.CacheClearForm.TOKEN';
    }

    /**
     * Get the error message for token validation failure.
     * Returning null means the framework's default token error handling is used.
     * You could return a specific message like _MD_LEGACY_ERROR_TOKEN if desired,
     * but that would require loading the legacy language file.
     */
    public function getTokenErrorMessage()
    {
        // Use default framework token error message if any
        return null;
        // return defined('_MD_LEGACY_ERROR_TOKEN') ? _MD_LEGACY_ERROR_TOKEN : 'Token error.'; // Alternative if you load legacy language
    }


    public function prepare()
    {
        //
        // Set form properties for different cache types
        //
        $this->mFormProperties['confirm'] = new XCube_BoolProperty('confirm');
        $this->mFormProperties['clear_smarty_cache'] = new XCube_BoolProperty('clear_smarty_cache');
        $this->mFormProperties['clear_compiled_templates'] = new XCube_BoolProperty('clear_compiled_templates');
        $this->mFormProperties['clear_logs'] = new XCube_BoolProperty('clear_logs');
        $this->mFormProperties['clear_uploads'] = new XCube_BoolProperty('clear_uploads');
        $this->mFormProperties['clear_age'] = new XCube_IntProperty('clear_age'); // No specific validation needed here if values are fixed by radio buttons

        //
        // Set field properties
        //
        // Confirmation checkbox
        $this->mFieldProperties['confirm'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['confirm']->setDependsByArray(['required']);
        // Ensure this language constant exists and is loaded
        $this->mFieldProperties['confirm']->addMessage('required', defined('_AD_STDCACHE_CLEAR_CONFIRM_REQUIRED') ? _AD_STDCACHE_CLEAR_CONFIRM_REQUIRED : 'You must confirm to clear the cache.');

        // No specific validators needed for bool properties beyond their type
        // unless you have specific rules like "at least one must be checked"

        // Initialize form values (default selections)
        // loadDefaults is called in getDefaultView of the action, not here in prepare
        // $this->loadDefaults();
    }

    /**
     * Load default values into the form properties
     */
    public function loadDefaults()
    {
        $this->set('confirm', false);
        $this->set('clear_smarty_cache', false); // Uncheck cache to prevent custom localization from delete
        $this->set('clear_compiled_templates', true); // Default to select compiled templates
        $this->set('clear_logs', false);
        $this->set('clear_uploads', false);
        $this->set('clear_age', 0); // Default to "All files"
    }

    /**
     * Add validation method.
     * @return bool True if validation passes, false otherwise
     */
    public function validate()
    {
        parent::validate(); // Call parent's validation first (handles token and field properties)

        // Ensure at least one cache type is selected for clearing
        if (!$this->get('clear_smarty_cache') &&
            !$this->get('clear_compiled_templates') &&
            !$this->get('clear_logs') &&
            !$this->get('clear_uploads') ) {
            // Ensure this language constant exists and is loaded
            $this->addErrorMessage(defined('_AD_STDCACHE_SELECT_CACHE_TYPE_TO_CLEAR') ? _AD_STDCACHE_SELECT_CACHE_TYPE_TO_CLEAR : 'Please select at least one cache type to clear.');
        }

        return !$this->hasError(); // Return true if no errors
    }

    // No need for update() method as this form doesn't update a single object directly
}
