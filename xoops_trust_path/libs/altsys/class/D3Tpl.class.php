<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Class D3Tpl
 * @package    Altsys
 * @version    XCL 2.3.1
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Authors
 * @license    GPL v2.0
 */

require_once XOOPS_ROOT_PATH . '/class/template.php';
require_once XOOPS_TRUST_PATH . '/libs/altsys/include/altsys_functions.php';


class D3Tpl extends XoopsTpl
{
    /**
     * D3Tpl constructor.
     */
    public function __construct()
    {
        parent::__construct() ;

        // for RTL users
        // TODO PHP8
/*         define('_GLOBAL_LEFT', 1 == _ADM_USE_RTL ? 'right' : 'left') ;
        define('_GLOBAL_RIGHT', 1 == _ADM_USE_RTL ? 'left' : 'right') ; */
    }
}
