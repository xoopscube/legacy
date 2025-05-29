<?php
/**
 * Bannerstats - Module for XCL
 * defines the primary base action class for bannerstats admin actions
 * contain all methods expected by the ActionFrame
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

class Bannerstats_Action
{
    /**
     * @var bool
     */
    protected $mAdminFlag = false;

    /**
     * @var XCube_ActionForm
     */
    public $mActionForm = null;

    /**
     * @var XoopsSimpleObject
     */
    public $mObject = null;

    /**
     * Constructor
     * @param bool
     */
    public function __construct($isAdmin = false)
    {
        $this->mAdminFlag = (bool)$isAdmin;
    }

    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        // Base preparation logic
        // Specific setup (like loading objects or forms) happens in subclasses
    }

    /**
     * Checks if the current user has permission to execute this action.
     * This method MUST exist as it's called by the ActionFrame
     * 
     * @param XCube_Controller
     * @param XoopsUser
     * @return bool True if permitted
     */
    public function hasPermission(&$controller, &$xoopsUser)
    {
        if ($this->mAdminFlag) {
            return (is_object($xoopsUser) && $xoopsUser->isAdmin());
        }
        return false;
    }
    
    /**
     * Gets the default view status for GET requests
     * 
     * @param XCube_Controller
     * @param XoopsUser
     * @return int A BANNERSTATS_FRAME_VIEW_ constant
     */
    public function getDefaultView(&$controller, &$xoopsUser)
    {
        return defined('BANNERSTATS_FRAME_VIEW_INDEX') ? BANNERSTATS_FRAME_VIEW_INDEX : 3;
    }
    
    /**
     * Executes the main logic of the action, typically for POST requests
     * 
     * @param XCube_Controller
     * @param XoopsUser
     * @return int A BANNERSTATS_FRAME_VIEW_ constant
     */
    public function execute(&$controller, &$xoopsUser)
    {
        return $this->getDefaultView($controller, $xoopsUser);
    }
    
    /**
     * Handles rendering for a successful operation
     * 
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     */
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        // redirect, e.g., $controller->executeForward(...);
    }
    
    /**
     * Handles rendering for an error in operation
     * 
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     */
    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        // redirect with an error message, e.g., $controller->executeRedirect(...);
    }
    
    /**
     * Handles rendering for an index/list view
     * 
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     */
    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        // Subclasses (like AbstractListAction) will implement this to set templates and attributes
    }

    /**
     * Handles rendering for an input/form view
     * 
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     */
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        // Subclasses (like AbstractEditAction) will implement this
    }

    /**
     * Handles rendering for a preview view (if applicable)
     * 
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     */
    public function executeViewPreview(&$controller, &$xoopsUser, &$render)
    {
    }

    /**
     * Handles rendering or logic for a cancel operation
     * 
     * @param XCube_Controller
     * @param XoopsUser
     * @param XCube_RenderTarget
     */
    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        // redirect, e.g., $controller->executeForward(...);
    }
}
