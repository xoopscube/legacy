<?php
/**
 * Bannerstats - Module for XCL
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

require_once XOOPS_MODULE_PATH . '/bannerstats/admin/class/AbstractEditAction.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/admin/forms/BannerclientAdminEditForm.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/BannerClient.class.php';

class Bannerstats_BannerclientEditAction extends Bannerstats_AbstractEditAction
{
    /**
     * Gets the ID of the banner client to be edited/created
     * @return int|null
     */
    public function _getId()
    {
        return xoops_getrequest('cid') ? (int)xoops_getrequest('cid') : null;
    }

    /**
     * Gets the handler for banner client objects
     * @return Bannerstats_BannerclientHandler|false
     */
    public function &_getHandler()
    {
        $handler = xoops_getmodulehandler('bannerclient', 'bannerstats');
        return $handler;
    }

    /**
     * Gets the form for editing banner client data
     * @param Bannerstats_BannerclientObject $obj banner client object to edit
     * @return Bannerstats_BannerclientAdminEditForm form object
     */
    public function _getForm(&$obj)
    {
        $form = new Bannerstats_BannerclientAdminEditForm();
        $form->prepare();
        $form->load($obj);
        
        return $form;
    }

    /**
     * Sets default values for a new banner client object
     * @param Bannerstats_BannerclientObject $obj
     */
    public function _setupActionForm()
    {
        $this->mActionForm = new Bannerstats_BannerclientAdminEditForm();
        $this->mActionForm->prepare();
        
        // Set default values for new banner client objects
        if ($this->mObject && $this->mObject->isNew()) {
            // Initialize all string properties with empty strings to avoid null values
            $this->mObject->set('name', '');
            $this->mObject->set('contact', '');
            $this->mObject->set('email', '');
            $this->mObject->set('login', '');
            $this->mObject->set('passwd', '');
            // Initialize address fields with empty strings
            $this->mObject->set('tel', '');
            $this->mObject->set('address1', '');
            $this->mObject->set('address2', '');
            $this->mObject->set('city', '');
            $this->mObject->set('region', '');
            $this->mObject->set('postal_code', '');
            $this->mObject->set('country_code', '');
            $this->mObject->set('extrainfo', '');
            // Set default status to active (1)
            $this->mObject->set('status', 1);
            // Set creation date to current time
            $this->mObject->set('date_created', time());
        }
    }

    /**
     * Prepares and sets data for the edit form view
     *
     * @param XCube_Controller $controller
     * @param XoopsUser        $xoopsUser
     * @param XCube_RenderTarget $render
     * @return void
     */
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        $render->setTemplateName('bannerclient_edit.html');
        $render->setAttribute('actionForm', $this->mActionForm);
        $render->setAttribute('object', $this->mObject);
        
        // Add any additional data needed for the form
        // e.g. list of countries for a dropdown
        $countries = $this->_getCountryList();
        $render->setAttribute('countries', $countries);
    }

    /**
     * Gets a list of countries for the country code dropdown
     * @return array Associative array of country codes and names
     */
    protected function _getCountryList()
    {
        // TODO complete with custom list
        return [
            '' => _SELECT,
            'US' => 'United States',
            'CA' => 'Canada',
            'GB' => 'United Kingdom',
            'FR' => 'France',
            'DE' => 'Germany',
            'JP' => 'Japan',
            'AU' => 'Australia',
            'BR' => 'Brazil',
            // todo
        ];
    }

    /**
     * Executes additional actions after successful save
     * @param XCube_Controller $controller
     * @param XoopsUser $xoopsUser
     * @param Bannerstats_BannerclientObject $obj
     */
    public function executeViewSuccess(&$controller, &$xoopsUser, &$obj)
    {
        $controller->executeForward('./index.php?action=BannerclientList');
    }


    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        if ($this->mActionForm->hasError()) {
            $this->executeViewInput($controller, $xoopsUser, $render);
        } else {
            $controller->executeRedirect('./index.php?action=BannerclientList', 1, _AD_BANNERSTATS_ERROR_DBUPDATE_FAILED);
        }
    }

    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        $controller->executeForward('./index.php?action=BannerclientList');
    }
}
