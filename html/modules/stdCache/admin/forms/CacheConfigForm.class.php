<?php
/**
 * Standard cache - Module for XCL
 * CacheConfigForm.class.php
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

class stdCache_CacheConfigForm extends XCube_ActionForm
{
    /**
     * Prepare form fields and validators
     */
    public function prepare()
    {
        //
        // Set form properties (defines the internal properties of the form object)
        // These should match the names of your config items
        //
        $this->mFormProperties['cache_limit_smarty'] = new XCube_IntProperty('cache_limit_smarty');
        $this->mFormProperties['cache_limit_alert_trigger'] = new XCube_IntProperty('cache_limit_alert_trigger');
        $this->mFormProperties['cache_limit_cleanup'] = new XCube_IntProperty('cache_limit_cleanup');
        $this->mFormProperties['cache_limit_compiled'] = new XCube_IntProperty('cache_limit_compiled');
        $this->mFormProperties['cache_limit_alert_enable'] = new XCube_IntProperty('cache_limit_alert_enable'); // Stored as 0/1

        //
        // Set field properties (defines validation rules and messages)
        //
        // Cache limit (in bytes)
        $this->mFieldProperties['cache_limit_smarty'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['cache_limit_smarty']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['cache_limit_smarty']->addMessage('required', _AD_STDCACHE_ERROR_REQUIRED, _AD_STDCACHE_CACHE_LIMIT);
        $this->mFieldProperties['cache_limit_smarty']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_STDCACHE_CACHE_LIMIT);
        $this->mFieldProperties['cache_limit_smarty']->addVar('min', '1000000'); // 1MB
        $this->mFieldProperties['cache_limit_smarty']->addVar('max', '1000000000'); // 1GB

        // Cache notification limit (in bytes)
        $this->mFieldProperties['cache_limit_alert_trigger'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['cache_limit_alert_trigger']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['cache_limit_alert_trigger']->addMessage('required', _AD_STDCACHE_ERROR_REQUIRED, _MI_STDCACHE_ALERT_TRIGGER);
        $this->mFieldProperties['cache_limit_alert_trigger']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _MI_STDCACHE_ALERT_TRIGGER);
        $this->mFieldProperties['cache_limit_alert_trigger']->addVar('min', '1000000'); // 1MB
        $this->mFieldProperties['cache_limit_alert_trigger']->addVar('max', '1000000000'); // 1GB

        // Cache cleanup limit (in bytes)
        $this->mFieldProperties['cache_limit_cleanup'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['cache_limit_cleanup']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['cache_limit_cleanup']->addMessage('required', _AD_STDCACHE_ERROR_REQUIRED, _AD_STDCACHE_CACHE_CLEANUP_LIMIT);
        $this->mFieldProperties['cache_limit_cleanup']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_STDCACHE_CACHE_CLEANUP_LIMIT);
        $this->mFieldProperties['cache_limit_cleanup']->addVar('min', '1000000'); // 1MB
        $this->mFieldProperties['cache_limit_cleanup']->addVar('max', '1000000000'); // 1GB

        // Compiled templates limit (in bytes)
        $this->mFieldProperties['cache_limit_compiled'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['cache_limit_compiled']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['cache_limit_compiled']->addMessage('required', _AD_STDCACHE_ERROR_REQUIRED, _AD_STDCACHE_COMPILED_TEMPLATES_LIMIT);
        $this->mFieldProperties['cache_limit_compiled']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_STDCACHE_COMPILED_TEMPLATES_LIMIT);
        $this->mFieldProperties['cache_limit_compiled']->addVar('min', '1000000'); // 1MB
        $this->mFieldProperties['cache_limit_compiled']->addVar('max', '1000000000'); // 1GB

        // Notification enabled (boolean/yesno)
        $this->mFieldProperties['cache_limit_alert_enable'] = new XCube_FieldProperty($this);
    }

    /**
     * Get form values as array
     * This method is used by the Action class to get data for saving
     * 
     * @return array Form values
     */
    public function getValues()
    {
        return [
            'cache_limit_smarty' => (int)$this->get('cache_limit_smarty'),
            'cache_limit_alert_trigger' => (int)$this->get('cache_limit_alert_trigger'),
            'cache_limit_cleanup' => (int)$this->get('cache_limit_cleanup'),
            'cache_limit_compiled' => (int)$this->get('cache_limit_compiled'),
            'cache_limit_alert_enable' => (int)$this->get('cache_limit_alert_enable')
        ];
    }

    /**
     * Get the token name for CSRF protection
     * 
     * @return string Token name
     */
    public function getTokenName()
    {
        return 'module.stdCache.CacheConfigForm.TOKEN';
    }

    /**
     * Get the error message for token validation failure
     * 
     * @return string Error message
     */
    public function getTokenErrorMessage()
    {
        return defined('_MD_LEGACY_ERROR_TOKEN') ? _MD_LEGACY_ERROR_TOKEN : 'Token error.';
    }

    /**
     * Get the 'min' variable for a specific field's 'intRange' validator
     *
     * @param string $fieldName The name of the form field
     * @return mixed|null The 'min' value if found, otherwise null
     */
    public function getFieldMin($fieldName)
    {
        if (isset($this->mFieldProperties[$fieldName])) {
            $fieldProperty = $this->mFieldProperties[$fieldName];
            // Access the mVariables public property directly
            if (isset($fieldProperty->mVariables['min'])) {
                return $fieldProperty->mVariables['min'];
            }
        }
        return null; // Or a default like 0
    }

    /**
     * Get the 'max' variable for a specific field's 'intRange' validator
     *
     * @param string $fieldName The name of the form field
     * @return mixed|null The 'max' value if found, otherwise null
     */
    public function getFieldMax($fieldName)
    {
        if (isset($this->mFieldProperties[$fieldName])) {
            $fieldProperty = $this->mFieldProperties[$fieldName];
            // Access the mVariables public property directly
            if (isset($fieldProperty->mVariables['max'])) {
                return $fieldProperty->mVariables['max'];
            }
        }
        return null; // large number or null to omit the attribute
    }
}
