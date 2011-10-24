<?php

class D3pipesJointAbstract {

	var $mydirname ;
	var $pipe_id ;
	var $mod_configs = array() ;
	var $errors = array() ;
	var $stage = 0 ;
	var $is_cached = false ;
	var $cached_body = array() ;

	function getErrors()
	{
		return $this->errors ;
	}

	function setModConfigs( $configs )
	{
		$this->mod_configs = $configs ;
	}

	function setStage( $stage )
	{
		$this->stage = $stage ;
	}

	// override it if the joint has caching system
	function isCached()
	{
		return $this->is_cached ;
	}

	// virtual
	function execute()
	{
		return array() ;
	}

	// override it if the joint has complex options
	function renderOptions( $index , $current_value = null )
	{
		$index = intval( $index ) ;

		return '<input type="text" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="'.htmlspecialchars($current_value,ENT_QUOTES).'" size="40" />' ;
	}

	function getMyname4disp()
	{
		$typeclass = substr( get_class( $this ) , strlen( 'D3pipes' ) ) ;

		return defined( '_MD_D3PIPES_CLASS_'.strtoupper( $typeclass ) ) ? constant( '_MD_D3PIPES_CLASS_'.strtoupper( $typeclass ) ) : $typeclass ;
	}


}


?>