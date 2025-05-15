<?php
/**
 * Standard cache - Module for XCL
 * CacheConfigForm.class.php
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

class stdCache_CacheConfigForm extends XCube_ActionForm
{
    /**
     * Prepare form fields and validators
     */
    public function prepare()
    {
        //
        // Set form properties (defines the internal properties of the form object)
        // These should match the names of your config items.
        //
        $this->mFormProperties['cache_limit'] = new XCube_IntProperty('cache_limit');
        $this->mFormProperties['cache_notification_limit'] = new XCube_IntProperty('cache_notification_limit');
        $this->mFormProperties['cache_cleanup_limit'] = new XCube_IntProperty('cache_cleanup_limit');
        $this->mFormProperties['compiled_templates_limit'] = new XCube_IntProperty('compiled_templates_limit');
        $this->mFormProperties['notification_enabled'] = new XCube_IntProperty('notification_enabled'); // Stored as 0/1

        //
        // Set field properties (defines validation rules and messages)
        //
        // Cache limit (in bytes)
        $this->mFieldProperties['cache_limit'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['cache_limit']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['cache_limit']->addMessage('required', _AD_STDCACHE_ERROR_REQUIRED, _AD_STDCACHE_CACHE_LIMIT);
        $this->mFieldProperties['cache_limit']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_STDCACHE_CACHE_LIMIT);
        $this->mFieldProperties['cache_limit']->addVar('min', '1000000'); // 1MB
        $this->mFieldProperties['cache_limit']->addVar('max', '1000000000'); // 1GB

        // Cache notification limit (in bytes)
        $this->mFieldProperties['cache_notification_limit'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['cache_notification_limit']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['cache_notification_limit']->addMessage('required', _AD_STDCACHE_ERROR_REQUIRED, _AD_STDCACHE_CACHE_NOTIFICATION_LIMIT);
        $this->mFieldProperties['cache_notification_limit']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_STDCACHE_CACHE_NOTIFICATION_LIMIT);
        $this->mFieldProperties['cache_notification_limit']->addVar('min', '1000000'); // 1MB
        $this->mFieldProperties['cache_notification_limit']->addVar('max', '1000000000'); // 1GB

        // Cache cleanup limit (in bytes)
        $this->mFieldProperties['cache_cleanup_limit'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['cache_cleanup_limit']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['cache_cleanup_limit']->addMessage('required', _AD_STDCACHE_ERROR_REQUIRED, _AD_STDCACHE_CACHE_CLEANUP_LIMIT);
        $this->mFieldProperties['cache_cleanup_limit']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_STDCACHE_CACHE_CLEANUP_LIMIT);
        $this->mFieldProperties['cache_cleanup_limit']->addVar('min', '1000000'); // 1MB
        $this->mFieldProperties['cache_cleanup_limit']->addVar('max', '1000000000'); // 1GB

        // Compiled templates limit (in bytes)
        $this->mFieldProperties['compiled_templates_limit'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['compiled_templates_limit']->setDependsByArray(['required', 'intRange']);
        $this->mFieldProperties['compiled_templates_limit']->addMessage('required', _AD_STDCACHE_ERROR_REQUIRED, _AD_STDCACHE_COMPILED_TEMPLATES_LIMIT);
        $this->mFieldProperties['compiled_templates_limit']->addMessage('intRange', _AD_LEGACY_ERROR_INTRANGE, _AD_STDCACHE_COMPILED_TEMPLATES_LIMIT);
        $this->mFieldProperties['compiled_templates_limit']->addVar('min', '1000000'); // 1MB
        $this->mFieldProperties['compiled_templates_limit']->addVar('max', '1000000000'); // 1GB

        // Notification enabled (boolean/yesno)
        $this->mFieldProperties['notification_enabled'] = new XCube_FieldProperty($this);
    }

    /**
     * Get form values as array
     * This method is used by the Action class to get data for saving.
     * 
     * @return array Form values
     */
    public function getValues()
    {
        return [
            'cache_limit' => (int)$this->get('cache_limit'),
            'cache_notification_limit' => (int)$this->get('cache_notification_limit'),
            'cache_cleanup_limit' => (int)$this->get('cache_cleanup_limit'),
            'compiled_templates_limit' => (int)$this->get('compiled_templates_limit'),
            'notification_enabled' => (int)$this->get('notification_enabled')
        ];
    }

    /**
     * Get the token name for CSRF protection.
     * 
     * @return string Token name
     */
    public function getTokenName()
    {
        return 'module.stdCache.CacheConfigForm.TOKEN';
    }

    /**
     * Get the error message for token validation failure.
     * 
     * @return string Error message
     */
    public function getTokenErrorMessage()
    {
        return defined('_MD_LEGACY_ERROR_TOKEN') ? _MD_LEGACY_ERROR_TOKEN : 'Token error.';
    }

    /**
     * Get the 'min' variable for a specific field's 'intRange' validator.
     *
     * @param string $fieldName The name of the form field.
     * @return mixed|null The 'min' value if found, otherwise null.
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
     * Get the 'max' variable for a specific field's 'intRange' validator.
     *
     * @param string $fieldName The name of the form field.
     * @return mixed|null The 'max' value if found, otherwise null.
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
