<?php
/**
 * XCube_ActionFilter.class.php
 * This class is an abstract class.
 * Typically, an abstract defines an interface for other classes to extend.
 * @package    XCube
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    BSD-3-Clause
 * @brief      [Abstract] Used for initialization, post-processing and other purposes by the controller.
 *
 *    This class is chained and called by the initialization procedure of the controller class.
 *    Developers or users can use the subclass to customize dynamically.
 *
 *    Users usually do not need to add filters because each controller have sufficient initialization code.
 *    This class is used in case of special customization of modules and users.
 *
 *    A controller must not use this class in its initialization procedure.
 *
 *    Two member functions are called by the controller at the proper time.
 *    The timing is different for each controller.
 */

class XCube_ActionFilter {
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
	 *
	 * @param $controller XCube_Controller
	 */
	public function __construct( &$controller ) {
		$this->mController =& $controller;
		$this->mRoot       =& $this->mController->mRoot;
	}

	/**
	 * @public
	 * @brief [Abstract] Executes the logic, when the controller executes preFilter().
	 * @remarks
	 *     This method is called at the very beginning of the controller initialization process,
	 *     some filters may not be called if these filters are registered later.
	 */
	public function preFilter() {
	}

	/**
	 * @public
	 * @brief [Abstract] Executes the logic, when the controller executes preBlockFilter().
	 * @remarks
	 *      Each controller has different timing when it calls preBlockFilter().
	 */
	public function preBlockFilter() {
	}

	/**
	 * @public
	 * @brief [Abstract] Executes the logic, when the controller executes postFilter().
	 * @remarks
	 *      Each controller has different timing when it calls postFilter().
	 */
	public function postFilter() {
	}
}
