<?php
/**
 * @package user
 * @version $Id: UserDataUploadAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) exit();

require_once dirname(__FILE__)."/UserDataUploadAction.class.php";

class User_UserDataUploadConfAction extends User_UserDataUploadAction
{
	/// アップされたCSVファイルを出力する
	function execute(&$controller, &$xoopsUser)
	{
	
		/// csv file check
		if (isset($_FILES['user_csv_file']) &&
			$_FILES['user_csv_file']['error'] == 0){
			return USER_FRAME_VIEW_SUCCESS;
		}
		return $this->getDefaultView($controller, $xoopsUser);
	}
	
	
	/// 確認画面を表示
	function executeViewSuccess(&$controller, &$xoopsUser, &$render)
	{
		/// success
		$render->setTemplateName("user_data_upload_conf.html");

		// fields
		$fields = array();
		$user_handler =& $this->_getHandler();
		$user_tmp = $user_handler->create();
		$user_key = array_keys($user_tmp->gets());
		foreach ($user_key as $key){
			$_f = '_MD_USER_LANG_'.strtoupper($key);
			$fields[] = defined($_f) ? constant($_f) : $key ;
		}
		$render->setAttribute('user_fields', $fields);

		/// csv data
		$csv_data = array();
		$csv_file = $_FILES['user_csv_file']['tmp_name'];
		$csv_encoding = '';
		$user_h =& $this->_getHandler();
		if (function_exists('mb_detect_encoding')){
			$_csv_contents = implode('', file($csv_file));
			$csv_encoding = mb_detect_encoding($_csv_contents);
		}

		foreach(file($csv_file) as $n=>$_data_line){
			if ($csv_encoding){
				mb_convert_variables(_CHARSET, $csv_encoding, $_data_line);
			}
			$_data = $this->explodeCSV($_data_line);
			if (!$n || !implode('', $_data)){
				continue;
			}
			$user_data = array(
				'error'  => false,
				'update' => 0,
				'is_new' => true,
				'value'  => array(),
				);
			if (count($_data) != count($user_key)){
				$user_data['error'] = true;
			}
			if ($_data[0]){
				$user =& $user_h->get($_data[0]);
				if ($user){
					for ($i=0; $i<count($user_key); $i++){
						$csv_value = $_data[$i];
						$user_value = $user->get($user_key[$i]);
						$update = $user_value != $csv_value;
						 switch ($user_key[$i]){
						  case 'user_regdate':
						  case 'last_login':
							$update = ($user_value || $csv_value) && strcmp(formatTimestamp($user_value, 'Y/n/j H:i'),  $csv_value)!==0;
							 if ($update){
							 }
							break;
						  case 'pass':
							if (strlen($csv_value)!=32){
								$update = $user_value != md5($csv_value);
								$csv_value = md5($csv_value);
							}
						  default:
						}
						$user_data['update'] = $user_data['update'] | $update;
						$user_data['value'][] = array(
							'var'    => $csv_value,
							'update' => $update,
							);
					}
					$user_data['is_new'] = false;
				}
			}
			if ($user_data['is_new'] == true){
				for ($i=0; $i<count($user_key); $i++){
					$var = isset($_data[$i]) && $_data[$i]!=='' ? $_data[$i] : $user_tmp->get($user_key[$i]);
					switch ($user_key[$i]){
					  case 'user_regdate':
					  case 'last_login':
						$var = formatTimestamp($var, 'Y/n/j H:i');
						break;
					}					
					$user_data['value'][] = array(
						'var'    => $var,
						'update' => 0);
				}
			}
			$csv_data[] = $user_data;
		}
		
		$render->setAttribute('csv_data', $csv_data);
		$_SESSION['user_csv_upload_data'] = $csv_data;
	}
	
	
	
    // {{{ explodeCSV(Ethna_Util.php)
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
//                          return Ethna::raiseNotice('CSV分割エラー(行継続)', E_UTIL_CSV_CONTINUE);
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
