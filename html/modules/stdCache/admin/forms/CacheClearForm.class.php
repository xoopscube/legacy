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
        return 'module.stdCache.CacheClearForm.TOKEN';
    }

    public function getTokenErrorMessage()
    {
        return null;
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
        $this->mFormProperties['clear_age'] = new XCube_IntProperty('clear_age'); // radio buttons

        //
        // Set field properties
        //
        $this->mFieldProperties['confirm'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['confirm']->setDependsByArray(['required']);
        $this->mFieldProperties['confirm']->addMessage('required', _AD_STDCACHE_CONFIRM_CLEAR);
    }

    /**
     * Load default values into the form properties
     */
    public function loadDefaults()
    {
        $this->set('confirm', false);
        $this->set('clear_smarty_cache', false);
        $this->set('clear_compiled_templates', true);
        $this->set('clear_logs', false);
        $this->set('clear_uploads', false);
        $this->set('clear_age', 0); // Default to "All files"
    }

    /**
     * Add validation method.
     * @return bool True if validation passes
     */
    public function validate()
    {
        parent::validate();

        if (!$this->get('clear_smarty_cache') &&
            !$this->get('clear_compiled_templates') &&
            !$this->get('clear_logs') &&
            !$this->get('clear_uploads') ) {
            $this->addErrorMessage(_AD_STDCACHE_CONFIRM_CLEAR);
        }

        return !$this->hasError();
    }

    // No need for update() method as this form doesn't update a single object directly
}
