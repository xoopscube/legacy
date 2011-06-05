<?php

require_once dirname(__FILE__).'/MyBlocksAdmin.class.php' ;

class MyBlocksAdminForXCL21 extends MyBlocksAdmin {


function MyBlocksAadminForXCL21()
{
}


function &getInstance()
{
	static $instance;
	if (!isset($instance)) {
		$instance = new MyBlocksAdminForXCL21();
		$instance->construct() ;
	}
	return $instance;
}


// virtual
// options
function renderCell4BlockOptions( $block_data )
{
	if( $this->target_dirname && substr( $this->target_dirname , 0 , 1 ) != '_' ) {
		$langman =& D3LanguageManager::getInstance() ;
		$langman->read( 'admin.php' , $this->target_dirname ) ;
	}

	$bid = intval( $block_data['bid'] ) ;

	$block = new XoopsBlock( $bid ) ;
	$legacy_block =& Legacy_Utils::createBlockProcedure( $block ) ;
	return $legacy_block->getOptionForm() ;
}




}

?>