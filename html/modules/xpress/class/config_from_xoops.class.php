<?php
/*
 * XPressME - WordPress for XOOPS
 *
 * @copyright	XPressME Project http://www.toemon.com
 * @license		http://www.fsf.org/copyleft/gpl.html GNU public license
 * @author		toemon
 * @package		module::xpress
 */

/* 
 * The function to acquire only a set value without calling the XOOPS system is here.
 */
class ConfigFromXoops{
	var $xoops_mainfile_path;
	var $define_arry = array();	
	var $external_define_path;
	var $xp_config_file_path;
	var $xoops_root_path;
	var $xoops_url;
	var $xoops_trust_path;
	var $xoops_cache_path;
	var $xoops_db_prefix;
	var $xoops_db_name;
	var $xoops_db_user;
	var $xoops_db_pass;
	var $xoops_db_host;
	var $module_name;
	var $module_path;
	var $module_url;
	var $module_db_prefix;
	var $module_version;
	var $module_codename;	
	var $xoops_upload_path;
	var $xoops_upload_url;
	var $xoops_db_salt;
	var $xoops_salt;
	var $is_impress;
	var $impress_db_config_file;
	var $wp_db_version;
	var $wp_version;
	var $is_wp_me;
	var $xoops_language;
	var $module_id;
	var $module_config= array();
	var $xoops_time_zone;
	var $xoops_var_path;
	var $xoops_db_charset;
	var $xoops_db_pconnect;
	var $xoops_path;
	
	function __constructor()	//for PHP5
    {
        $this->ConfigFromXoops();
       
    }
    
    function xpress_eval($str){
    	$eval_str = '$ret = ' . $str . ' ;';
    	eval($eval_str);
    	return $ret;
    }

    function ConfigFromXoops()	//for PHP4 constructor
    {  
    	$this->xoops_mainfile_path = $this->get_xoops_mainfile_path();
    	$this->module_path=dirname(dirname(__FILE__));
    	$this->module_name=basename($this->module_path);
    	$this->xp_config_file_path = $this->module_path . '/xp-config.php';
    	
    	// start /admin/index.php page detect
    	$php_script_name = $_SERVER['SCRIPT_NAME'];
		$php_query_string = $_SERVER['QUERY_STRING'];
		$admin_page = 	basename(dirname(dirname(__FILE__))) . '/admin/index.php';
		$is_xoops_module_admin = false;
		if (strstr($php_script_name,$admin_page) !== false) $is_xoops_module_admin = true;
		if (strstr($php_query_string,$admin_page) !== false) $is_xoops_module_admin = true;
    	// end of /admin/index.php page detect
    	
    	if (file_exists($this->xp_config_file_path)){	// file exists xp-config.php
    		$this->_get_value_by_xp_config_file();
    	}else if (defined('XOOPS_MAINFILE_INCLUDED') && !$is_xoops_module_admin){ // loaded XOOPS mainfile.php
    		$this->_get_value_by_xoops_define();
    	} else {  // A set value is acquired from mainfile.php by the pattern match.
			if(file_exists($this->xoops_mainfile_path)){
    			$this->_get_value_by_xoops_mainfile();
    			
    			// Xoops2.5 secure.php 
				if (!empty($this->xoops_var_path)){
					$secure_path = $this->xoops_var_path . "/data/secure.php";
    				if (file_exists($secure_path)){
    					$this->_get_value_by_xoops_secure($secure_path);
    				}
				}

    			// Value 'is_impress' and value 'external_define_path' used in the under
    			// are set in _get_value_by_xoops_mainfile(). 
				if ($this->is_impress){		// DB Config from Impress CMS impress_db_config file
					$this->_get_value_by_impress_db_config_file();
				} else if(!empty($this->external_define_path)){ // file exists mainfile.php in the trust pass.
					$this->_get_value_by_trust_mainfile();
				}
			} // end of if file_exists
			
		}

		//  define from /settings/definition.inc.php (XCL)  or /include/common.php(2016a-JP)
		$this->xoops_upload_path = $this->xoops_root_path .'/uploads';
		$this->xoops_upload_url = $this->xoops_url . '/uploads';
		$this->module_db_prefix = $this->xoops_db_prefix  . '_' . preg_replace('/wordpress/','wp',$this->module_name) . '_';
		
		$this->set_module_version();
		$this->set_wp_version();
		if (function_exists('date_default_timezone_get')){
			$this->xoops_time_zone = date_default_timezone_get();
		}
		$this->_get_cache_path();
    }

	// A set value is acquired from XOOPS mainfile.php by the pattern match.
	function _get_value_by_xoops_mainfile(){
		$array_file = file($this->xoops_mainfile_path);
		$pattern = '^\s*define\s*\(\s*(\'[^\']+\'|"[^"]+")\s*,\s*([^\s]+.*)\s*\)\s*;';
		$impress_include_pattern = '^\s*(include_once|include)\s*[\(\s]\s*XOOPS_TRUST_PATH\s*.\s*[\'"]([^\'"]+)[\'"]\s*[\)\s]\s*;';
		$external_define_file_pattern = '^\s*(include_once|include|require_once|require_once)\s*\((.*mainfile\.php.*)\)';
		for ($i = 0 ; $i <count($array_file) ; $i++){
			if (preg_match('/' . $pattern . '/' ,$array_file[$i],$matchs)){
				$keys = $matchs[1];
				if (preg_match('/^\'[^\']*\'$/',$keys)) $keys = preg_replace('/\'/', '', $keys);
				if (preg_match('/^"[^"]*"$/',$keys)) $keys = preg_replace('/"/', '', $keys);
				$key_value = $matchs[2];

				switch ($keys){
					case  'XOOPS_ROOT_PATH':
						$this->xoops_root_path = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_URL':
						$this->xoops_url = $this->xpress_eval($key_value);
						$this->module_url = $this->xoops_url . '/modules/' . $this->module_name;
						break;
					case  'XOOPS_PATH':
						$this->xoops_path = $this->xpress_eval($key_value);						
					case  'XOOPS_TRUST_PATH':
						$this->xoops_trust_path = $this->xpress_eval($key_value);
						if ($this->xoops_trust_path ==  'XOOPS_PATH')
							$this->xoops_trust_path = $this->xoops_path;
						break;
					case  'XOOPS_DB_PREFIX':
						$this->xoops_db_prefix = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_DB_NAME':
						$this->xoops_db_name = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_DB_USER':
						$this->xoops_db_user = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_DB_PASS':
						$this->xoops_db_pass = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_DB_HOST':
						$this->xoops_db_host = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_DB_SALT':
						$this->xoops_db_salt = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_SALT':
						$this->xoops_salt = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_VAR_PATH':
						$this->xoops_var_path = $this->xpress_eval($key_value);
						break;
					default :
						
				}	// end of switch
			}	 // end of if preg_match
			
			// Check External Define File
			if (preg_match('/' . $external_define_file_pattern . '/' ,$array_file[$i],$trust_main_matchs)){
				$include_path = $this->xpress_eval($trust_main_matchs[2]);
				if (file_exists($include_path))
					$this->external_define_path = $include_path;
			}
			
			// Check ImpressCMS
			if (preg_match('/' . $impress_include_pattern . '/' ,$array_file[$i],$impres_matchs)){
				$this->is_impress = true;
				$this->impress_db_config_file = $this->xoops_trust_path . $impres_matchs[2];
			}
		} // end of for loop		
	}
	// A set value is acquired from XOOPS mainfile.php by the pattern match.
	function _get_value_by_xoops_secure($secure_path){
		$array_file = file($secure_path);
		$pattern = '^\s*define\s*\(\s*(\'[^\']+\'|"[^"]+")\s*,\s*([^\s]+.*)\s*\)\s*;';
		$impress_include_pattern = '^\s*(include_once|include)\s*\(\s*XOOPS_TRUST_PATH\s*.\s*[\'"]([^\'"]+)[\'"]\s*\)';
		$external_define_file_pattern = '^\s*(include_once|include|require_once|require_once)\s*\((.*mainfile\.php.*)\)';
		for ($i = 0 ; $i <count($array_file) ; $i++){
			if (preg_match('/' . $pattern . '/' ,$array_file[$i],$matchs)){
				$keys = $matchs[1];
				if (preg_match('/^\'[^\']*\'$/',$keys)) $keys = preg_replace('/\'/', '', $keys);
				if (preg_match('/^"[^"]*"$/',$keys)) $keys = preg_replace('/"/', '', $keys);
				$key_value = $matchs[2];

				switch ($keys){
					case  'XOOPS_DB_PREFIX':
						$this->xoops_db_prefix = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_DB_NAME':
						$this->xoops_db_name = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_DB_USER':
						$this->xoops_db_user = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_DB_PASS':
						$this->xoops_db_pass = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_DB_HOST':
						$this->xoops_db_host = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_DB_SALT':
						$this->xoops_db_salt = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_SALT':
						$this->xoops_salt = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_DB_CHARSET':
						$this->xoops_db_charset = $this->xpress_eval($key_value);
						break;
					case  'XOOPS_DB_PCONNECT':
						$this->xoops_db_pconnect = $this->xpress_eval($key_value);
						break;
					default :
						
				}	// end of switch
			}	 // end of if preg_match
			
		} // end of for loop		
	}
	// A set value is acquired from XOOPS define value .
	function _get_value_by_xoops_define(){
		$this->xoops_root_path = XOOPS_ROOT_PATH;
		$this->xoops_url = XOOPS_URL;
		$this->module_url = $this->xoops_url . '/modules/' . $this->module_name;
		if(defined('XOOPS_TRUST_PATH')) $this->xoops_trust_path = XOOPS_TRUST_PATH; else $this->xoops_trust_path = '';
		$this->xoops_db_prefix = XOOPS_DB_PREFIX;
		$this->xoops_db_name = XOOPS_DB_NAME;
		$this->xoops_db_user = XOOPS_DB_USER;
		$this->xoops_db_pass = XOOPS_DB_PASS;
		$this->xoops_db_host = XOOPS_DB_HOST;
		if(defined('XOOPS_DB_SALT')) $this->xoops_db_salt = XOOPS_DB_SALT; else $this->xoops_db_salt = '';
		if(defined('XOOPS_SALT')) $this->xoops_salt = XOOPS_SALT; else $this->xoops_salt = '';
	}
	// A set value is acquired from xp-config.php .
	function _get_value_by_xp_config_file(){
	    require_once($this->xp_config_file_path);
		$this->xoops_root_path =XP_XOOPS_ROOT_PATH;
		$this->xoops_url = XP_XOOPS_URL;
		$this->module_url = $this->xoops_url . '/modules/' . $this->module_name;
		if(defined('XP_XOOPS_TRUST_PATH')) $this->xoops_trust_path = XP_XOOPS_TRUST_PATH; else $this->xoops_trust_path = '';
		$this->xoops_db_prefix = XP_XOOPS_DB_PREFIX;
		$this->xoops_db_name = XP_XOOPS_DB_NAME;
		$this->xoops_db_user = XP_XOOPS_DB_USER;
		$this->xoops_db_pass = XP_XOOPS_DB_PASS;
		$this->xoops_db_host = XP_XOOPS_DB_HOST;
		if(defined('XP_XOOPS_DB_SALT')) $this->xoops_db_salt = XP_XOOPS_DB_SALT; else $this->xoops_db_salt = '';
		if(defined('XOOPS_SALT')) $this->xoops_salt = XP_XOOPS_SALT; else $this->xoops_salt = '';
	}
	
	// A set value is acquired from config file in the trust pass by the pattern match.
    function _get_value_by_impress_db_config_file(){
		if(file_exists($this->impress_db_config_file)){
			$array_file = file($this->impress_db_config_file);
			$pattern = '^\s*define\s*\(\s*(\'[^\']+\'|"[^"]+")\s*,\s*(\'[^\']*\'|"[^"]*"|[^\'"])\s*\)\s*;';
			for ($i = 0 ; $i <count($array_file) ; $i++){
				if (preg_match('/' . $pattern . '/' ,$array_file[$i],$matchs)){
					$keys = $matchs[1];
					if (preg_match('/^\'[^\']*\'$/',$keys)) $keys = preg_replace('/\'/', '', $keys);
					if (preg_match('/^"[^"]*"$/',$keys)) $keys = preg_replace('/"/', '', $keys);
					$key_value = $matchs[2];

					switch ($keys){
						case  'SDATA_DB_SALT':
							$this->xoops_db_salt = $this->xpress_eval($key_value);
							break;
						case  'SDATA_DB_PREFIX':
							$this->xoops_db_prefix = $this->xpress_eval($key_value);
							break;
						case  'SDATA_DB_NAME':
							$this->xoops_db_name = $this->xpress_eval($key_value);
							break;
						case  'SDATA_DB_USER':
							$this->xoops_db_user = $this->xpress_eval($key_value);
							break;
						case  'SDATA_DB_PASS':
							$this->xoops_db_pass = $this->xpress_eval($key_value);
							break;
						case  'SDATA_DB_HOST':
							$this->xoops_db_host = $this->xpress_eval($key_value);
							break;
						default :
							
					}	// end of switch
				}
			} // end of for
		} // end of if file_exists
    }
    
    function _get_value_by_trust_mainfile(){
		// When the definition is written in mainfile.php in the trust passing
		if(file_exists($this->external_define_path)){
			require_once($this->external_define_path);
			
			$this->xoops_root_path = XOOPS_ROOT_PATH;
    		$this->xoops_url = XOOPS_URL;
    		$this->module_url = $this->xoops_url . '/modules/' . $this->module_name;
    		if(defined('XOOPS_TRUST_PATH')) $this->xoops_trust_path = XOOPS_TRUST_PATH; else $this->xoops_trust_path = '';
    		$this->xoops_db_prefix = XOOPS_DB_PREFIX;
    		$this->xoops_db_name = XOOPS_DB_NAME;
    		$this->xoops_db_user = XOOPS_DB_USER;
    		$this->xoops_db_pass = XOOPS_DB_PASS;
    		$this->xoops_db_host = XOOPS_DB_HOST;
			if(defined('XOOPS_DB_SALT')) $this->xoops_db_salt = XOOPS_DB_SALT; else $this->xoops_db_salt = '';
			if(defined('XOOPS_SALT')) $this->xoops_salt = XOOPS_SALT; else $this->xoops_salt = '';
		} // end of if file_exists
	}
	
	// call after the $this->xoops_trust_path is set
	function _get_cache_path(){
		$cache_path = $this->xoops_trust_path . '/cache';
		if (file_exists($cache_path) && is_writable($cache_path)){
			$this->xoops_cache_path = $cache_path;
			return;
		}
		$this->xoops_cache_path = $this->xoops_root_path . '/cache';
	}
    
    function get_xoops_mainfile_path(){
    	return dirname(dirname(dirname(dirname(__FILE__)))) . '/mainfile.php';
    }
    
    // set XPressME module virsion and codename from xoops_versions.php
    function set_module_version(){
    	$xoops_version_file = dirname(dirname(__FILE__)) . '/xoops_version.php';
		if(file_exists($xoops_version_file)){
			$version_file = file($xoops_version_file);
			$version_pattern = '^\s*(\$modversion\[\s*\'version\'\s*\])\s*=\s*[\'"]([^\'"]*)[\'"]';
			$codename_pattern = '^\s*(\$modversion\[\s*\'codename\'\s*\])\s*=\s*[\'"]([^\'"]*)[\'"]';
			$version_found = false;
			$codename_found = false;
			for ($i = 0 ; $i <count($version_file) ; $i++){
				if (preg_match( "/$version_pattern/", $version_file[$i] ,$v_matches )){
					$this->module_version = $v_matches[2];
					$version_found = true;
				}
				if (preg_match( "/$codename_pattern/", $version_file[$i] ,$c_matches )){
					$this->module_codename = $c_matches[2];
					$codename_found = true;
				}
				if ( $version_found && $codename_found ) break;
			}
		}
    }
    
    function set_wp_version(){
    	include dirname(dirname(__FILE__)) . '/wp-includes/version.php';
    	
    	$this->wp_db_version = $wp_db_version;
		
		$this->wp_version = str_replace("ME", "", $wp_version);
		
		$pattern = 'ME.*';
    	if (preg_match('/' . $pattern . '/' ,$wp_version)){
			$this->is_wp_me = true;
		} else {
			$this->is_wp_me = true;
		}
    }
    
}
?>