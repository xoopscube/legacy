<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/PicoControllerAbstract.class.php';
require_once __DIR__ . '/PicoModelCategory.class.php';
require_once __DIR__ . '/PicoModelContent.class.php';

class PicoControllerGetHistory extends PicoControllerAbstract {

	public $contentObj;
	public $view;

	public function execute( $request ) {
		parent::execute( $request );

		$cat_data       = $this->currentCategoryObj->getData();
		$breadcrumbsObj = AltsysBreadcrumbs::getInstance();

		$this->contentObj = new PicoContent( $this->mydirname, $request['content_id'], $this->currentCategoryObj );

		// add breadcrumbs if the content exists
		if ( ! $this->contentObj->isError() ) {
			$content_data            = $this->contentObj->getData();
			$this->assign['content'] = $this->contentObj->getData4html();
			$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/' . $this->mydirname . '/' . $this->assign['content']['link'], $this->assign['content']['subject'] );
			$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/' . $this->mydirname . '/index.php?page=contentmanager&amp;content_id=' . $content_data['id'], _MD_PICO_CONTENTMANAGER );
		}

		// permission check by 'can_edit'
		if ( empty( $cat_data['can_edit'] ) ) {
			redirect_header( XOOPS_URL . '/', 1, _MD_PICO_ERR_EDITCONTENT );
			exit;
		}

		// get $history_profile from the id
		$this->assign['content_history_id'] = $request['content_history_id'];
		[ , , $history_body ] = pico_get_content_history_profile( $this->mydirname, $request['content_history_id'] );
		$this->assign['history_body_raw'] = $history_body;
        // Since XCL 2.3 revision list
        $this->assign['content_histories'] = pico_get_content_histories4assign( $this->mydirname, $content_data['id'] );
		// breadcrumbs
		$breadcrumbsObj->appendPath( '', _MD_PICO_HISTORY );
		$this->assign['xoops_breadcrumbs'] = $breadcrumbsObj->getXoopsbreadcrumbs();
		$this->assign['xoops_pagetitle']   = _MD_PICO_HISTORY;

		// view
		$this->view = $request['view'];
		switch ( $this->view ) {
			case 'viewhistory':
				$this->template_name         = $this->mydirname . '_main_viewhistory.html';
				$this->is_need_header_footer = true;
				break;
			case 'download':
			default:
				$this->is_need_header_footer = false;
				break;
		}
	}

	public function render( $target = null ) {
		// remove all ob filters
		while ( ob_get_level() ) {
			ob_end_clean();
		}

		/* after */
		if ( $this->view === 'download' ) {
			header( 'Content-Type: text/plain' );
			header( 'Content-Disposition: attachment; filename="content_history_' . sprintf( '%010d', $this->assign['content_history_id'] ) . '.txt"' );
			echo $this->assign['history_body_raw'];
		} else {
			header( 'Content-Type: text/html;' );
			header( 'Content-Disposition: inline; filename="content_history_' . sprintf( '%010d', $this->assign['content_history_id'] ) . '.txt"' );
			echo nl2br( htmlspecialchars( $this->assign['history_body_raw'], ENT_QUOTES ) );
		}

	}
}
