<?php
/**
 * @package user
 * @version $Id: UserDataUploadAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once dirname(__FILE__)."/DataUploadAction.class.php";

class Profile_Admin_DataUploadConfAction extends Profile_Admin_DataUploadAction
{
	var $user_key = array();
	var $group_key = array();
	var $userObjects = array();
	var $groupObjects = array();
	var $profObjects = array();
	
	function &_getHandler($tableName='data')
	{
		$handler = xoops_getmodulehandler($tableName);
		return $handler;
	}
	
	function execute()
	{
		/// csv file check
		if (isset($_FILES['profile_csv_file']) &&
			$_FILES['profile_csv_file']['error'] == 0){
			return PROFILE_FRAME_VIEW_SUCCESS;
		}
		return $this->getDefaultView();
	}
	// 2012-5-20 : Refactoring by Y.Sakai 
	function executeViewSuccess(&$controller,&$render)
	{
		// success
		$render->setTemplateName("data_upload_conf.html");

		// Get user table keys
		$userHandler = xoops_getmodulehandler('users', 'user');
		$user_tmp = $userHandler->create();
		$this->user_key = array_keys($user_tmp->gets());
		
		// Get group table keys
		$groupHandler = xoops_getmodulehandler('groups_users_link', 'user');
		$group_tmp = $groupHandler->create();
		$this->group_key = array("groupid");

		// Get profile_definitions fields
		$defHandler = $this->_getHandler('definitions');
		$defArr = $defHandler->getDefinitions(false);
		
		$criteria = new CriteriaElement();
		$criteria->setSort('uid');
		$handler = $this->_getHandler('data');
		$dataArr = $handler->getObjects($criteria);
		if (count($dataArr)==0){
			return PROFILE_FRAME_VIEW_INDEX;
		}
		// Set key to restore
		$import_key = $this->user_key;
		$import_key[] = "groupid"; 
		foreach ($defArr as $key => $val){
			if ($key!="uid") $import_key[] = $key;
		}
		$render->setAttribute('import_fields', $import_key);
		// csv data
		$csvData = array();
		$csvFName = $_FILES['profile_csv_file']['tmp_name'];
		if (function_exists('mb_detect_encoding')){
			$csv_encoding = "SJIS";
		}else{
			$csv_encoding = '';
		}
		$lineCount=0;
		$fp = fopen($csvFName, 'r');
		while(!feof($fp)){
			$_line = $this->loadeCSV($fp,$csv_encoding);
			if (!$_line) break;
			$_data = $this->explodeCSV($_line);
			$import_data = array('error' => false, 'update' => 0, 'is_new' => true, 'value'  => array() );
			if (count($_data) != count($import_key)){
				$import_data['error'] = true;
			}
			// Check exist user by uname
			if ($lineCount>0){ 
				$uname = $_data[2];
				$userObjects = $userHandler->getObjects(new Criteria('uname', $uname), false);
				if ($userObjects){
					foreach($userObjects as $obj){
						$uid = $obj->get('uid');
					}
					$_data[0] = $uid;
				}
			}
			$import_data['is_new'] = true;
			// For User,Group,Profile update 
			if ($_data[0]){
				$this->userObjects = $userHandler->get($_data[0]);
				if ($this->userObjects){
					$uid = $this->userObjects->get('uid');
					if ($uid>0){
						$groupObjects = $groupHandler->getObjects(new Criteria('uid', $uid), false);
						$profObjects = $handler->getObjects(new Criteria('uid', $uid));
						$userUpdate = $groupUpdate = $profUpdate = false;
						$userCreate = $groupCreate = $profCreate = false;
						for ($i=0; $i<count($import_key); $i++){
							$csv_value = $_data[$i];
							$key = $import_key[$i];
							if (in_array($key,$this->user_key)){
								$userValue = $this->userObjects->get($key);
								$userUpdate = $userValue != $csv_value;
								$import_data['userUpdate'] = $import_data['userUpdate'] | $userUpdate;
								$import_data['value'][] = array( 'field'=>$key, 'var' => $csv_value, 'update' => $userUpdate );
							} elseif (in_array($key,$this->group_key)){
								$currentLinkArr = array();
								foreach($groupObjects as $obj){
									$currentLinkArr[] = $obj->get("groupid"); 
								}
								$newLinkArr = explode("|",$csv_value);
								$newLinkArr = array_filter($newLinkArr);
								$groupUpdate = $currentLinkArr!=$newLinkArr;
								$import_data['groupUpdate'] = $import_data['groupUpdate'] | $groupUpdate;
								$import_data['value'][] = array( 'field'=>$key, 'var' => $csv_value, 'update' => $groupUpdate );
							} else{
								$profValue = NULL;
								if($profObjects){
									foreach($profObjects as $obj){
										$profValue = $obj->get($key); 
									}
									$profUpdate = $profValue != $csv_value;
									$import_data['profUpdate'] = $import_data['profUpdate'] | $profUpdate;
								} else {
									$profCreate = ture;
									$import_data['profUpdate'] = false;
								}
								$import_data['value'][] = array( 'field'=>$key, 'var' => $csv_value, 'update' => $profUpdate );
							}
						}
						$import_data['userCreate'] = $userCreate;
						$import_data['groupCreate'] = $groupCreate;
						$import_data['profCreate'] = $profCreate;
						$import_data['is_new'] = false;
					}
				}
			}
			// For User,Group,Profile insert as new member 
			if ($import_data['is_new'] == true){
				$import_data['userCreate'] = $import_data['groupCreate'] =$import_data['profCreate'] = true;
				for ($i=0; $i<count($import_key); $i++){
					$key = $import_key[$i];					
					$var = isset($_data[$i]) && $_data[$i]!=='' ? $_data[$i] : '';					
					$import_data['value'][] = array( 'field'=>$key, 'var' => $var, 'update' => 0 );
				}
			}
			if($lineCount>0) $csvData[] = $import_data;
			$lineCount++;
		}
		$render->setAttribute('csv_data', $csvData,&$this->userObjects);
		$_SESSION['import_csv_upload_data'] = $csvData;
	}
	private function checkUpdate($key,$csv_value){
		if (in_array($key,$this->user_key)){
			$user_value = $this->userObjects->get($key);
			$update = $user_value != $csv_value;
		} elseif (in_array($key,$this->group_key)){
			$currentLinkArr = array();
			foreach($this->groupObjects as $obj){
				$currentLinkArr[] = $obj->get("groupid"); 
			}
			$newLinkArr = explode("|",$csv_value);
			$newLinkArr = array_filter($newLinkArr);
			$update = $currentLinkArr!=$newLinkArr;
		} else{
			foreach($this->profObjects as $obj){
				$profValue = $obj->get($key);
			}
			$update = $profValue != $csv_value;
		}
		return $update;
	}
    function loadeCSV(&$fp,$csv_encoding)
    {
    	$csvLine="";
    	while(!feof($fp)){
    		$_line = fgets($fp);
			$cl = 0;
			if ($csv_encoding){
				mb_convert_variables(_CHARSET, $csv_encoding, $_line);
			}			
			$csvLine .= $_line;
			$cnt = substr_count($csvLine, '"');
			$cl++;
			if ($cnt%2==0) break;
		}
        return $csvLine;
    }
	
    // {{{ explodeCSV(Ethna_Util.php)
    // {{{ explodeCSV
    /**
     *  CSV形式の文字列を配列に分割する
     *
     *  @access public
     *  @param  string  $csv        CSV形式の文字列(1行分)
     *  @param  string  $delimiter  フィールドの区切り文字
     *  @return mixed   (array):分割結果 Ethna_Error:エラー(行継続)
     */
    function explodeCSV($csv, $delimiter = ",")
    {
        $space_list = '';
        foreach (array(" ", "\t", "\r", "\n") as $c) {
            if ($c != $delimiter) {
                $space_list .= $c;
            }
        }
 
        $line_end = "";
        if (preg_match("/([$space_list]+)\$/sS", $csv, $match)) {
            $line_end = $match[1];
        }
        $csv = substr($csv, 0, strlen($csv)-strlen($line_end));
        $csv .= ' ';
 
        $field = '';
        $retval = array();
 
        $index = 0;
        $csv_len = strlen($csv);
        do {
            // 1. skip leading spaces
            if (preg_match("/^([$space_list]+)/sS", substr($csv, $index), $match)) {
                $index += strlen($match[1]);
            }
            if ($index >= $csv_len) {
                break;
            }
 
            // 2. read field
            if ($csv{$index} == '"') {
                // 2A. handle quote delimited field
                $index++;
                while ($index < $csv_len) {
                    if ($csv{$index} == '"') {
                        // handle double quote
                        if ($csv{$index+1} == '"') {
                            $field .= $csv{$index};
                            $index += 2;
                        } else {
                            // must be end of string
                            while ($csv{$index} != $delimiter && $index < $csv_len) {
                                $index++;
                            }
                            if ($csv{$index} == $delimiter) {
                                $index++;
                            }
                            break;
                        }
                    } else {
                        // normal character
                        if (preg_match("/^([^\"]*)/S", substr($csv, $index), $match)) {
                            $field .= $match[1];
                            $index += strlen($match[1]);
                        }
 
                        if ($index == $csv_len) {
                            $field = substr($field, 0, strlen($field)-1);
                            $field .= $line_end;
 
                            // request one more line
                            //return Ethna::raiseNotice('CSV Split Error (line continue)', E_UTIL_CSV_CONTINUE);
                        }
                    }
                }
            } else {
                // 2B. handle non-quoted field
                if (preg_match("/^([^$delimiter]*)/S", substr($csv, $index), $match)) {
                    $field .= $match[1];
                    $index += strlen($match[1]);
                }
 
                // remove trailing spaces
                $field = preg_replace("/[$space_list]+\$/S", '', $field);
                if ($csv{$index} == $delimiter) {
                    $index++;
                }
            }
            $retval[] = $field;
            $field = '';
        } while ($index < $csv_len);
 
        return $retval;
    }
    // }}}
    // {{{ escapeCSV
    /**
     *  CSVエスケープ処理を行う
     *
     *  @access public
     *  @param  string  $csv        エスケープ対象の文字列(CSVの各要素)
     *  @param  bool    $escape_nl  改行文字(\r/\n)のエスケープフラグ
     *  @return string  CSVエスケープされた文字列
     */
    function escapeCSV($csv, $escape_nl = false)
    {
        if (preg_match('/[,"\r\n]/', $csv)) {
            if ($escape_nl) {
                $csv = preg_replace('/\r/', "\\r", $csv);
                $csv = preg_replace('/\n/', "\\n", $csv);
            }
            $csv = preg_replace('/"/', "\"\"", $csv);
            $csv = "\"$csv\"";
        }
 
        return $csv;
    }
    // }}}
}

?>