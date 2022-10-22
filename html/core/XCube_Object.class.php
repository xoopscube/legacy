<?php
/**
 * XCube_Object.class.php
 * @package    XCube
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Minahito, 2008/10/12
 * @copyright  (c) 2005-2022 The XOOPSCube Project
 * @license    BSD-3-Clause
 */


/**
 * @param $definition
 *
 * @return array
 */
function S_PUBLIC_VAR( $definition ) {
	$t_str = explode( ' ', trim( $definition ) );

	return [ 'name' => trim( $t_str[1] ), 'type' => trim( $t_str[0] ) ];
}

class XCube_Object {
	/**
	 * Member property
	 */
	public $mProperty = [];

	/**
	 * @static
	 * @return bool
	 */
	public function isArray() {
		return false;
	}

	/**
	 * Return member property information. This member function is called in
	 * the initialize of object and service. This member function has to be
	 * a static function.
	 *
	 * @static
	 * @return void
	 */
	public function getPropertyDefinition() {
	}

	public function __construct() { //typo rename to fields
		$fields = $this->getPropertyDefinition();
		foreach ( $fields as $t_field ) {
			$this->mProperty[ $t_field['name'] ] = [
				'type'  => $t_field['type'],
				'value' => null
			];
		}
	}

	/**
	 * Initialize. If the exception raises, return false.
	 */
	public function prepare() {
	}

	public function toArray() {
		$retArray = [];

		foreach ( $this->mProperty as $t_key => $t_value ) {
			$retArray[ $t_key ] = $t_value['value'];
		}

		return $retArray;
	}

	public function loadByArray( $vars ) {
		foreach ( $vars as $t_key => $t_value ) {
			if ( isset( $this->mProperty[ $t_key ] ) ) {
				$this->mProperty[ $t_key ]['value'] = $t_value;
			}
		}
	}
}

class XCube_ObjectArray {
	public function isArray() {
		return true;
	}

	/**
	 * @static
	 * @return string
	 */
	public function getClassName() {
	}
}
