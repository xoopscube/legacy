<?php
/**
 * Standard cache - Module for XCL
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

class stdCache_CacheClearForm extends XCube_ActionForm
{
    public function getTokenName()
    {
        return 'module.stdCache.CacheClearForm.Token.' . $this->get('confirm'); // Make token name dynamic if needed or keep static
    }

    public function prepare()
    {
        // Set form properties for different cache types
        $this->mFormProperties['confirm'] = new XCube_BoolProperty('confirm');
        $this->mFormProperties['clear_smarty_cache'] = new XCube_BoolProperty('clear_smarty_cache');
        $this->mFormProperties['clear_compiled_templates'] = new XCube_BoolProperty('clear_compiled_templates');
        $this->mFormProperties['clear_logs'] = new XCube_BoolProperty('clear_logs');
        $this->mFormProperties['clear_uploads'] = new XCube_BoolProperty('clear_uploads');
        // $this->mFormProperties['clear_all'] = new XCube_BoolProperty('clear_all'); // 'clear_all' can be handled in action logic
        $this->mFormProperties['clear_age'] = new XCube_IntProperty('clear_age'); // No specific validation needed here if values are fixed by radio buttons.

        // Set field properties
        // Confirmation checkbox
        $this->mFieldProperties['confirm'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['confirm']->setDependsByArray(['required']);
        $this->mFieldProperties['confirm']->addMessage('required', _AD_STDCACHE_CONFIRM_CLEAR);
        
        // No specific validators needed for bool properties beyond their type,
        // unless you have specific rules like "at least one must be checked".

        // Initialize form values (default selections)
        $this->loadDefaults();
    }

    public function loadDefaults()
    {
        $this->set('confirm', false);
        $this->set('clear_smarty_cache', false); // Uncheck cache to prevent custom localization from delete
        $this->set('clear_compiled_templates', true); // Default to select compiled templates
        $this->set('clear_logs', false);
        $this->set('clear_uploads', false);
        $this->set('clear_age', 0); // Default to "All files"
        // $this->set('clear_all', false);
    }

    // Add validation method
    public function validate()
    {
        parent::validate(); // Call parent's validation first

        // Ensure the confirmation checkbox is checked
        if (!$this->get('confirm')) {
            // The 'required' dependency on 'confirm' field property should handle this,
            // but an explicit check here is also fine.
            // $this->addErrorMessage(_AD_STDCACHE_CONFIRM_CLEAR); // Already handled by field property
        }
        
        // Ensure at least one cache type is selected for clearing
        if (!$this->get('clear_smarty_cache') &&
            !$this->get('clear_compiled_templates') &&
            !$this->get('clear_logs') &&
            !$this->get('clear_uploads') /* &&
            !$this->get('clear_all') */ ) {
            // Ensure this language constant exists
            $this->addErrorMessage(defined('_AD_STDCACHE_SELECT_CACHE_TYPE_TO_CLEAR') ? _AD_STDCACHE_SELECT_CACHE_TYPE_TO_CLEAR : 'Please select at least one cache type to clear.');
        }
        
        return !$this->hasError();
    }
}
