<?php
// File: c:\Users\nunol\Local Sites\php82\app\public\legacy250\html\modules\bannerstats\admin\class\Action.class.php

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

// This file defines the primary base action class for bannerstats admin actions.
// It should contain all methods expected by the ActionFrame.

class Bannerstats_Action
{
    /**
     * @var bool Flag indicating if the action is in an admin context.
     */
    protected $mAdminFlag = false;

    /**
     * @var XCube_ActionForm Holds the form associated with this action.
     */
    public $mActionForm = null;

    /**
     * @var XoopsSimpleObject Holds the main data object this action operates on (e.g., for edit/delete).
     */
    public $mObject = null;

    /**
     * Constructor
     * @param bool $isAdmin Set to true if this action is for the admin area.
     */
    public function __construct($isAdmin = false)
    {
        $this->mAdminFlag = (bool)$isAdmin; // Ensure it's a boolean
    }

    /**
     * Prepare for the action execution.
     * This method MUST exist as it's called by the ActionFrame.
     * Subclasses (like AbstractEditAction, AbstractListAction) will override and extend this.
     * 
     * @param XCube_Controller $controller The main XOOPS controller.
     * @param XoopsUser $xoopsUser The current user object.
     * @param array $moduleConfig The current module's configuration.
     */
    public function prepare(&$controller, &$xoopsUser, $moduleConfig)
    {
        // Base preparation logic.
        // Specific setup (like loading objects or forms) happens in subclasses.
    }

    /**
     * Checks if the current user has permission to execute this action.
     * This method MUST exist as it's called by the ActionFrame.
     * Subclasses should override this for more specific permission checks if needed.
     *
     * @param XCube_Controller $controller
     * @param XoopsUser $xoopsUser
     * @return bool True if permitted, false otherwise.
     */
    public function hasPermission(&$controller, &$xoopsUser)
    {
        // Default permission for admin actions: only site administrators.
        if ($this->mAdminFlag) {
            return (is_object($xoopsUser) && $xoopsUser->isAdmin());
        }
        // Default for non-admin actions (if this base were used for them) would be false.
        return false;
    }
    
    /**
     * Gets the default view status for GET requests.
     * This method MUST exist as it's called by the ActionFrame.
     * 
     * @param XCube_Controller $controller
     * @param XoopsUser $xoopsUser
     * @return int A BANNERSTATS_FRAME_VIEW_ constant.
     */
    public function getDefaultView(&$controller, &$xoopsUser)
    {
        // Default to showing an index/list view.
        // Ensure BANNERSTATS_FRAME_VIEW_INDEX is defined (typically in ActionFrame.class.php).
        return defined('BANNERSTATS_FRAME_VIEW_INDEX') ? BANNERSTATS_FRAME_VIEW_INDEX : 3; // Fallback
    }
    
    /**
     * Executes the main logic of the action, typically for POST requests.
     * This method MUST exist as it's called by the ActionFrame.
     * 
     * @param XCube_Controller $controller
     * @param XoopsUser $xoopsUser
     * @return int A BANNERSTATS_FRAME_VIEW_ constant.
     */
    public function execute(&$controller, &$xoopsUser)
    {
        // By default, if not overridden, a POST request might just show the default view
        // or indicate failure. Specific actions (like edit/delete) will override this.
        return $this->getDefaultView($controller, $xoopsUser);
    }
    
    /**
     * Handles rendering for a successful operation.
     * This method MUST exist as it's called by the ActionFrame.
     * 
     * @param XCube_Controller $controller
     * @param XoopsUser $xoopsUser
     * @param XCube_RenderTarget $render The render target.
     */
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        // Often involves a redirect, e.g., $controller->executeForward(...);
    }
    
    /**
     * Handles rendering for an error in operation.
     * This method MUST exist as it's called by the ActionFrame.
     * 
     * @param XCube_Controller $controller
     * @param XoopsUser $xoopsUser
     * @param XCube_RenderTarget $render
     */
    public function executeViewError(&$controller, &$xoopsUser, &$render)
    {
        // Often involves a redirect with an error message, e.g., $controller->executeRedirect(...);
    }
    
    /**
     * Handles rendering for an index/list view.
     * This method MUST exist as it's called by the ActionFrame.
     * 
     * @param XCube_Controller $controller
     * @param XoopsUser $xoopsUser
     * @param XCube_RenderTarget $render
     */
    public function executeViewIndex(&$controller, &$xoopsUser, &$render)
    {
        // Subclasses (like AbstractListAction) will implement this to set templates and attributes.
    }

    /**
     * Handles rendering for an input/form view.
     * This method MUST exist as it's called by the ActionFrame.
     * 
     * @param XCube_Controller $controller
     * @param XoopsUser $xoopsUser
     * @param XCube_RenderTarget $render
     */
    public function executeViewInput(&$controller, &$xoopsUser, &$render)
    {
        // Subclasses (like AbstractEditAction) will implement this.
    }

    /**
     * Handles rendering for a preview view (if applicable).
     * This method MUST exist as it's called by the ActionFrame.
     * 
     * @param XCube_Controller $controller
     * @param XoopsUser $xoopsUser
     * @param XCube_RenderTarget $render
     */
    public function executeViewPreview(&$controller, &$xoopsUser, &$render)
    {
    }

    /**
     * Handles rendering or logic for a cancel operation.
     * This method MUST exist as it's called by the ActionFrame.
     * 
     * @param XCube_Controller $controller
     * @param XoopsUser $xoopsUser
     * @param XCube_RenderTarget $render
     */
    public function executeViewCancel(&$controller, &$xoopsUser, &$render)
    {
        // Often involves a redirect, e.g., $controller->executeForward(...);
    }
}
