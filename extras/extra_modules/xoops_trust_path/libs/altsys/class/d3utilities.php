<?php

class D3Utilities {

	var $dirname = '' ; // directory name under xoops_trust_path
	var $mydirname = '' ; // each directory name under xoops_root_path
	var $mid = 0 ; // id of each module instance
	var $table = '' ; // table with prefix and dirname
	var $primary_key = '' ; // column for primary_key
	var $cols = array() ; // settings of each columns
	var $form_mode = 'new' ; // 'new','edit' are available
	var $page_name = '' ; // controller's name  eg) page=(controller) in URI
	var $action_base_hiddens = array() ;

	function D3Utilities( $mydirname , $table_body , $primary_key , $cols , $page_name , $action_base_hiddens )
	{
		$db =& Database::getInstance() ;

		$this->dirname = basename( dirname( dirname( __FILE__ ) ) ) ;
		$this->mydirname = $mydirname ;
		$this->table = $db->prefix( $mydirname ? $mydirname . '_' . $table_body : $table_body ) ;
		$this->primary_key = $primary_key ;
		$this->cols = $cols ;
		$module_handler =& xoops_gethandler( 'module' ) ;
		$module =& $module_handler->getByDirname( $this->mydirname ) ;
		if( ! empty( $module ) ) $this->mid = intval( $module->getVar('mid') ) ;
		$this->page_name = $page_name ;
		$this->action_base_hiddens = $action_base_hiddens ;
	}


	function get_language_constant( $name )
	{
		return constant( strtoupper( '_MD_A_' . $this->dirname . '_' . $this->page_name . '_' . $name ) ) ;
	}


	function get_set4sql( $value , $col )
	{
	
		switch( $col['type'] ) {
			case 'text' :
			case 'blob' :
				if( get_magic_quotes_gpc() ) $value = stripslashes( $value ) ;
				$length = empty( $col['length'] ) ? 65535 : intval( $col['length'] ) ;
				return "`{$col['name']}`='".addslashes( xoops_substr( $value , 0 , $length ) )."'" ;
			case 'char' :
			case 'varchar' :
			case 'string' :
				if( get_magic_quotes_gpc() ) $value = stripslashes( $value ) ;
				$length = empty( $col['length'] ) ? 255 : intval( $col['length'] ) ;
				return "`{$col['name']}`='".addslashes( xoops_substr( $value , 0 , $length ) )."'" ;
			case 'int' :
			case 'integer' :
				$value = intval( $value ) ;
				if( ! empty( $col['max'] ) ) $value = min( $value , intval( $col['max'] ) ) ;
				if( ! empty( $col['min'] ) ) $value = max( $value , intval( $col['min'] ) ) ;
				return "`{$col['name']}`=$value" ;
		}
	}


	// single update or insert
	function insert()
	{
		$db =& Database::getInstance() ;

		$id = $this->init_default_values() ;

		$set4sql = '' ;
		foreach( $this->cols as $col ) {
			if( empty( $col['edit_edit'] ) ) continue ;
			if( $col['name'] == $this->primary_key ) continue ;
			$set4sql .= $this->get_set4sql( @$_POST[ $col['name'] ] , $col ) . ',' ;
		}
		if( ! empty( $set4sql ) ) {
			if( $id > 0 ) {
				// UPDATE
				$db->queryF( "UPDATE $this->table SET ".substr( $set4sql , 0 , -1 )." WHERE $this->primary_key='".addslashes($id)."'" ) ;
				return array( $id , 'update' ) ;
			} else {
				// INSERT
				$db->queryF( "INSERT INTO $this->table SET ".substr( $set4sql , 0 , -1 ) ) ;
				return array( $db->getInsertId() , 'insert' ) ;
			}
		}

	}


	// multiple update
	function update()
	{
		$db =& Database::getInstance() ;

		// search appropriate column for getting primary_key
		foreach( $this->cols as $col ) {
			if( in_array( @$col['list_edit'] , array( 'text' , 'textarea' , 'hidden' ) ) ) {
				$column4key = $col['name'] ;
				break ;
			}
		}
		if( empty( $column4key ) ) $column4key = $this->cols[0]['name'] ;

		$ret = array() ;
		foreach( array_keys( $_POST[$column4key] ) as $id ) {
			$id = intval( $id ) ;	// primary_key should be 'integer'
			$set4sql = '' ;
			foreach( $this->cols as $col ) {
				if( empty( $col['list_edit'] ) ) continue ;
				if( $col['name'] == $this->primary_key ) continue ;
				$set4sql .= $this->get_set4sql( @$_POST[ $col['name'] ][$id] , $col ) . ',' ;
			}
			if( ! empty( $set4sql ) ) {
				$result = $db->query( "SELECT * FROM $this->table WHERE $this->primary_key=$id" ) ;
				if( $db->getRowsNum( $result ) == 1 ) {
					$db->queryF( "UPDATE $this->table SET ".substr( $set4sql , 0 , -1 )." WHERE $this->primary_key=$id" ) ;
					if( $db->getAffectedRows() == 1 ) {
						$ret[ $id ] = $db->fetchArray( $result ) ;
					}
				}
			}
		}

		return $ret ;
	}


	function delete( $delete_comments = false , $delete_notifications = false )
	{
		$db =& Database::getInstance() ;

		$ret = array() ;
		foreach( array_keys( $_POST['admin_main_checkboxes'] ) as $id ) {
			$id = intval( $id ) ;	// primary_key should be 'integer'
			$result = $db->query( "SELECT * FROM $this->table WHERE $this->primary_key=$id" ) ;
			if( $db->getRowsNum( $result ) == 1 ) {
				$ret[ $id ] = $db->fetchArray( $result ) ;

				$db->queryF( "DELETE FROM $this->table WHERE $this->primary_key=$id" ) ;
				if( $delete_comments ) {
					// remove comments
					$db->queryF( "DELETE FROM ".$db->prefix("xoopscomments")." WHERE com_modid=$this->mid AND com_itemid=$id" ) ;
				}
	
				if( $delete_notifications ) {
					// remove notifications
					$db->queryF( "DELETE FROM ".$db->prefix("xoopsnotifications")." WHERE not_modid=$this->mid AND not_itemid=$id" ) ;
				}
			}
		}

		return $ret ;
	}


	function init_default_values()
	{
		$db =& Database::getInstance() ;

		if( @$_GET['id'] ) {
			$id = intval( $_GET['id'] ) ;
			$rs = $db->query( "SELECT * FROM $this->table WHERE $this->primary_key=$id" ) ;
			if( $db->getRowsNum( $rs ) == 1 ) {
				$row = $db->fetchArray( $rs ) ;
				foreach( array_keys( $this->cols ) as $key ) {
					if( empty( $this->cols[$key]['edit_show'] ) ) continue ;
					$this->cols[$key]['default_value'] = $row[ $this->cols[$key]['name'] ] ;
				}
				$this->form_mode = 'edit' ;
				return $id ;
			}
		}

		$this->form_mode = 'new' ;
		return 0 ;
	}


	function get_view_edit()
	{
		$id = $this->init_default_values() ;

		$lines = array() ;
		foreach( $this->cols as $col ) {
			if( empty( $col['edit_show'] ) ) continue ;
			if( ! isset( $col['default_value'] ) ) {
				switch( $col['type'] ) {
					case 'int' :
					case 'integer' :
						$col['default_value'] = 0 ;
						break ;
					default :
						$col['default_value'] = '' ;
						break ;
				}
			}
			switch( $col['edit_edit'] ) {
				case 'checkbox' :
					$checked = empty( $col['default_value'] ) ? '' : "checked='checked'" ;
					$value = empty( $col['checkbox_value'] ) ? 1 : htmlspecialchars( $col['checkbox_value'] , ENT_QUOTES ) ;

					$lines[ $col['name'] ] = "<input type='checkbox' name='{$col['name']}' value='$value' $checked />" ;
					break ;
				case 'text' :
				default :
					$size = empty( $col['edit_size'] ) ? 32 : intval( $col['edit_size'] ) ;
					$length = empty( $col['length'] ) ? 255 : intval( $col['length'] ) ;
					$lines[ $col['name'] ] = "<input type='text' name='{$col['name']}' size='$size' maxlength='$length' value='".htmlspecialchars( $col['default_value'] , ENT_QUOTES )."' />" ;
					break ;
				case false :
					$lines[ $col['name'] ] = htmlspecialchars( $col['default_value'] , ENT_QUOTES ) ;
					break ;
			}
		}

		return array( $id , $lines ) ;
	}


	function get_control_form( $controllers )
	{
		$hiddens = '' ;
		foreach( $this->action_base_hiddens as $key => $val ) {
			$key4disp = htmlspecialchars( $key , ENT_QUOTES ) ;
			$val4disp = htmlspecialchars( $val , ENT_QUOTES ) ;
			$hiddens .= "<input type='hidden' name='$key4disp' value='$val4disp' />\n" ;
		}

		$controllers_html = '' ;
		foreach( $controllers as $type => $body ) {
			if( $type == 'num' ) {
				$controllers_html .= $this->get_select( 'num' , $body , $GLOBALS['num'] ) ;
			}
		}

		return "
			<form action='' method='get' name='admin_control' id='admin_control'>
				$hiddens
				$controllers_html
				<input type='submit' value='"._SUBMIT."' />
			</form>\n" ;
	}

	function get_select( $name , $options , $current_value ) {
		$ret = "<select name='".htmlspecialchars($name,ENT_QUOTES)."'>\n" ;
		foreach( $options as $key => $val ) {
			$selected = $val == $current_value ? "selected='selected'" : "" ;
			$ret .= "<option value='".htmlspecialchars($key,ENT_QUOTES)."' $selected>".htmlspecialchars($val,ENT_QUOTES)."</option>\n" ;
		}
		$ret .= "</select>\n" ;

		return $ret ;
	}

}


?>