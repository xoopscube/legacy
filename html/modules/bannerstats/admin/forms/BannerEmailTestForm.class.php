<?php
/**
 * Bannerstats - Module for XCL
 * BannerEmailTestForm.class.php
 *
 * Form for testing email notifications with CSRF protection.
 *
 * @package    Bannerstats
 * @author     Nuno Luciano (aka gigamaster) XCL PHP8
 * @copyright  2005-2025 The XOOPSCube Project
 * @license    GPL V2
 * @version    v2.5.0 Release XCL 
 * @link       http://github.com/xoopscube/
 **/

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';

class Bannerstats_BannerEmailTestForm extends XCube_ActionForm
{
    public function getTokenName(): string
    {
        return 'module.bannerstats.BannerEmailTestForm.TOKEN';
    }

    public function prepare()
    {
        // Set form properties
        $this->mFormProperties['bid'] = new XCube_IntProperty('bid');
        $this->mFormProperties['email_type'] = new XCube_StringProperty('email_type');

        // Set validation rules
        $this->mFieldProperties['bid'] = new XCube_FieldProperty($this);
         $this->mFieldProperties['bid']->setDependsByArray(['required', 'intRange']);
    
        $this->mFieldProperties['bid']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED.'bid', _AD_BANNERSTATS_EMAILTEST_BANNER_ID);
        $this->mFieldProperties['bid']->addMessage('intRange', _AD_BANNERSTATS_ERROR_INTRANGE.'bid int', _AD_BANNERSTATS_EMAILTEST_BANNER_ID, '1');
        $this->mFieldProperties['bid']->addVar('min', 1);
        $this->mFieldProperties['bid']->addVar('max', 2147483647);

        $this->mFieldProperties['email_type'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['email_type']->setDependsByArray(['required']);
$this->mFieldProperties['email_type']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_EMAILTEST_SELECT_EMAIL_TYPE);
    }

public function validate()
{
    // Call parent validation but don't add our own duplicate messages
    parent::validate();
    
    // Only validate email_type against allowed values (not required - that's handled by parent)
    $validTypes = ['low_client', 'low_admin', 'finished_client', 'finished_admin'];
    if ($this->get('email_type') && !in_array($this->get('email_type'), $validTypes)) {
        $this->addErrorMessage('INVALID_EMAIL_TYPE');
    }
    
    return !$this->hasError();
}
}
