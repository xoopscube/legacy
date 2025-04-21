<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/PicoControllerAbstract.class.php';
require_once __DIR__ . '/PicoModelCategory.class.php';
require_once __DIR__ . '/gtickets.php';
require_once dirname( __DIR__ ) . '/include/transact_functions.php';

class PicoControllerDeleteCategory extends PicoControllerAbstract {

	public function execute( $request ) {
		// Ticket Check
		if ( ! $GLOBALS['xoopsGTicket']->check( true, 'pico' ) ) {
			redirect_header( XOOPS_URL . '/', 2, $GLOBALS['xoopsGTicket']->getErrors() );
		}

		parent::execute( $request );

		// $categoryObj (not parent)
		$picoPermission = &PicoPermission::getInstance();
		$categoryObj    = new PicoCategory( $this->mydirname, $request['cat_id'], $picoPermission->getPermissions( $this->mydirname ) );

		// check existence
		if ( $categoryObj->isError() ) {
			redirect_header( XOOPS_URL . "/modules/$this->mydirname/index.php", 1, _MD_PICO_ERR_READCONTENT );
			exit;
		}
		$cat_data = $categoryObj->getData();

		// permission check
		if ( empty( $cat_data['isadminormod'] ) ) {
			redirect_header( XOOPS_URL . '/', 1, _MD_PICO_ERR_CATEGORYMANAGER );
		}

		// cat_id != 0 check
		if ( 0 === $cat_data['id'] ) {
			// LANGTD
			redirect_header( XOOPS_URL . '/', 1, 'top category cannot be deleted' );
		}

		// children check
		if ( (is_countable($cat_data['redundants']['subcattree_raw']) ? count( $cat_data['redundants']['subcattree_raw'] ) : 0) > 0 ) {
			// LANGTD
			redirect_header( XOOPS_URL . '/', 1, 'child categories exist' );
		}

		// delete transaction
		pico_delete_category( $this->mydirname, $request['cat_id'] );

		// view
		$this->is_need_header_footer = false;
	}

	public function render() {
		redirect_header( XOOPS_URL . "/modules/$this->mydirname/", 1, _MD_PICO_MSG_CATEGORYDELETED );
		exit;
	}
}
