<?php
/**
 * @version $Id: D3pipesBlockXccklist.class.php ,ver0.01 2012-01-05 20:55:00 domifara $
 * @brief d3pipes plugin for xcck module
 * @@author domifara
 */

if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH into mainfile.php' );

require_once dirname(dirname(dirname(__FILE__))).'/joints/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockXccklist extends D3pipesBlockAbstract {

	var $target_dirname = '';
	var $trustdirname = 'xcck';

	function init()
	{
		// parse and check option for this class
		$params = array_map( 'trim' , explode( '|' , $this->option ) ) ;
		if( empty( $params[0] ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDDIRNAMEINBLOCK."\n($this->pipe_id)" ;
			return false ;
		}
		$this->target_dirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $params[0] ) ;
		// language files
		$this->includeLanguageBlock() ;

		// configurations (file, name, block_options)
		$this->func_file = XOOPS_TRUST_PATH.'/modules/'.$this->trustdirname.'/blocks/ListBlock.class.php' ;

		// when d3module class name is Xcck_ListBlock -> your need to set func_name = 'cl::*'
		$this->func_name = 'cl::ListBlock' ;

		if (!isset($options[2])){
			$options[2] = '' ;//show all
		}
		$categories = empty($options[2]) ? '' : implode(',',array_map( 'intval' , explode( ',' , $options[2] ) )) ;

		$this->block_options = array(
			0 =>  empty( $params[1] ) ? 5 : intval( $params[1] ),  // max entries
			1 => empty( $params[2] ) ? '' : $categories , // cat_ids
			2 => empty( $params[3] ) ? 1 :  intval( $params[3] ) // display order num
			) ;

		return true ;
	}

	function reassign($data)
	{
		$entries = array();
		if(is_array($data)) {
			foreach( $data['block'] as $obj ) {
				$entry = array(
						'pubtime' => $obj->getShow('posttime'), // unix timestamp
						'link' => XOOPS_MODULE_URL.'/'.$this->target_dirname.'/index.php?action=PageView&page_id='.$obj->getShow('page_id'),
						'headline' => $obj->getShow('title'),
						);
				$entry['fingerprint'] = $entry['link'] ;
				$entries[] = $entry ;
			}
		}
		return $entries ;
	}

	// returns array of dirnames can be applied the joint
	function renderOptions($index, $current_value = null)
	{
		$index = intval( $index ) ;
		$options = explode( '|' , $current_value ) ;

		// options[0]  (dirname)
		$dirnames = $this->getValidDirnames() ;
		$ret_0 = '<select name="joint_options['.$index.'][0]">' ;
		foreach( $dirnames as $dirname ) {
			$ret_0 .= '<option value="'.$dirname.'" '.($dirname==@$options[0]?'selected="selected"':'').'>'.$dirname.'</option>' ;
		}
		$ret_0 .= '</select>' ;

		// options[1]  (max_entries)
		$options[1] = !isset( $options[1] ) ? 5 : intval( $options[1] ) ;
		$ret_1 = _MD_D3PIPES_N4J_MAXENTRIES.'<input type="text" name="joint_options['.$index.'][1]" value="'.$options[1].'" size="2" style="text-align:right;" />' ;

		// options[2]  (cat_ids)
		$options[2] = !isset( $options[2] ) ? '' : $options[2] ;
		$options[2] = preg_replace( '/[^0-9,]/' , '' , @$options[2] ) ;
		$ret_2 = _MD_D3PIPES_N4J_CID.'<input type="text" name="joint_options['.$index.'][2]" value="'.$options[2].'" size="20" />' ;

		// options[3]  (display)
		$options[3] = !isset($options[3]) ? 1 : intval($options[3]);
		$ret_3 = _MD_D3PIPES_N4J_EXTRAOPTIONS.'<input type="text" name="joint_options['.$index.'][3]" value="'.$options[3].'" size="10" style="text-align:left;" />' ;

			return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0.' '.$ret_1.'<br />'.$ret_2.' '.$ret_3 ;
	}


	// returns array of dirnames can be applied the joint
	function getValidDirnames()
	{
		$ret = array() ;
		$module_handler =& xoops_gethandler( 'module' ) ;
		$modules = $module_handler->getObjects() ;

		foreach( $modules as $module ) {
			$trust_dirname = $module->getVar( 'trust_dirname' ) ;
			if( $trust_dirname == $this->trustdirname ) {
				$ret[] = $module->getVar('dirname') ;
			}
		}

		return $ret ;
	}

}

?>