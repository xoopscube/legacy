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
require_once __DIR__ . '/PicoControllerEditContent.class.php';
require_once __DIR__ . '/PicoModelCategory.class.php';
require_once __DIR__ . '/PicoModelContent.class.php';
require_once __DIR__ . '/gtickets.php';
require_once dirname( __DIR__ ) . '/include/transact_functions.php';
require_once dirname( __DIR__ ) . '/include/history_functions.php';

class PicoControllerPreviewContent extends PicoControllerEditContent {

	public function processPreview( $request ) {
		// Ticket Check
		if ( ! $GLOBALS['xoopsGTicket']->check( true, 'pico' ) ) {
			redirect_header( XOOPS_URL . '/', 2, $GLOBALS['xoopsGTicket']->getErrors() );
		}

		// initialize
		$cat_data = $this->currentCategoryObj->getData();
		$myts     = &PicoTextSanitizer::sGetInstance();

		// assigning other than preview/request
		// parent::execute( $request ) ;
		// permission check (can_edit) done

		// request
		$errors                  = [];
		$request                 = pico_get_requests4content( $this->mydirname, $errors, $cat_data['post_auto_approved'], $cat_data['isadminormod'], $this->assign['content']['id'] );
		$request['body_raw']     = $request['body'];
		$request['subject_raw']  = $request['subject'];
		$request4assign          = array_map( 'htmlspecialchars_ent', $request );
		$this->assign['request'] = $request4assign;

		// override content data for edit
		$this->assign['content']                 = $request4assign + $this->assign['content'];
		$this->assign['content']['filter_infos'] = pico_main_get_filter_infos( $request['filters'], $cat_data['isadminormod'] );
		$this->assign['content']['body_raw']     = $request['body'];
		$this->assign['content']['extra_fields'] = $request['extra_fields'];
		$this->assign['content']['ef']           = pico_common_unserialize( $request['extra_fields'] );

		// temporary $contentObj
		$tmpContentObj = new PicoContent( $this->mydirname, 0, $this->currentCategoryObj, true );

		// preview
		$this->assign['preview'] = [
			'errors'     => $errors,
			'htmlheader' => $request['htmlheader'], // remove it?
			'subject'    => $myts->makeTboxData4Show( $request['subject'], 1, 1 ),
			'body'       => $tmpContentObj->filterBody( $this->assign['content'] ),
		];
	}
}
