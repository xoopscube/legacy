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
require_once __DIR__ . '/gtickets.php';
require_once dirname( __DIR__ ) . '/include/history_functions.php';

class PicoControllerEditContent extends PicoControllerAbstract {

	public function execute( $request ) {
		parent::execute( $request );

		// makecontent/contentmanager
		$page = empty( $request['makecontent'] ) ? 'contentmanager' : 'makecontent';

		// $contentObj
		$contentObj = new PicoContent( $this->mydirname, $request['content_id'], $this->currentCategoryObj, 'makecontent' === $page );

		// check existence
		if ( $contentObj->isError() ) {
			redirect_header( XOOPS_URL . "/modules/$this->mydirname/index.php", 1, _MD_PICO_ERR_READCONTENT );
			exit;
		}

		// fetch data from DB
		$cat_data                          = $this->currentCategoryObj->getData();
		$this->assign['category']          = $this->currentCategoryObj->getData4html();
		$content_data                      = $contentObj->getData();
		$this->assign['content_base']      = $contentObj->getData4html( true );
		$this->contentObjs['content_base'] = &$contentObj;
		$this->assign['content']           = $contentObj->getData4edit();

		// permission check
		if ( 'makecontent' === $page ) {
			if ( empty( $cat_data['can_post'] ) ) {
				redirect_header( XOOPS_URL . '/', 1, _MD_PICO_ERR_CREATECONTENT );
			}
		} else if ( empty( $content_data['can_edit'] ) ) {
			redirect_header( XOOPS_URL . '/', 1, $content_data['locked'] ? _MD_PICO_ERR_LOCKEDCONTENT : _MD_PICO_ERR_EDITCONTENT );
		}

		// category list can be read for category jump-box etc.
		$categoryHandler                     = new PicoCategoryHandler( $this->mydirname, $this->permissions );
		$categories                          = $categoryHandler->getAllCategories();
		$this->assign['categories_can_post'] = [];
		foreach ( $categories as $tmpObj ) {
			$tmp_data = $tmpObj->getData();
			if ( empty( $tmp_data['can_post'] ) && empty( $tmp_data['can_edit'] ) ) {
				continue;
			}
			$this->assign['categories_can_post'][ $tmp_data['id'] ] = str_repeat( '--', $tmp_data['cat_depth_in_tree'] ) . $tmp_data['cat_title'];
		}

		// vpath options
		$this->assign['content']['wraps_files'] = [ '' => '---' ] + pico_main_get_wraps_files_recursively( $this->mydirname, '/' );

		// breadcrumbs
		$breadcrumbsObj = AltsysBreadcrumbs::getInstance();
		if ( 'makecontent' === $page ) {
			$breadcrumbsObj->appendPath( '', _MD_PICO_LINK_MAKECONTENT );
			$this->assign['xoops_pagetitle'] = _MD_PICO_LINK_MAKECONTENT;
		} else {
			$breadcrumbsObj->appendPath( XOOPS_URL . '/modules/' . $this->mydirname . '/' . $this->assign['content']['link'], $this->assign['content']['subject'] );
			$breadcrumbsObj->appendPath( '', _MD_PICO_CONTENTMANAGER );
			$this->assign['xoops_pagetitle'] = _MD_PICO_CONTENTMANAGER;
		}
		$this->assign['xoops_breadcrumbs'] = $breadcrumbsObj->getXoopsbreadcrumbs();

		// misc assigns
		$this->assign['content_histories'] = pico_get_content_histories4assign( $this->mydirname, $content_data['id'] );
		$this->assign['page']              = $page;
		$this->assign['formtitle']         = 'makecontent' === $page ? _MD_PICO_LINK_MAKECONTENT : _MD_PICO_LINK_EDITCONTENT;
		$this->assign['gticket_hidden']    = $GLOBALS['xoopsGTicket']->getTicketHtml( __LINE__, 1800, 'pico' );

		// views
		$this->template_name         = $this->mydirname . '_main_content_form.html';
		$this->is_need_header_footer = true;

		// preview
		$this->processPreview( $request );

		// editor (wysiwyg etc)
		$editor_assigns               = $this->getEditorAssigns( 'body', $this->assign['content']['body_raw'] );
		$this->assign['body_wysiwyg'] = $editor_assigns['body'];
		$this->html_header            .= $editor_assigns['header'];
	}

	// virtual
	public function processPreview( $request ) {
	}

	public function getEditorAssigns( $name, $value ) {
		if ( empty( $_POST['body_editor'] ) ) {
			$editor = $this->mod_config['body_editor'];
		}
		// @Todo add wysiwyg editor
		/*  else {
			$editor = $_POST['body_editor'];
		}

	   if ('common_fckeditor' === $editor) {
			// FCKeditor in common/fckeditor/
			$header = '
				<script type="text/javascript" src="' . XOOPS_URL . '/common/fckeditor/fckeditor.js"></script>
				<script type="text/javascript"><!--
					function fckeditor_exec() {
						var oFCKeditor = new FCKeditor( "' . $name . '" , "100%" , "500" , "Default" );

						oFCKeditor.BasePath = "' . XOOPS_URL . '/common/fckeditor/";

						oFCKeditor.ReplaceTextarea();
					}
				// --></script>
			';
			$body   = '<textarea id="' . $name . '" name="' . $name . '">' . htmlspecialchars($value, ENT_QUOTES) . '</textarea><script>fckeditor_exec();</script>';
		} else {*/
		// normal (xoopsdhtmltarea)
		$header = '';
		$body   = '';

		// }

		return [ 'header' => $header, 'body' => $body ];
	}
}
