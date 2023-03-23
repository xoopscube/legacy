<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.3
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/PicoControllerAbstract.class.php';
require_once __DIR__ . '/PicoModelCategory.class.php';
require_once __DIR__ . '/PicoModelContent.class.php';
require_once __DIR__ . '/gtickets.php';
require_once dirname( __DIR__ ) . '/include/transact_functions.php';
require_once dirname( __DIR__ ) . '/include/history_functions.php';

class PicoControllerDeleteContent extends PicoControllerAbstract {

	public $contentObj;

	public function execute( $request ) {
		// Ticket Check
		if ( ! $GLOBALS['xoopsGTicket']->check( true, 'pico' ) ) {
			redirect_header( XOOPS_URL . '/', 3, $GLOBALS['xoopsGTicket']->getErrors() );
		}

		parent::execute( $request );

		// contentObj
		$cat_data         = $this->currentCategoryObj->getData();
		$this->contentObj = new PicoContent( $this->mydirname, $request['content_id'], $this->currentCategoryObj );

		// check existence
		if ( $this->contentObj->isError() ) {
			redirect_header( XOOPS_URL . "/modules/$this->mydirname/index.php", 2, _MD_PICO_ERR_READCONTENT );
			exit;
		}
		$content_data = $this->contentObj->getData();

		// permission check (can_delete)
		if ( empty( $content_data['can_delete'] ) ) {
			redirect_header( XOOPS_URL . '/', 2, _MD_PICO_ERR_DELETECONTENT );
			exit;
		}

		// delete transaction
		pico_delete_content( $this->mydirname, $request['content_id'] );

		// view
		$this->is_need_header_footer = false;
	}

	public function render() {
		redirect_header( XOOPS_URL . "/modules/$this->mydirname/" . pico_common_make_category_link4html( $this->mod_config, $this->currentCategoryObj->getData() ), 2, _MD_PICO_MSG_CONTENTDELETED );
		exit;
	}
}
