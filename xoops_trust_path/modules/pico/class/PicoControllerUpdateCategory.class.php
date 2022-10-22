<?php
/**
 * Pico content management D3 module for XCL
 *
 * @package    Pico
 * @version    XCL 2.3.1
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2022 Author
 * @license    https://github.com/xoopscube/xcl/blob/master/GPL_V2.txt
 */

require_once __DIR__ . '/PicoControllerAbstract.class.php';
require_once __DIR__ . '/PicoModelCategory.class.php';
require_once __DIR__ . '/gtickets.php';
require_once dirname( __DIR__ ) . '/include/transact_functions.php';

class PicoControllerUpdateCategory extends PicoControllerAbstract {

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

	public $cat_id = 0;

	public function execute( $request ) {
		// Ticket Check
		if ( ! $GLOBALS['xoopsGTicket']->check( true, 'pico' ) ) {
			redirect_header( XOOPS_URL . '/', 3, $GLOBALS['xoopsGTicket']->getErrors() );
		}

		parent::execute( $request );

		// initialize
		$this->cat_id = $request['cat_id'];

		// $categoryObj (not parent)
		$picoPermission = &PicoPermission::getInstance();
		$categoryObj    = new PicoCategory( $this->mydirname, $request['cat_id'], $picoPermission->getPermissions( $this->mydirname ) );

		// check error
		if ( $categoryObj->isError() ) {
			redirect_header( XOOPS_URL . "/modules/$this->mydirname/index.php", 2, _MD_PICO_ERR_READCONTENT );
			exit;
		}
		$cat_data = $categoryObj->getData();

		// permission check
		if ( empty( $cat_data['isadminormod'] ) ) {
			redirect_header( XOOPS_URL . '/', 2, _MD_PICO_ERR_CATEGORYMANAGER );
		}

		// update category
		pico_updatecategory( $this->mydirname, $this->cat_id );

		// view
		$this->is_need_header_footer = false;
	}

	public function render( $target = null ) {
		redirect_header( XOOPS_URL . "/modules/$this->mydirname/" . pico_common_make_category_link4html( $this->mod_config, $this->cat_id, $this->mydirname ), 2, _MD_PICO_MSG_CATEGORYUPDATED );
		exit;
	}
}
