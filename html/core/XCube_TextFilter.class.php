<?php
/**
 * /core/XCube_TextFilter.class.php
 * @package    XCube
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2023 The XOOPSCube Project
 * @license    BSD-3-Clause
 */

class XCube_TextFilter {
	public $mDummy;  //Dummy member for preventing object be treated as empty.

	public static function getInstance( &$instance ) {
		if ( empty( $instance ) ) {
			$instance = new self();
		}
	}

	public function toShow( $str ) {
		return htmlspecialchars( $str, ENT_QUOTES );
	}

	public function toEdit( $str ) {
		return htmlspecialchars( $str, ENT_QUOTES );
	}
}
