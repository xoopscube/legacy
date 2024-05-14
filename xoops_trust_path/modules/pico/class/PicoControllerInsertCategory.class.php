<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.4.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/PicoControllerAbstract.class.php';
require_once __DIR__ . '/PicoModelCategory.class.php';
require_once __DIR__ . '/gtickets.php';
require_once dirname( __DIR__ ) . '/include/transact_functions.php';

class PicoControllerInsertCategory extends PicoControllerAbstract {

	public $new_cat_id = - 1;

	public function execute( $request ) {
		// Ticket Check
		if ( ! $GLOBALS['xoopsGTicket']->check( true, 'pico' ) ) {
			redirect_header( XOOPS_URL . '/', 2, $GLOBALS['xoopsGTicket']->getErrors() );
		}

		parent::execute( $request );

		// initialize
		$pcat_data = $this->currentCategoryObj->getData();

		// permission check
		if ( empty( $pcat_data['can_makesubcategory'] ) ) {
			redirect_header( XOOPS_URL . '/', 1, _MD_PICO_ERR_MAKECATEGORY );
		}

		// create category
		$this->new_cat_id = pico_makecategory( $this->mydirname );

		// view
		$this->is_need_header_footer = false;
	}

	public function render( $target = null ) {
		redirect_header( XOOPS_URL . "/modules/$this->mydirname/" . pico_common_make_category_link4html( $this->mod_config, $this->new_cat_id, $this->mydirname ), 1, _MD_PICO_MSG_CATEGORYMADE );
		exit;
	}
}
