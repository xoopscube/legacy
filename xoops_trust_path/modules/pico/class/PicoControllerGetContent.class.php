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
require_once __DIR__ . '/PicoModelContent.class.php';

class PicoControllerGetContent extends PicoControllerAbstract {

	public function execute( $request ) {
		parent::execute( $request );

		// $contentObj
		$contentObj = new PicoContent( $this->mydirname, $request['content_id'], $this->currentCategoryObj );

		// check existence
		if ( $contentObj->isError() ) {
			$this->exitFileNotFound();
		}

		$cat_data                     = $this->currentCategoryObj->getData();
		$this->assign['category']     = $this->currentCategoryObj->getData4html();
		$content_data                 = $contentObj->getData();
		$this->assign['content']      = $contentObj->getData4html( true );
		$this->contentObjs['content'] = &$contentObj;

		// permission check
		if ( empty( $content_data['can_read'] ) || empty( $content_data['can_readfull'] ) ) {
			if ( $this->uid > 0 ) {
				redirect_header( XOOPS_URL . '/', 1, _MD_PICO_ERR_PERMREADFULL );
			} else {
				redirect_header( XOOPS_URL . '/user.php', 1, _MD_PICO_ERR_LOGINTOREADFULL );
			}
			exit;
		}

		// auto-register
		if ( !empty( $this->mod_config['wraps_auto_register'] ) && '/' === @$cat_data['cat_vpath'][0] && 0 === $content_data['poster_uid'] && '' !== $content_data['vpath'] ) {
			$register_class = empty( $this->mod_config['auto_register_class'] ) ? 'PicoAutoRegisterWraps' : $this->mod_config['auto_register_class'];
			require_once __DIR__ . '/' . $register_class . '.class.php';
			$register_obj = new $register_class( $this->mydirname, $this->mod_config );
			$is_updated   = $register_obj->updateContent( $content_data['content_id'], $content_data['vpath'] );
			if ( $is_updated > 0 ) {
				// reload if the content is updated
				header( 'Location: ' . pico_common_unhtmlspecialchars( $this->assign['mod_url'] ) . '/' . pico_common_unhtmlspecialchars( $this->assign['content']['link'] ) );
				exit;
			}
		}

		// prev/next content
		$prevContentObj                    = &$contentObj->getPrevContent();
		$this->assign['prev_content']      = is_object( $prevContentObj ) ? $prevContentObj->getData4html() : [];
		$this->contentObjs['prev_content'] = &$prevContentObj;
		$nextContentObj                    = &$contentObj->getNextContent();
		$this->assign['next_content']      = is_object( $nextContentObj ) ? $nextContentObj->getData4html() : [];
		$this->contentObjs['next_content'] = &$nextContentObj;

		// link for "tell to friends"
		if ( $this->mod_config['use_taf_module'] ) {
			$this->assign['content']['tellafriend_uri'] = XOOPS_URL . '/modules/tellafriend/index.php?target_uri=' . rawurlencode( XOOPS_URL . "/modules/$this->mydirname/" . pico_common_make_content_link4html( $this->mod_config, $content_data ) ) . '&amp;subject=' . rawurlencode(
					sprintf( _MD_PICO_FMT_TELLAFRIENDSUBJECT, @$GLOBALS['xoopsConfig']['sitename'] )
				);
		} else {
			$this->assign['content']['tellafriend_uri'] = 'mailto:?subject='
			                                              . pico_main_escape4mailto( sprintf( _MD_PICO_FMT_TELLAFRIENDSUBJECT, @$GLOBALS['xoopsConfig']['sitename'] ) )
			                                              . '&amp;body='
			                                              . pico_main_escape4mailto( sprintf( _MD_PICO_FMT_TELLAFRIENDBODY, $content_data['subject'] ) )
			                                              . '%0A'
			                                              . XOOPS_URL
			                                              . "/modules/$this->mydirname/"
			                                              . rawurlencode( pico_common_make_content_link4html( $this->mod_config, $content_data ) );
		}

		// category list can be read for category jumpbox etc.
		$categoryHandler                     = new PicoCategoryHandler( $this->mydirname, $this->permissions );
		$categories                          = $categoryHandler->getAllCategories();
		$this->assign['categories_can_read'] = [];
		foreach ( $categories as $tmpObj ) {
			$tmp_data                                               = $tmpObj->getData();
			$this->assign['categories_can_read'][ $tmp_data['id'] ] = str_repeat( '--', $tmp_data['cat_depth_in_tree'] ) . $tmp_data['cat_title'];
		}

		// count up 'viewed' - comment out if 'modifier_ip' for local views
		if ( $content_data['modifier_ip'] !== @$_SERVER['REMOTE_ADDR'] ) {
			$contentObj->incrementViewed();
		}

		// breadcrumbs
		$breadcrumbsObj = AltsysBreadcrumbs::getInstance();
		$breadcrumbsObj->appendPath( '', $this->assign['content']['subject'] );
		$this->assign['xoops_breadcrumbs'] = $breadcrumbsObj->getXoopsbreadcrumbs();
		$this->assign['xoops_pagetitle']   = $this->assign['content']['subject'];

		// views
		switch ( $request['view'] ) {
			case 'singlecontent':
				$this->template_name         = 'db:' . $this->mydirname . '_independent_single_content.html';
				$this->is_need_header_footer = false;
				break;
			case 'print':
				$this->template_name         = 'db:' . $this->mydirname . '_independent_print.html';
				$this->is_need_header_footer = false;
				break;
			default:
				$this->template_name         = $this->mydirname . '_main_view_content.html';
				$this->is_need_header_footer = true;
				break;
		}

		// htmlheader
		if ( ! empty( $this->mod_config['allow_each_htmlheader'] ) ) {
			$this->html_header .= $content_data['htmlheader'];
		}
	}
}
