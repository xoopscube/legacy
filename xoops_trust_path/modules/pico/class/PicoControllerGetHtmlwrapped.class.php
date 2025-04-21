<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.5.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2025 Authors
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

require_once __DIR__ . '/PicoControllerAbstract.class.php';
require_once __DIR__ . '/PicoModelCategory.class.php';
require_once __DIR__ . '/PicoModelContent.class.php';

// HTML wrapping without DB
class PicoControllerGetHtmlwrapped extends PicoControllerAbstract {

	public function execute( $request ) {
		parent::execute( $request );

		$this->assign['content'] = $this->readWrappedFile( $request );

		// check existence
		if ( empty( $this->assign['content'] ) ) {
			$this->exitFileNotFound();
		}

		$cat_data                 = $this->currentCategoryObj->getData();
		$this->assign['category'] = $this->currentCategoryObj->getData4html();

		// permission check
		if ( empty( $this->assign['content']['can_read'] ) || empty( $this->assign['content']['can_readfull'] ) ) {
			if ( $this->uid > 0 ) {
				redirect_header( XOOPS_URL . '/', 1, _MD_PICO_ERR_PERMREADFULL );
			} else {
				redirect_header( XOOPS_URL . '/user.php', 1, _MD_PICO_ERR_LOGINTOREADFULL );
			}
			exit;
		}

		// auto-register
		if ( ! empty( $this->mod_config['wraps_auto_register'] ) && '/' === @$cat_data['cat_vpath'][0] ) {
			$register_class = empty( $this->mod_config['auto_register_class'] ) ? 'PicoAutoRegisterWraps' : $this->mod_config['auto_register_class'];
			require_once __DIR__ . '/' . $register_class . '.class.php';
			$register_obj  = new $register_class( $this->mydirname, $this->mod_config );
			$affected_rows = $register_obj->registerByCatvpath( $cat_data );
			if ( $affected_rows > 0 ) {
				// reload if the content is updated
				header( 'Location: ' . pico_common_unhtmlspecialchars( $this->assign['mod_url'] ) . '/' . pico_common_unhtmlspecialchars( $this->assign['content']['link'] ) );
				exit;
			}
		}

		// link for "tell to friends"
		// (TODO?)
		if ( $this->mod_config['use_taf_module'] ) {
			$this->assign['content']['tellafriend_uri'] = XOOPS_URL . '/modules/tellafriend/index.php?target_uri=' . rawurlencode( pico_common_unhtmlspecialchars( $this->assign['mod_url'] . '/' . $this->assign['content']['link'] ) ) . '&amp;subject=' . rawurlencode(
					sprintf( _MD_PICO_FMT_TELLAFRIENDSUBJECT, @$GLOBALS['xoopsConfig']['sitename'] )
				);
		} else {
			$this->assign['content']['tellafriend_uri'] = 'mailto:?subject='
			                                              . pico_main_escape4mailto( sprintf( _MD_PICO_FMT_TELLAFRIENDSUBJECT, @$GLOBALS['xoopsConfig']['sitename'] ) )
			                                              . '&amp;body='
			                                              . pico_main_escape4mailto( sprintf( _MD_PICO_FMT_TELLAFRIENDBODY, $this->assign['content']['subject'] ) )
			                                              . '%0A'
			                                              . rawurlencode( pico_common_unhtmlspecialchars( $this->assign['mod_url'] . '/' . $this->assign['content']['link'] ) );
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
	}

	public function readWrappedFile( $request ): array {
		$wrap_full_path = XOOPS_TRUST_PATH . _MD_PICO_WRAPBASE . '/' . $this->mydirname . $request['path_info'];

		ob_start();
		include $wrap_full_path;
		$full_content = pico_convert_encoding_to_ie( ob_get_clean() );

		// parse full_content (get subject, body etc.)
		$file  = substr( strrchr( $wrap_full_path, '/' ), 1 );
		$mtime = (int) @filemtime( $wrap_full_path );
		if ( preg_match( '/\<title\>([^<>]+)\<\/title\>/is', $full_content, $regs ) ) {
			$subject = $regs[1];
		} else {
			$subject = $file;
		}
		if ( preg_match( '/\<body[^<>]*\>(.*)\<\/body\>/is', $full_content, $regs ) ) {
			$body = $regs[1];
		} else {
			$body = $full_content;
		}

		$cat_data = $this->currentCategoryObj->getData();

		$link = empty( $this->mod_config['use_rewrite'] ) ? 'index.php' . $request['path_info'] : substr( $request['path_info'], 1 );

		return [
			'id'                     => 0,
			'link'                   => $link,
			'created_time'           => $mtime,
			'created_time_formatted' => formatTimestamp( $mtime ),
			'subject_raw'            => pico_common_unhtmlspecialchars( $subject ),
			'subject'                => $subject,
			'body'                   => $body,
			'can_read'               => $cat_data['isadminormod'] || $cat_data['can_read'],
			'can_readfull'           => $cat_data['isadminormod'] || $cat_data['can_readfull'],
			'can_edit'               => false,
			'can_vote'               => false,
		];
	}
}
