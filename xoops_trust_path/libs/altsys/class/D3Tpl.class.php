<?php

require_once XOOPS_ROOT_PATH.'/class/template.php' ;
require_once XOOPS_TRUST_PATH.'/libs/altsys/include/altsys_functions.php' ;

class D3Tpl extends XoopsTpl {

	function D3Tpl() {
		parent::XoopsTpl() ;
		if( in_array( altsys_get_core_type() , array( ALTSYS_CORE_TYPE_X20S , ALTSYS_CORE_TYPE_X23P ) ) ) {
			array_unshift( $this->plugins_dir , XOOPS_TRUST_PATH.'/libs/altsys/smarty_plugins' ) ;
		}

		// for RTL users
		@define( '_GLOBAL_LEFT' , @_ADM_USE_RTL == 1 ? 'right' : 'left' ) ;
		@define( '_GLOBAL_RIGHT' , @_ADM_USE_RTL == 1 ? 'left' : 'right' ) ;
	}
}

?>