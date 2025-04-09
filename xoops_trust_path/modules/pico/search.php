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

require_once __DIR__ . '/include/common_functions.php';
require_once __DIR__ . '/class/PicoTextSanitizer.class.php';
require_once __DIR__ . '/class/PicoModelContent.class.php';

eval( 'function ' . $mydirname . '_global_search( $keywords , $andor , $limit , $offset , $uid ) {	return pico_global_search_base( \'' . $mydirname . '\' , $keywords , $andor , $limit , $offset , $uid ) ;}' );


if ( ! function_exists( 'pico_global_search_base' ) ) {

	function pico_global_search_base( $mydirname, $keywords, $andor, $limit, $offset, $uid ) {
		// get this module's config
		$module_handler = xoops_gethandler( 'module' );

		$module = $module_handler->getByDirname( $mydirname );

		$config_handler = xoops_gethandler( 'config' );

		$configs = $config_handler->getConfigList( $module->mid() );

		// check xmobile or not
		$is_xmobile = false;

		if ( function_exists( 'debug_backtrace' ) && ( $backtrace = debug_backtrace() ) && strpos( $backtrace[2]['file'], '/xmobile/actions/' ) !== false ) {
			$is_xmobile = true;
		}

		// XOOPS Search module
		$showcontext = empty( $_GET['showcontext'] ) ? 0 : 1;

		// where by uid
		if ( ! empty( $uid ) ) {
			if ( empty( $configs['search_by_uid'] ) ) {
				return [];
			}
			$whr_uid = 'o.poster_uid=' . (int) $uid;
		} else {
			$whr_uid = '1';
		}

		// whereby keywords
		if ( is_array( $keywords ) && count( $keywords ) > 0 ) {
			switch ( strtolower( $andor ) ) {
				case 'and':
					$whr_kw = '';
					foreach ( $keywords as $keyword ) {
						$whr_kw .= "(`for_search` LIKE '%$keyword%') AND ";
					}
					$whr_kw .= '1';
					break;
				case 'or':
					$whr_kw = '';
					foreach ( $keywords as $keyword ) {
						$whr_kw .= "(`for_search` LIKE '%$keyword%') OR ";
					}
					$whr_kw .= '0';
					break;
				default:
					$whr_kw = "(`for_search` LIKE '%$keywords[0]%')";
					break;
			}
		} else {
			$whr_kw = 1;
		}

		$content_handler = new PicoContentHandler( $mydirname );

		$contents4assign = $content_handler->getContents4assign( "($whr_kw) AND ($whr_uid)", 'created_time DESC', $offset, $limit, false );

		$ret = [];

		foreach ( $contents4assign as $content ) {
			// get context for module "search"
			if ( function_exists( 'search_make_context' ) && $showcontext && $content['can_readfull'] ) {
				$full_context = strip_tags( @$content['body_cached'] );
				if ( function_exists( 'easiestml' ) ) {
					$full_context = easiestml( $full_context );
				}
				$context = search_make_context( $full_context, $keywords );
			} else {
				$context = '';
			}

			$ret[] = [
				'image'   => '',
				'link'    => $is_xmobile ? 'index.php?cat_id=' . $content['cat_id'] . '&content_id=' . $content['content_id'] : pico_common_make_content_link4html( $configs, $content ),
				'title'   => $content['subject'],
				'time'    => $content['created_time'],
				'uid'     => empty( $configs['search_by_uid'] ) ? 0 : $content['poster_uid'],
				'context' => $context,
			];
		}

		return $ret;
	}
}
