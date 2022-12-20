<?php
/**
 *
 * @package Legacy
 * @version $Id: Legacy_ActionForm.class.php,v 1.4 2008/09/25 15:11:58 kilica Exp $
 * @copyright Copyright 2005-2022 XOOPSCube Project
 * @license GPL 2.0
 *
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once XCUBE_CORE_PATH . '/XCube_ActionForm.class.php';

/**
 * @public
 * @brief Provides a base actionForm class for module developers
 *
 * This class retains the specification defined in the Legacy 2.1 release.
 * XCube_ActionForm will be changed. This class will change to adapt the base
 * class and will provide uniform specifications for developers.
 *
 * @todo All actionForms from Package_Legacy must inherit from this class.
 */
class Legacy_ActionForm extends XCube_ActionForm
{
    /**
     * @public
     * @brief Constructor.
     */
    public function __construct()
    {
// version 2.3.1
    	 parent::__construct();
        //parent::XCube_ActionForm();
    }
}
