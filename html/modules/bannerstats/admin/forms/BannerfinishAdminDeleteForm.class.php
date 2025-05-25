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

require_once XOOPS_ROOT_PATH . '/core/XCube_ActionForm.class.php';
require_once XOOPS_MODULE_PATH . '/legacy/class/Legacy_Validator.class.php';
require_once XOOPS_MODULE_PATH . '/bannerstats/class/handler/BannerFinish.class.php';


class Bannerstats_BannerfinishAdminDeleteForm extends XCube_ActionForm
{
    /**
     * Gets the unique token name for this form instance
     * @return string
     */
    public function getTokenName(): string
    {
        return 'module.bannerstats.BannerfinishAdminDeleteForm.TOKEN.' . $this->get('bid');
    }

    /**
     * Prepares the form properties and field validations
     * @return void
     */
    public function prepare(): void
    {
        //
        // Set form properties
        //
        $this->mFormProperties['bid'] = new XCube_IntProperty('bid');

        //
        // Set field properties (validations)
        //
        $this->mFieldProperties['bid'] = new XCube_FieldProperty($this);
        $this->mFieldProperties['bid']->setDependsByArray(['required']);
        $this->mFieldProperties['bid']->addMessage('required', _AD_BANNERSTATS_ERROR_REQUIRED, _AD_BANNERSTATS_BID);
    }

    /**
     * Loads data from a Bannerstats_BannerfinishObject into the form
     *
     * @param XoopsSimpleObject $obj
     * @return void
     */
    public function load(&$obj): void
    {
        if ($obj instanceof Bannerstats_BannerfinishObject) {
            $this->set('bid', $obj->get('bid'));
        }
    }

    /**
     * Updates a Bannerstats_BannerfinishObject with data from the form.
     * For a delete form, this method is often minimal or not strictly necessary
     * as the primary action is deletion, not updating the object's state.
     *
     * @param XoopsSimpleObject $obj Bannerstats_BannerfinishObject
     * @return void
     */
    public function update(&$obj): void
    {
        if ($obj instanceof Bannerstats_BannerfinishObject) {
            $obj->set('bid', $this->get('bid'));
        }
    }
}
