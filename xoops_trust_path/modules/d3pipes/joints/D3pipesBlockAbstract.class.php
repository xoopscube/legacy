<?php

require_once dirname(__FILE__).'/D3pipesJointAbstract.class.php' ;

class D3pipesBlockAbstract extends D3pipesJointAbstract {

	var $option ;
	var $func_file = '' ;
	var $func_name = '' ;
	var $target_dirname = '' ;
	var $block_options = array() ;
	var $db ;
//XCL block mode
	var $class_name = '' ;

	// constructor
	function D3pipesBlockAbstract( $mydirname , $pipe_id , $option )
	{
		$this->mydirname = $mydirname ;
		$this->pipe_id = intval( $pipe_id ) ;
		$this->option = $option ;
		$this->db =& Database::getInstance() ;
	}
	function execute( $dummy = '' , $max_entries = '' )
	{
		$ret="";
		if (defined('LEGACY_MODULE_VERSION') && version_compare(LEGACY_MODULE_VERSION, '2.1', '>=')) {
			$ret= $this->executeXCL2CLASS( $dummy , $max_entries );
		}else{
			$ret= $this->executeStandard($dummy , $max_entries );
		}
		return $ret;
	}
	function executeStandard( $dummy = '' , $max_entries = '' )
	{
		if( ! $this->init() ) {
			return array() ;
		}

		// file check
		if( ! file_exists( $this->func_file ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDFILEINBLOCK."\n".$this->func_file.' ('.get_class( $this ).')' ;
			return array() ;
		}
		require_once $this->func_file ;

		// function check
		if( ! function_exists( $this->func_name ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDFUNCINBLOCK."\n".$this->func_name.' ('.get_class( $this ).')' ;
			return array() ;
		}

		$block = call_user_func( $this->func_name , $this->block_options ) ;

		// update lastfetch_time
		$db =& Database::getInstance() ;
		$db->queryF( "UPDATE ".$db->prefix($this->mydirname."_pipes")." SET lastfetch_time=UNIX_TIMESTAMP() WHERE pipe_id=$this->pipe_id" ) ;

		return $this->reassign( $block ) ;
	}

	function executeXCL2CLASS( $dummy = '' , $max_entries = '' )
	{

		if( ! $this->init() ) {
			return array() ;
		}

		// file check
		if( ! file_exists( $this->func_file ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDFILEINBLOCK."\n".$this->func_file.' ('.get_class( $this ).')' ;
			return array() ;
		}
		require_once $this->func_file ;

		//d3module and function type module check
		if( function_exists( $this->func_name ) || array_key_exists ( 'disable_renderer' , $this->block_options )) {
			$block = $this->executeStandard($dummy , $max_entries );
			return $block;
		}

		//----------  get block object  ----------//
		//XCL modules
		$db =& XoopsDatabaseFactory::getDatabaseConnection();
		//chanhe class_name -> func_name
		//only no xoos_trust_path module class type , convert class_name to func_name
		//you need to set $this->func_name when xoos_trust_path module class type
		if ( !empty($this->class_name) && empty($this->func_name)) {
			if ( !empty($this->trustdirname)) {
				$this->func_name = 'cl::'.preg_replace('/^'.$this->trustdirname.'_/i','',$this->class_name ) ;
			}else{
				$this->func_name = 'cl::'.preg_replace('/^'.$this->target_dirname.'_/i','',$this->class_name ) ;
			}
		}

		//get bid
		$sql = "SELECT bid FROM " .$db->prefix("newblocks"). " WHERE dirname=".$db->quoteString($this->target_dirname)." AND show_func=".$db->quoteString($this->func_name)." AND block_type='M' ";
		$result = $db->query($sql);
		list( $bid ) = $db->fetchRow($result);

		// bid check old function
		if( empty( $bid ) ) {
			$block = $this->executeStandard($dummy , $max_entries );
			return $block;
		}
		//----------  module object check ----------//
		$module_handler =& xoops_gethandler('module');
		$module = $module_handler->getByDirname($this->target_dirname);
		if (!is_object($module) || !$module->getVar('isactive')){
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDPIPEIDINBLOCK."\n".' modulet not found : target_dirname='.$this->target_dirname.' func_name='.$this->func_name.' ('.get_class( $this ).')' ;
			return array() ;
		}
		//----------  get block object  ----------//
		$blockHandler =& xoops_gethandler('block');
		$blockObject =& $blockHandler->get($bid);
		if ( ! is_object($blockObject)) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDPIPEIDINBLOCK."\n".' block object not found : target_dirname='.$this->target_dirname.' func_name='.$this->func_name.' ('.get_class( $this ).')' ;
			return array() ;
		}

		//XCL AND other Xoops single module
		$options_separated = implode('|', $this->block_options);
		$blockObject->set('options',$options_separated);
		//get tager of block
		$blockProcedure =& Legacy_Utils::createBlockProcedure($blockObject);
		$blockProcedure->prepare();

		$blockProcedure->execute();
		$target =& $blockProcedure->getRenderTarget();
		$buffer = $target->getAttributes() ;

		//class type
		$block = $buffer;
		// update lastfetch_time
		$db->queryF( "UPDATE ".$db->prefix($this->mydirname."_pipes")." SET lastfetch_time=UNIX_TIMESTAMP() WHERE pipe_id=$this->pipe_id" ) ;

		return $this->reassign( $block ) ;
	}

	// virtual
	function init() {}

	// virtual
	function reassign() {}

	function unhtmlspecialchars( $data )
	{
		if( is_array( $data ) ) {
			return array_map( array( $this , 'unhtmlspecialchars' ) , $data ) ;
		} else {
			return str_replace(
				array( '&lt;' , '&gt;' , '&amp;' , '&quot;' , '&#039;' ) ,
				array( '<' , '>' , '&' , '"' , "'" ) ,
				$data ) ;
		}
	}

	function strToServerTime( $text )
	{
		return strtotime( $text ) - xoops_getUserTimestamp( 0 ) ;
	}

	// returns array of dirnames can be applied the joint
	function getValidDirnames()
	{
		$ret = array() ;
		$module_handler =& xoops_gethandler( 'module' ) ;
		$modules = $module_handler->getList( null , true ) ;

		if( ! empty( $this->trustdirname ) ) {
			foreach( array_keys( $modules ) as $mydirname ) {
				$trustpath_file = XOOPS_ROOT_PATH.'/modules/'.$mydirname.'/mytrustdirname.php' ;
				if( ! file_exists( $trustpath_file ) ) continue ;
				$mytrustdirname = '' ;
				require $trustpath_file ;
				if( $mytrustdirname == $this->trustdirname ) $ret[] = $mydirname ;
			}
		} else {
			$dirname = strtolower( substr( get_class( $this ) , strlen('D3pipesBlock') ) ) ;
			if( isset( $modules[ $dirname ] ) ) $ret[] = $dirname ;
		}

		return $ret ;
	}

	function includeLanguageBlock()
	{
		$langmanpath = XOOPS_TRUST_PATH.'/libs/altsys/class/D3LanguageManager.class.php' ;
		if( ! file_exists( $langmanpath ) ) die( 'install the latest altsys' ) ;
		require_once( $langmanpath ) ;
		$langman =& D3LanguageManager::getInstance() ;
		$langman->read( 'blocks.php' , $this->target_dirname ) ;
	}

	function refetchPubtime( $table , $time_field , $pkey_name , $id )
	{
		list( $time ) = $this->db->fetchRow( $this->db->query( "SELECT `$time_field` FROM `".$this->db->prefix($table)."` WHERE `$pkey_name`='$id'" ) ) ;
		return intval( $time ) ;
	}

	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;
		$options = explode( '|' , $current_value , 3 ) ;

		// options[0]  (dirname)
		$dirnames = $this->getValidDirnames() ;
		$ret_0 = '<select name="joint_options['.$index.'][0]">' ;
		foreach( $dirnames as $dirname ) {
			$ret_0 .= '<option value="'.$dirname.'" '.($dirname==@$options[0]?'selected="selected"':'').'>'.$dirname.'</option>' ;
		}
		$ret_0 .= '</select>' ;

		// options[1]  (max_entries)
		$options[1] = empty( $options[1] ) ? 10 : intval( $options[1] ) ;
		$ret_1 = _MD_D3PIPES_N4J_MAXENTRIES.'<input type="text" name="joint_options['.$index.'][1]" value="'.$options[1].'" size="2" style="text-align:right;" />' ;
		// options[2],[3]... (extra options)
		$extra_options = empty( $options[2] ) ? '' : $options[2] ;
		$ret_2 = _MD_D3PIPES_N4J_EXTRAOPTIONS.'<input type="text" name="joint_options['.$index.'][2]" value="'.htmlspecialchars($extra_options,ENT_QUOTES).'" size="20" />' ;

		return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0.' &nbsp; '.$ret_1.'<br />'.$ret_2 ;
	}

}


?>