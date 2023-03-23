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

class PicoControllerAbstract {

	public $mydirname = '';
	public $mytrustdirname = 'pico';
	public $assign = [];
	public $mod_config = [];
	public $uid = 0;
	public $currentCategoryObj = null;
	public $permissions = [];
	public $is_need_header_footer = true;
	public $template_name = '';
	public $html_header = '';
	public $contentObjs = [];

	public function __construct( &$currentCategoryObj ) {
		global $xoopsUser;

		$this->currentCategoryObj = &$currentCategoryObj;
		$this->mydirname          = $currentCategoryObj->mydirname;
		$this->mod_config         = $currentCategoryObj->getOverriddenModConfig();
		$this->uid                = is_object( $xoopsUser ) ? $xoopsUser->getVar( 'uid' ) : 0;

		$picoPermission      = &PicoPermission::getInstance();
		$this->permissions   = $picoPermission->getPermissions( $this->mydirname );
		$this->assign        = [
			'mymodname'      => htmlspecialchars( $currentCategoryObj->mod_name, ENT_QUOTES ),
			'mydirname'      => $this->mydirname,
			'mytrustdirname' => $this->mytrustdirname,
			'mod_url'        => XOOPS_URL . '/modules/' . $this->mydirname,
			'mod_imageurl'   => XOOPS_URL . '/modules/' . $this->mydirname . '/' . $this->mod_config['images_dir'],
			'xoops_config'   => $GLOBALS['xoopsConfig'],
			'mod_config'     => $this->mod_config,
			'uid'            => $this->uid,
		];
		$this->template_name = $this->mydirname . '_index.html';
	}

	public function execute( $request ) {
		// abstract (must override it)
	}

	public function render( $target = null ) {
		require_once XOOPS_ROOT_PATH . '/class/template.php';

		$tpl = new XoopsTpl();

		$tpl->assign( $this->getAssign() );
		$tpl->assign( 'xoops_module_header', pico_main_render_moduleheader( $this->mydirname, $GLOBALS['xoopsModuleConfig'], $this->getHtmlHeader() ) );
		$tpl->display( $this->getTemplateName() );
	}

	public function isNeedHeaderFooter(): bool {
		return $this->is_need_header_footer;
	}

	public function getTemplateName(): string {
		$template_name = $this->template_name;

		// calling a delegate for replacing the main template
		if ( class_exists( 'XCube_DelegateUtils' ) ) {
			XCube_DelegateUtils::raiseEvent( 'ModuleClass.Pico.Controller.GetTemplateName', $this->mydirname, new XCube_Ref( $template_name ) );
		}

		return $template_name;
	}

	public function getAssign(): array {
		foreach ( $this->contentObjs as $index => $contentObj ) {
			if ( ! is_object( $contentObj ) ) {
				continue;
			}
			if ( $contentObj->need_filter_body ) {
				$this->assign[ $index ]['body'] = $contentObj->filterBody( $contentObj->getData4html() );
			}
		}

		return $this->assign;
	}

	public function getHtmlHeader(): string {
		return $this->html_header;
	}

	public function exitFileNotFound(): void {
		$error404 = $this->mod_config['err_document_404'];
		if ( ! empty( $error404 ) ) {
			$error404 = preg_replace( '#^root_path#i', XOOPS_ROOT_PATH, $error404 );
			$error404 = preg_replace( '#^trust_path#i', XOOPS_TRUST_PATH, $error404 );
		}
		if ( $error404 && is_readable( $error404 ) ) {
            // Note
			// Do not mix the use of http_response_code() and manually setting the response code header to avoid unexpected status code
            // header( 'HTTP/1.0 404 Not Found' );
            http_response_code(404);
			readfile( $error404 );
		} else {
			redirect_header( XOOPS_URL . "/modules/$this->mydirname/index.php", 2, _MD_PICO_ERR_READCONTENT );
		}
		exit;
	}
}
