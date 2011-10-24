<?php

require_once dirname(dirname(__FILE__)).'/D3pipesUnionAbstract.class.php' ;
require_once dirname(dirname(dirname(__FILE__))).'/include/common_functions.php' ;

class D3pipesUnionTheotherd3pipes extends D3pipesUnionAbstract {

	var $target_dirname = '' ;

	// constructor
	function D3pipesUnionTheotherd3pipes( $mydirname , $pipe_id , $option = '' )
	{
		$options = explode( '|' , $option ) ;
		$this->target_dirname = empty( $options[3] ) ? $mydirname : trim( $options[3] ) ;
		parent::D3pipesUnionAbstract( $mydirname , $pipe_id , $option ) ;
	}

	// $max_entires : max entries aggregated
	function execute( $entries , $max_entries = 10 )
	{
		foreach( $this->union_ids as $union_ids ) {
			$pipe4assign = d3pipes_common_get_pipe4assign( $this->target_dirname , $union_ids['pipe_id'] ) ;
			$entries_tmp = d3pipes_common_fetch_entries( $this->target_dirname , $pipe4assign , $union_ids['num'] , $errors , $this->mod_configs ) ;
			$this->errors = array_merge( $this->errors , $errors ) ;
			$entries_tmp = $this->appendPipeInfoIntoEntries( $entries_tmp , $pipe4assign ) ;
			$entries = is_array( $entries ) ? array_merge( $entries , $entries_tmp ) : $entries_tmp ;
		}

		// not sorted

		return array_slice( $entries , 0 , $max_entries ) ;
	}

 	// append <input> for dirname
	function renderOptions( $index , $current_value = null )
	{
		$options = explode( '|' , $current_value ) ;
		$this->target_dirname = @$options[3] ;

		// make list for d3pipes
		$module_handler =& xoops_gethandler( 'module' ) ;
		$modules = $module_handler->getList( null , true ) ;
		$select_options = '' ;
		foreach( array_keys( $modules ) as $dirname ) {
			if( ! file_exists( XOOPS_ROOT_PATH.'/modules/'.$dirname.'/mytrustdirname.php' ) ) continue ;
			$mytrustdirname = '' ;
			include XOOPS_ROOT_PATH.'/modules/'.$dirname.'/mytrustdirname.php' ;
			if( $mytrustdirname != 'd3pipes' ) continue ;

			$selected = $dirname == $this->target_dirname ? 'selected="selected"' : '' ;
			$dirname4disp = htmlspecialchars( $dirname , ENT_QUOTES ) ;
			$select_options .= "<option value='$dirname4disp' $selected > $dirname4disp </option>" ;
		}

		$ret_3 = '<label>'._MD_D3PIPES_N4J_TARGETMODULE.':<select name="joint_options['.$index.'][3]">'.$select_options.'</select></label>' ;

		$ret = parent::renderOptions( $index , $current_value ) ;
		return $ret . '<br />' . $ret_3 ;
	}

}

?>