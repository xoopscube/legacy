<?php
/**
 * @package user
 * @version $Id: UserDataUploadAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once dirname(__FILE__)."/DataUploadAction.class.php";

class Profile_Admin_DataUploadConfAction extends Profile_Admin_DataUploadAction
{
	function &_getHandler()
	{
		$handler =& xoops_getmodulehandler('data');
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
	
	
	function executeViewSuccess(&$controller,&$render)
	{
		/// success
		$render->setTemplateName("data_upload_conf.html");

		// fields
		$fields = array();
		$handler =& $this->_getHandler();
		$defHandler =& xoops_getmodulehandler('definitions');
		$defArr =& $defHandler->getDefinitions(false);
		
		$criteria = new CriteriaElement();
		$criteria->setSort('uid');
		$dataArr = $handler->getObjects($criteria);
		if (count($dataArr)==0){
			return PROFILE_FRAME_VIEW_INDEX;
		}
		$profile_key[] = "uid";
		foreach ($defArr as $key => $val){
			$profile_key[] = $key;
		}
		$render->setAttribute('profile_fields', $profile_key);
		/// csv data
		$csv_data = array();
		$csv_file = $_FILES['profile_csv_file']['tmp_name'];
		$csv_encoding = '';
		$profile_h =& $this->_getHandler();
		if (function_exists('mb_detect_encoding')){
			$_csv_contents = implode('', file($csv_file));
			$csv_encoding = "SJIS";//mb_detect_encoding($_csv_contents);
		}
		foreach(file($csv_file) as $n=>$_data_line){
			if ($csv_encoding){
				mb_convert_variables(_CHARSET, $csv_encoding, $_data_line);
			}
			$_data = $this->explodeCSV($_data_line);
			if (!$n || !implode('', $_data)){
				continue;
			}
			$prof_data = array(
				'error'  => false,
				'update' => 0,
				'is_new' => true,
				'value'  => array(),
				);
			if (count($_data) != count($profile_key)){
				$prof_data['error'] = true;
			}
			if ($_data[0]){
				$user =& $profile_h->get($_data[0]);
				if ($user){
					for ($i=0; $i<count($profile_key); $i++){
						$csv_value = $_data[$i];
						$key = $profile_key[$i];
						$profile_value = $user->get($key);
						$update = $profile_value != $csv_value;
						$prof_data['update'] = $prof_data['update'] | $update;
						$prof_data['value'][] = array(
							'var'    => $csv_value,
							'update' => $update,
							);
					}
					$prof_data['is_new'] = false;
				}
			}
			if ($prof_data['is_new'] == true){
				for ($i=0; $i<count($profile_key); $i++){
					$var = isset($_data[$i]) && $_data[$i]!=='' ? $_data[$i] : $profile_tmp->get($profile_key[$i]);					
					$prof_data['value'][] = array(
						'var'    => $var,
						'update' => 0);
				}
			}
			$csv_data[] = $prof_data;
		}
		$render->setAttribute('csv_data', $csv_data);
		$_SESSION['profile_csv_upload_data'] = $csv_data;
	}
	
	
	
    // {{{ explodeCSV(Ethna_Util.php)
    /**
     *  CSV
     *
     *  @access public
     *  @param  string  $csv        CSV
     *  @param  string  $delimiter  
     *  @return mixed   (array): Ethna_Error:
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
//                          return Ethna::raiseNotice('CSV�����G���[(�s�p��)', E_UTIL_CSV_CONTINUE);
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
                if (isset($csv{$index}) && $csv{$index} == $delimiter) {
                    $index++;
                }
            }
            $retval[] = $field;
            $field = '';
        } while ($index < $csv_len);

        return $retval;
    }
    // }}}
}

?>
