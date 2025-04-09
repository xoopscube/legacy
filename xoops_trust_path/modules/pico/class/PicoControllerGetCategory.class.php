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

class PicoControllerGetCategory extends PicoControllerAbstract {

	public function execute( $request ) {
		parent::execute( $request );

		// check existence
		if ( $this->currentCategoryObj->isError() ) {
			redirect_header( XOOPS_URL . "/modules/$this->mydirname/index.php", 1, _MD_PICO_ERR_READCATEGORY );
			exit;
		}

		$cat_data                 = $this->currentCategoryObj->getData();
		$this->assign['category'] = $this->currentCategoryObj->getData4html();

		// permission check
		if ( ! $cat_data['can_read'] ) {
			redirect_header( XOOPS_URL . "/modules/$this->mydirname/index.php", 1, _MD_PICO_ERR_READCATEGORY );
			exit;
		}

		// auto-register
		if ( ! empty( $this->mod_config['wraps_auto_register'] )
		     && '/' === @$cat_data['cat_vpath'][0] ) {
			$register_class = empty( $this->mod_config['auto_register_class'] ) ? 'PicoAutoRegisterWraps' : $this->mod_config['auto_register_class'];
			require_once __DIR__ . '/' . $register_class . '.class.php';
			$register_obj = new $register_class( $this->mydirname, $this->mod_config );
			$register_obj->registerByCatvpath( $cat_data );
		}

		// contents
		$this->assign['contents'] = [];
		$contentObjs              = $this->currentCategoryObj->getContents();
		foreach ( $contentObjs as $contentObj ) {
			$this->assign['contents'][] = $contentObj->getData4html();
		}

		// subcategories
		$categoryHandler = new PicoCategoryHandler( $this->mydirname, $this->permissions );
		$subcategoryObjs = $categoryHandler->getSubCategories( $request['cat_id'] );
		foreach ( $subcategoryObjs as $subcategoryObj ) {
			$this->assign['subcategories'][] = $subcategoryObj->getData4html();
		}

		$breadcrumbsObj                    = AltsysBreadcrumbs::getInstance();
		$this->assign['xoops_breadcrumbs'] = $breadcrumbsObj->getXoopsbreadcrumbs();
		$this->assign['xoops_pagetitle']   = $this->assign['category']['title'];

		// views (no views other than 'listcontents')
		$this->template_name         = $this->mydirname . '_main_list_contents.html';
		$this->is_need_header_footer = true;
	}
}
