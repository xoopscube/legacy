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

class BannerEmailTestForm extends XCube_ActionForm
{
    /**
     * Get the token name for CSRF protection.
     * @return string Token name
     */
    public function getTokenName()
    {
        return 'module.bannerstats.BannerEmailTestForm.TOKEN';
    }

    /**
     * Get the error message for token validation failure.
     */
    public function getTokenErrorMessage()
    {
        return null; // Use framework default
    }

    /**
     * Prepares the form and initializes properties.
     */
    public function prepare()
    {
        parent::prepare(); // Essential for token generation
        
        // Define form properties
        $this->mFormProperties['bid'] = new XCube_IntProperty('bid');
        $this->mFormProperties['email_type'] = new XCube_StringProperty('email_type');
    }

    /**
     * Validate banner ID
     */
    public function validate_bid()
    {
        $bid = $this->get('bid');
        if (empty($bid) || !is_numeric($bid) || $bid <= 0) {
            $this->addErrorMessage('bid', 'Please select a valid banner.');
        }
    }

    /**
     * Validate email type
     */
    public function validate_email_type()
    {
        $emailType = $this->get('email_type');
        $validTypes = ['admin_alert', 'client_alert', 'both'];
        
        if (empty($emailType) || !in_array($emailType, $validTypes)) {
            $this->addErrorMessage('email_type', 'Please select a valid email type.');
        }
    }
}