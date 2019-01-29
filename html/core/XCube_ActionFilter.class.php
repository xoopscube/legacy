<?php
/**
 *
 * @package XCube
 * @version $Id: XCube_ActionFilter.class.php,v 1.5 2008/10/12 04:30:27 minahito Exp $
 * @copyright Copyright 2005-2007 XOOPS Cube Project  <https://github.com/xoopscube/legacy>
 * @license https://github.com/xoopscube/legacy/blob/master/docs/bsd_licenses.txt Modified BSD license
 *
 */

/**
 * @public
 * @brief [Abstract] Used for initialization, post-processing and others by the controller.
 * 
 *    This class is chained and called by the initialization procedure of the
 *    controller class. Developers or users can use the subclass of this class for
 *    dynamic customizing.
 * 
 *    Users usually don't need to add on filters because each controllers should
 *    have initialization code enough. This class is used to the case of special
 *    customizing by modules and users.
 * 
 *    Each controllers should not use this class to their initialization procedure.
 * 
 *    Two member functions are called by the controller at the special timing.
 *    These timing is different in each controllers.
 *
 * \par Abstract Class
 *    This class is an abstract class.
 */
class XCube_ActionFilter
{
    /**
     * @protected
     * @brief [READ ONLY] XCube_Controller
     */
    public $mController;
    
    /**
     * @protected
     * @brief [READ ONLY] XCube_Root
     */
    public $mRoot;
    
    /**
     * @public
     * @brief Constructor.
     * @param $controller XCube_Controller
     */
    // !Fix PHP7
    public function __construct(&$controller)
    //public function XCube_ActionFilter(&$controller)
    {
        $this->mController =& $controller;
        $this->mRoot =& $this->mController->mRoot;
    }

    /**
     * @public
     * @brief [Abstract] Executes the logic, when the controller executes preFilter().
     * @remarks
     *     This method is called earliest in the controller's initialization process, so 
     *     some of filters may not be called if these filters are registered later.
     */
    public function preFilter()
    {
    }
    
    /**
     * @public
     * @brief [Abstract] Executes the logic, when the controller executes preBlockFilter().
     * @remarks
     *      Each controller has different timing when it calls preBlockFilter().
     */
    public function preBlockFilter()
    {
    }
    
    /**
     * @public
     * @brief [Abstract] Executes the logic, when the controller executes postFilter().
     * @remarks
     *      Each controller has different timing when it calls postFilter().
     */
    public function postFilter()
    {
    }
}
