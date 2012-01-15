<?php
/*
 * Created on 2007/12/21 by nao-pon http://hypweb.net/
 * $Id: d3pipes.inc.php,v 1.2 2012/01/14 03:38:10 nao-pon Exp $
 */

require_once XOOPS_TRUST_PATH . '/modules/d3pipes/joints/D3pipesBlockAbstract.class.php' ;

class D3pipesBlockXpwikipagesSubstance extends D3pipesBlockAbstract {

    var $target_dirname = '' ;
    var $trustdirname = 'xpwiki' ;

    function init()
    {
        // parse and check option for this class
        $params = array_map( 'trim' , explode( '|' , $this->option ) ) ;
        if( empty( $params[0] ) ) {
            $this->errors[] = _MD_D3PIPES_ERR_INVALIDDIRNAMEINBLOCK."\n($this->pipe_id)" ;
            return false ;
        }
        $this->target_dirname = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , $params[0] ) ;

        // configurations (file, name, block_options)
        $this->func_file = XOOPS_TRUST_PATH.'/modules/'.$this->trustdirname.'/include/stand_alone_functions.php' ;
        $this->func_name = 'xpwiki_saf_getRecentPages_base' ;

        $as_guest = (@ $params[3] === 'No')? false : true;
        $this->block_options = array(
            0 => $this->target_dirname , // mydirname
            1 => strval(@ $params[1]) ,  // base page
            2 => empty( $params[2] ) ? 10 : intval( $params[2] ) , // max_entries
            3 => (@ $params[3] === 'No')? false : true , // Get as guest always
            4 => empty( $params[4] ) ? 0 : 1 , // get body too
        ) ;

        return true ;
    }

    function reassign( $data )
    {
        $entries = array() ;
        foreach( $data['entries'] as $entry ) {
            $entry['fingerprint'] = $entry['link'] ;
            $entries[] = $entry ;
        }

        return $entries ;
    }

    function renderOptions( $index , $current_value = null )
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

        // options[1]  (base page)
        $options[1] = preg_replace( '/[^0-9a-zA-Z_-]/' , '' , @$options[1] ) ;
        $ret_1 = 'Base page'.'<input type="text" name="joint_options['.$index.'][1]" value="'.$options[1].'" size="15" />' ;

        // options[2]  (max_entries)
        $options[2] = empty( $options[2] ) ? 10 : intval( $options[2] ) ;
        $ret_2 = _MD_D3PIPES_N4J_MAXENTRIES.'<input type="text" name="joint_options['.$index.'][2]" value="'.$options[2].'" size="2" style="text-align:right;" />' ;

        // options[3]  (Get as guest?)
        $options[3] = ( @$options[3] === 'No' ) ? 'No' : 'Yes' ;
        $ret_3 = 'Get as guest always?'.'<select name="joint_options['.$index.'][3]">' ;
        foreach( array('Yes', 'No') as $name ) {
            $ret_3 .= '<option value="'.$name.'" '.($name === $options[3]?'selected="selected"':'').'>'.$name.'</option>' ;
        }
        $ret_3 .= '</select>' . '(Never cache it when you select "No".)';

		// options[4]  (with body or not)
		$ret_4 = '<input type="checkbox" name="joint_options['.$index.'][4]" value="1" '.(empty($options[4])?'':'checked="checked"').' /> ' . _MD_D3PIPES_N4J_WITHDESCRIPTION ;

        return '<input type="hidden" name="joint_option['.$index.']" id="joint_option_'.$index.'" value="" />'.$ret_0.' '.$ret_1.' '.$ret_2.'<br />'.$ret_3.'<br />'.$ret_4 ;
    }

}

?>