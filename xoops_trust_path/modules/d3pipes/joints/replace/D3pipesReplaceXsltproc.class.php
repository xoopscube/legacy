<?php

require_once dirname(dirname(__FILE__)).'/D3pipesReplaceAbstract.class.php' ;

class D3pipesReplaceXsltproc extends D3pipesReplaceAbstract {

	function execute( $data , $max_entries = '' )
	{
		if( is_array( $data ) ) {
			die( 'This joint can be used only for HTML text '.class_name($this) ) ;
		}

		$process = proc_open( escapeshellcmd($this->mod_configs['xsltproc_path']).' '.escapeshellarg($this->option).' -' , array( array('pipe','r') , array('pipe','w') , array('pipe','r') ) , $pipes ) ;
		fwrite( $pipes[0] , $data ) ;
		fclose( $pipes[0] ) ;
		$ret = '' ;
		while( ! feof( $pipes[1] ) ) {
			$ret .= fread( $pipes[1] , 8192 ) ;
		}
		fclose( $pipes[1] ) ;
		proc_close( $process ) ;

		return $ret ;
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;

		return '<input type="text" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" size="40" /><br />'._MD_D3PIPES_N4J_XSLTPATH ;
	}
}

?>