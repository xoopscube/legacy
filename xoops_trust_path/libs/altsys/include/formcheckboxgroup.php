<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * A checkbox field with a choice of available groups
 * @package    Altsys
 * @version    XCL 2.3.1
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

if ( ! defined( 'XOOPS_ROOT_PATH' ) ) {
	exit();
}

/**
 * Parent
 */
include_once XOOPS_ROOT_PATH . '/class/xoopsform/formcheckbox.php';


class AltsysFormCheckboxGroup extends XoopsFormCheckbox {
	/**
	 * Constructor
	 *
	 * @param string $caption
	 * @param string $name
	 * @param bool $include_anon Include group "anonymous"?
	 * @param mixed $value Pre-selected value (or array of them).
	 */
	public function __construct( $caption, $name, $include_anon = false, $value = null ) {

		parent::__construct( $caption, $name, $value );
		$member_handler =& xoops_gethandler( 'member' );
		if ( ! $include_anon ) {
			$options = $member_handler->getGroupList( new Criteria( 'groupid', XOOPS_GROUP_ANONYMOUS, '!=' ) );
		} else {
			$options = $member_handler->getGroupList();
		}
		foreach ( $options as $k => $v ) {
			$options[ $k ] = $v . '<br>';
		}
		$this->addOptionArray( $options );
	}
}
