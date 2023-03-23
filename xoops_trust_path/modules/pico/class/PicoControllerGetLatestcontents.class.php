<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.3
 * @author     Other authors gigamaster, 2020 XCL/PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

require_once __DIR__ . '/PicoControllerAbstract.class.php';
require_once __DIR__ . '/PicoModelCategory.class.php';

class PicoControllerGetLatestcontents extends PicoControllerAbstract {

	//var $mydirname = '' ;
	//var $mytrustdirname = '' ;
	//var $assign = array() ;
	//var $mod_config = array() ;
	//var $uid = 0 ;
	//var $currentCategoryObj = null ;
	//var $permissions = array() ;
	//var $is_need_header_footer = true ;
	//var $template_name = '' ;
	//var $html_header = '' ;
	//var $contentObjs = array() ;

	public function execute( $request ) {
		parent::execute( $request );

		// check existence
		if ( $this->currentCategoryObj->isError() ) {
			redirect_header( XOOPS_URL . "/modules/$this->mydirname/index.php", 2, _MD_PICO_ERR_READCATEGORY );
			exit;
		}

		$cat_data                 = $this->currentCategoryObj->getData();
		$this->assign['category'] = $this->currentCategoryObj->getData4html();

		// permission check
		if ( ! $cat_data['can_read'] ) {
			redirect_header( XOOPS_URL . "/modules/$this->mydirname/index.php", 2, _MD_PICO_ERR_READCATEGORY );
			exit;
		}

		// contents (order by modified_time DESC)
		$this->assign['contents'] = [];
		$contentObjs              = $this->currentCategoryObj->getLatestContents( 10, true );
		foreach ( $contentObjs as $contentObj ) {
			$content_data = $contentObj->getData();
			if ( $content_data['can_read'] ) {
				$this->assign['contents'][] = [
					                              'body4rss'          => htmlspecialchars( xoops_substr( strip_tags( $content_data['body_cached'] ), 0, 191 ), ENT_QUOTES ),
					                              'created_time4rss'  => date( 'r', $content_data['created_time'] ),
					                              'modified_time4rss' => date( 'r', $content_data['modified_time'] ),
				                              ] + $contentObj->getData4html();
			}
		}

		// views
		if ( 'rss' === $request['view'] ) {
			$this->template_name         = 'db:' . $this->mydirname . '_independent_rss20.html';
			$this->is_need_header_footer = false;
			if ( function_exists( 'mb_http_output' ) ) {
				mb_http_output( 'pass' );
			}
			pico_common_utf8_encode_recursive( $this->assign );
			header( 'Content-Type:text/xml; charset=UTF-8' );
		} else {
			$this->template_name         = $this->mydirname . '_main_latestcontents.html';
			$this->is_need_header_footer = true;
		}
	}
}
