<?php
/**
 * Created by JetBrains PhpStorm.
 * User: bluemooninc
 * Date: 2012/11/07
 * Time: 13:20
 * To change this template use File | Settings | File Templates.
 */
class importCsv
{
	protected $userObjects = array();
	protected $userKey;
	protected $groupKey;

	function importCsv($userKey, $groupKey)
	{
		$this->userKey = $userKey;
		$this->groupKey = $groupKey;
	}

	/**
	 *  CSVエスケープ処理を行う
	 *
	 * @access public
	 * @param  string  $csv        エスケープ対象の文字列(CSVの各要素)
	 * @param  bool    $escape_nl  改行文字(\r/\n)のエスケープフラグ
	 * @return string  CSVエスケープされた文字列
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

	/**
	 * 2012-5-20 : Refactoring by Y.Sakai
	 * @param $fp
	 * @param $csv_encoding
	 * @return string
	 */
	public function loadCSV(&$fp, $csv_encoding)
	{
		$csvLine = "";
		while (!feof($fp)) {
			$_line = fgets($fp);
			if ($csv_encoding) {
				mb_convert_variables(_CHARSET, $csv_encoding, $_line);
			}
			$csvLine .= $_line;
			$cnt = substr_count($csvLine, '"');
			if ($cnt % 2 == 0) break;
		}
		return $csvLine;
	}

	/**
	 *  CSV形式の文字列を配列に分割する
	 *
	 * @access public
	 * @param  string  $csv        CSV形式の文字列(1行分)
	 * @param  string  $delimiter  フィールドの区切り文字
	 * @return mixed   (array):分割結果 Ethna_Error:エラー(行継続)
	 */
	static function _explodeCsv($csv)
	{
		$delimiter = ",";
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
		$csv = substr($csv, 0, strlen($csv) - strlen($line_end));
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
			if ($csv[$index] == '"') {
				// 2A. handle quote delimited field
				$index++;
				while ($index < $csv_len) {
					$checkChar = isset($csv[$index]) ? $csv[$index] : null;
					if ($checkChar == '"') {
						// handle double quote
						if ($csv[$index + 1] == '"') {
							$field .= $csv[$index];
							$index += 2;
						} else {
							// must be end of string
							while ($checkChar != $delimiter && $index < $csv_len) {
								$index++;
								$checkChar = isset($csv[$index]) ? $csv[$index] : null;
							}
							if ($checkChar == $delimiter) {
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
							$field = substr($field, 0, strlen($field) - 1);
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
				if ($csv[$index] == $delimiter) {
					$index++;
				}
			}
			$retval[] = $field;
			$field = '';
		} while ($index < $csv_len);
		return $retval;

	}

	/**
	 * @param $_line
	 * @return array|null
	 */
	public function loadOneLineToArray($_line, &$import_key, &$userHandler, &$groupHandler, &$dataHandler)
	{
		if (!$_line) return null;

		$_data = $this->_explodeCsv($_line);

		$import_data = array(
			'error' => false,
			'update' => 0,
			'is_new' => true,
			'value' => array(),
			'userUpdate'=>false,
			'groupUpdate'=>false,
			'profUpdate'=>false
		);
		if (count($_data) != count($import_key)) {
			$import_data['error'] = true;
		}
		// Check exist user by uname
		if ($_data) {
			$uname = isset($_data[2]) ? $_data[2] : "";
			$userObjects = $userHandler->getObjects(new Criteria('uname', $uname), false);
			if ($userObjects) {
				foreach ($userObjects as $obj) {
					$uid = $obj->get('uid');
				}
				$_data[0] = $uid;
			}
		}
		$import_data['is_new'] = true;
		// For User,Group,Profile update
		if ($_data[0]) {
			$this->userObjects = $userHandler->get($_data[0]);
			if ($this->userObjects) {
				$uid = $this->userObjects->get('uid');
				if ($uid > 0) {
					$groupObjects = $groupHandler->getObjects(new Criteria('uid', $uid), false);
					$profObjects = $dataHandler->getObjects(new Criteria('uid', $uid));
					$userCreate = $groupCreate = $profCreate = false;
					for ($i = 0; $i < count($import_key); $i++) {
						$csv_value = isset($_data[$i]) ? $_data[$i] : null;
						$key = $import_key[$i];
						if (in_array($key, $this->userKey)) {
							$userValue = $this->userObjects->get($key);
							if (strpos($key,'user_regdate') !== false) {
								$csv_value = strtotime($csv_value);
							}
							$userUpdate = ($userValue <> $csv_value) ? true : false;
							$import_data['userUpdate'] = ($import_data['userUpdate'] | $userUpdate) ? true : false;
							$import_data['value'][] = array('field' => $key, 'var' => $csv_value, 'update' => $userUpdate);
						} elseif (in_array($key, $this->groupKey)) {
							$currentLinkArr = array();
							foreach ($groupObjects as $obj) {
								$currentLinkArr[] = $obj->get("groupid");
							}
							$newLinkArr = explode("|", $csv_value);
							$newLinkArr = array_filter($newLinkArr);
							$groupUpdate = ($currentLinkArr != $newLinkArr) ? true : false;
							$import_data['groupUpdate'] = $import_data['groupUpdate'] | $groupUpdate;
							$import_data['value'][] = array('field' => $key, 'var' => $csv_value, 'update' => $groupUpdate);
						} else {
							$profValue = NULL;
							if ($profObjects) {
								foreach ($profObjects as $obj) {
									$profValue = $obj->get($key);
								}
								$profUpdate = ($profValue != $csv_value) ? true : false;
								$import_data['profUpdate'] = $import_data['profUpdate'] | $profUpdate;
							} else {
								$profCreate = true;
								$import_data['profUpdate'] = false;
							}
							$profUpdate = isset($profUpdate) ? $profUpdate : false;
							$import_data['value'][] = array('field' => $key, 'var' => $csv_value, 'update' => $profUpdate);
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
		if ($import_data['is_new'] == true) {
			$import_data['userCreate'] = $import_data['groupCreate'] = $import_data['profCreate'] = true;
			for ($i = 0; $i < count($import_key); $i++) {
				$key = $import_key[$i];
				$var = isset($_data[$i]) && $_data[$i] !== '' ? $_data[$i] : '';
				$import_data['value'][] = array('field' => $key, 'var' => $var, 'update' => 0);
			}
		}
		return $import_data;
	}

	public function &userObjects()
	{
		return $this->userObjects;
	}
}