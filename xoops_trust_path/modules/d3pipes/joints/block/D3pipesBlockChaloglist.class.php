<?php
/**
 * @version $Id: D3pipesBlockChaloglist.class.php ,ver0.01 2011-07-04 04:15:00 domifara $
 * @brief d3pipes plugin for chalog module
 * @@author domifara
 */

if( ! defined( 'XOOPS_TRUST_PATH' ) ) die( 'set XOOPS_TRUST_PATH into mainfile.php' );

require_once dirname(dirname(dirname(__FILE__))).'/joints/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockChaloglist extends D3pipesBlockAbstract {

	var $target_dirname = 'chalog';

	function init()
	{
		// parse and check option for this class
		$params = array_map( 'trim' , explode( '|' , $this->option ) ) ;
		if( empty( $params[0] ) ) {
			$this->errors[] = _MD_D3PIPES_ERR_INVALIDDIRNAMEINBLOCK."\n($this->pipe_id)" ;
			return false ;
		}
		// language files
		$this->includeLanguageBlock() ;

		// configurations (file, name, block_options)
		$this->func_file = XOOPS_MODULE_PATH.'/'.$this->target_dirname.'/blocks/recent.php' ;

		// chalog is none d3module module ,or your need to set $this->func_name = 'cl::*'
		$this->class_name = 'Chalog_RecentBlock' ;//same $this->func_name = 'cl::RecentBlock'

		$this->block_options = array(
			0 =>  empty( $params[0] ) ? 5 : intval( $params[0] )  // max entries
		) ;

		return true ;
	}

	function reassign($data)
	{
			$entries = array();
			if(is_array($data)) {
					foreach( $data['recentArr'] as $item ) {
							$entry = array(
									'pubtime' => $item->getShow('published'), // unix timestamp
									'link' => XOOPS_MODULE_URL.'/chalog/?action=BlogView&id='.$item->getShow('id'),
									'headline' => $item->getShow('title'),
									'description' => xoops_substr(strip_tags($item->getShow('comment')),0,255),
									'allow_html' => true,
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
			$index = intval($index);
			$options = explode('|', $current_value);

			// options[1]  (max_entries)
			$options[0] = !isset($options[0]) ? 5 : intval($options[0]);
			$ret_0 = _MD_D3PIPES_N4J_MAXENTRIES.'<input type="text" name="joint_options['.$index.'][0]" value="'.$options[0].'" size="2" style="text-align:right;" />' ;

			return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0 ;
	}


	// returns array of dirnames can be applied the joint
	function getValidDirnames()
	{
		return array( 'chalog' ) ;
	}

}

?>