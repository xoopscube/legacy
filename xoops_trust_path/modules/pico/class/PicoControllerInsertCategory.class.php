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

class PicoControllerInsertCategory extends PicoControllerAbstract {

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

	public $new_cat_id = - 1;

	public function execute( $request ) {
		// Ticket Check
		if ( ! $GLOBALS['xoopsGTicket']->check( true, 'pico' ) ) {
			redirect_header( XOOPS_URL . '/', 3, $GLOBALS['xoopsGTicket']->getErrors() );
		}

		parent::execute( $request );

		// initialize
		$pcat_data = $this->currentCategoryObj->getData();

		// permission check
		if ( empty( $pcat_data['can_makesubcategory'] ) ) {
			redirect_header( XOOPS_URL . '/', 2, _MD_PICO_ERR_MAKECATEGORY );
		}

		// create category
		$this->new_cat_id = pico_makecategory( $this->mydirname );

		// view
		$this->is_need_header_footer = false;
	}

	public function render( $target = null ) {
		redirect_header( XOOPS_URL . "/modules/$this->mydirname/" . pico_common_make_category_link4html( $this->mod_config, $this->new_cat_id, $this->mydirname ), 2, _MD_PICO_MSG_CATEGORYMADE );
		exit;
	}
}
