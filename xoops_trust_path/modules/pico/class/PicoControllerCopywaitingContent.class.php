<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/PicoControllerAbstract.class.php';
require_once __DIR__ . '/PicoModelCategory.class.php';
require_once __DIR__ . '/PicoModelContent.class.php';
require_once __DIR__ . '/gtickets.php';

require_once dirname( __DIR__ ) . '/include/transact_functions.php';
require_once dirname( __DIR__ ) . '/include/history_functions.php';

class PicoControllerCopywaitingContent extends PicoControllerAbstract {

	public $contentObj;

	public function execute( $request ) {
		// Ticket Check
		if ( ! $GLOBALS['xoopsGTicket']->check( true, 'pico' ) ) {
			redirect_header( XOOPS_URL . '/', 2, $GLOBALS['xoopsGTicket']->getErrors() );
		}

		parent::execute( $request );

		// contentObj
		$cat_data         = $this->currentCategoryObj->getData();
		$this->contentObj = new PicoContent( $this->mydirname, $request['content_id'], $this->currentCategoryObj );

		// check existence
		if ( $this->contentObj->isError() ) {
			redirect_header( XOOPS_URL . "/modules/$this->mydirname/index.php", 1, _MD_PICO_ERR_READCONTENT );
			exit;
		}
		$content_data = $this->contentObj->getData();

		// permission check (isadminormod)
		if ( empty( $content_data['isadminormod'] ) ) {
			redirect_header( XOOPS_URL . '/', 1, _MD_PICO_ERR_CATEGORYMANAGEMENT );
			exit;
		}

		// copy from waiting eg) body_waiting -> body
		pico_transact_copyfromwaitingcontent( $this->mydirname, $request['content_id'] );

		// view
		$this->is_need_header_footer = false;
	}

	public function render() {
		redirect_header( XOOPS_URL . "/modules/$this->mydirname/" . pico_common_make_content_link4html( $this->mod_config, $this->contentObj->getData() ), 1, _MD_PICO_MSG_CONTENTUPDATED );
		exit;
	}
}
