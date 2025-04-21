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

define( 'ALTSYS_DIR', XOOPS_TRUST_PATH . '/libs/altsys' );
if ( ! file_exists( ALTSYS_DIR . '/include/Text_Diff.php' ) ) {
	die( 'Install altsys' );
}
require_once ALTSYS_DIR . '/include/Text_Diff.php';
require_once ALTSYS_DIR . '/include/Text_Diff_Renderer.php';
require_once ALTSYS_DIR . '/include/Text_Diff_Renderer_unified.php';
require_once ALTSYS_DIR . '/include/Text_Diff_Renderer_inline.php';

class PicoControllerDiffHistories extends PicoControllerAbstract {

	public $contentObj;

	public function execute( $request ) {
		parent::execute( $request );

		$cat_data       = $this->currentCategoryObj->getData();
		$breadcrumbsObj = AltsysBreadcrumbs::getInstance();

		// get $history_profile from the id
		$older_profile = pico_get_content_history_profile( $this->mydirname, $request['older_history_id'] );
		if ( empty( $request['newer_history_id'] ) ) {
			$newer_profile = pico_get_content_history_profile( $this->mydirname, 0, (int) $older_profile[1] );
		} else {
			$newer_profile = pico_get_content_history_profile( $this->mydirname, $request['newer_history_id'] );
		}

		// check each content_ids
		if ( $older_profile[1] !== $newer_profile[1] ) {
			die( 'Content_ids different from each other' );
		}

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

		// get diff
		$diff_from_file4disp  = '';
		$original_error_level = error_reporting();
		error_reporting( $original_error_level & ~E_NOTICE & ~E_WARNING );
		$diff = new Text_Diff( explode( "\n", $older_profile[2] ), explode( "\n", $newer_profile[2] ) );
		//$renderer = new Text_Diff_Renderer_unified();
		//$diff_str = htmlspecialchars( $renderer->render( $diff ) , ENT_QUOTES ) ;
		$renderer                 = new Text_Diff_Renderer_inline();
		$this->assign['diff_str'] = $renderer->render( $diff );
		error_reporting( $original_error_level );
        // Since XCL 2.3 revision list
        $this->assign['content_histories'] = pico_get_content_histories4assign( $this->mydirname, $content_data['id'] );
		// breadcrumbs
		$breadcrumbsObj->appendPath( '', 'DIFF' );
		$this->assign['xoops_breadcrumbs'] = $breadcrumbsObj->getXoopsbreadcrumbs();
		$this->assign['xoops_pagetitle']   = _MD_PICO_HISTORY;

		// view
		$this->view = $request['view'];

		if ( $this->view === 'diffhistories' ) {
			$this->template_name         = $this->mydirname . '_main_diff_history.html';
			$this->is_need_header_footer = true;
		} else {
			$this->is_need_header_footer = false;
		}
	}


	public function render( $target = null ) {
		// remove all ob filters
		while ( ob_get_level() ) {
			ob_end_clean();
		}

		switch ( $this->view ) {
			default:
				header( 'Content-Type: text/html;' );
				echo '<html><meta http-equiv="Content-Type" content="text/html; charset='
				     . _CHARSET
				     . '" /><head>'
				     . pico_main_render_moduleheader( $this->mydirname, $this->mod_config )
				     . '</head><body><pre class="pico_history_diff" id="'
				     . $this->mydirname
				     . '_history_diff">'
				     . $this->assign['diff_str']
				     . '</pre></body></html>';
				break;
		}
	}
}
