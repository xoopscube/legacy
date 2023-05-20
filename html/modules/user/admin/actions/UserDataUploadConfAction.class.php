<?php
/**
 * @package user
 * @author  Kazuhisa Minato aka minahito, Core developer
 * @version $Id: UserDataUploadAction.class.php,v 1.1 2007/05/15 02:34:42 minahito Exp $
 */

if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}

require_once __DIR__ . '/UserDataUploadAction.class.php';

class User_UserDataUploadConfAction extends User_UserDataUploadAction
{
    /// アップされたCSVファイルを出力する
    public function execute(&$controller, &$xoopsUser)
    {

        /// csv file check
        if (isset($_FILES['user_csv_file']) &&
            0 == $_FILES['user_csv_file']['error']) {
            return USER_FRAME_VIEW_SUCCESS;
        }
        return $this->getDefaultView($controller, $xoopsUser);
    }


    /// 確認画面を表示
    public function executeViewSuccess(&$controller, &$xoopsUser, &$render)
    {
        /// success
        $render->setTemplateName('user_data_upload_conf.html');

        // fields
        $fields = [];
        $user_handler =& $this->_getHandler();
        $user_tmp = $user_handler->create();
        $user_key = array_keys($user_tmp->gets());
        foreach ($user_key as $key) {
            $_f = '_MD_USER_LANG_'.strtoupper($key);
            $fields[] = defined($_f) ? constant($_f) : $key ;
        }
        $render->setAttribute('user_fields', $fields);

        /// csv data
        $csv_data = [];
        $csv_file = $_FILES['user_csv_file']['tmp_name'];
        $csv_encoding = '';
        $user_h =& $this->_getHandler();
        if (function_exists('mb_detect_encoding')) {
            $_csv_contents = implode('', file($csv_file));
            $csv_encoding = mb_detect_encoding($_csv_contents);
        }

        if (false !== ($handle = fopen($csv_file, 'r'))) {
            $current_locale = false;
            if ('UTF-8' === $csv_encoding) {
                $current_locale = setlocale(LC_ALL, '0');
                setlocale(LC_ALL, 'ja_JP.UTF-8');
                $bom = fread($handle, 3); // remove BOM
                if (0xef !== ord($bom[0]) || 0xbb !== ord($bom[1]) || 0xbf !== ord($bom[2])) {
                    rewind($handle); // BOM not found then do rewind
                }
            }
            $n = 0;
            while (false !== ($_data = fgetcsv($handle))) {
                if ($csv_encoding) {
                    mb_convert_variables(_CHARSET, $csv_encoding, $_data);
                }
                if (!$n++ || !implode('', $_data)) {
                    continue;
                }
                $user_data = [
                    'error'  => false,
                    'update' => 0,
                    'is_new' => true,
                    'value'  => [],
                ];
                if (count((array) $_data) != count($user_key)) {
                    $user_data['error'] = true;
                }
                if ($_data[0]) {
                    $user =& $user_h->get($_data[0]);
                    if ($user) {
                        for ($i=0; $i<count($user_key); $i++) {
                            $csv_value = $_data[$i];
                            $user_value = $user->get($user_key[$i]);
                            $update = $user_value != $csv_value;
                            switch ($user_key[$i]) {
                              case 'user_regdate':
                              case 'last_login':
                                $update = ($user_value || $csv_value) && 0 !== strcmp(formatTimestamp($user_value, 'Y/n/j H:i'), $csv_value);
                                 if ($update) {
                                 }
                                break;
                              case 'pass':
                                if (strlen($csv_value) < 32) {
                                    $csv_value = User_Utils::encryptPassword($csv_value);
                                    $update = $user_value !== $csv_value;
                                }
                              default:
                            }
                            $user_data['update'] = $user_data['update'] | $update;
                            $user_data['value'][] = [
                                'var'    => $csv_value,
                                'update' => $update,
                            ];
                        }
                        $user_data['is_new'] = false;
                    }
                }
                if (true == $user_data['is_new']) {
                    for ($i=0; $i<count($user_key); $i++) {
                        $var = isset($_data[$i]) && '' !== $_data[$i] ? $_data[$i] : $user_tmp->get($user_key[$i]);
                        switch ($user_key[$i]) {
                          case 'user_regdate':
                          case 'last_login':
                            $var = formatTimestamp($var, 'Y/n/j H:i');
                            break;
                        }
                        $user_data['value'][] = [
                            'var'    => $var,
                            'update' => 0
                        ];
                    }
                }
                $csv_data[] = $user_data;
            }
            if ($current_locale) {
                setlocale(LC_ALL, $current_locale);
            }
            fclose($handle);
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
    public function explodeCSV($csv, $delimiter = ',')
    {
        $space_list = '';
        foreach ([' ', "\t", "\r", "\n"] as $c) {
            if ($c != $delimiter) {
                $space_list .= $c;
            }
        }

        $line_end = '';
        if (preg_match("/([$space_list]+)\$/sS", $csv, $match)) {
            $line_end = $match[1];
        }
        $csv = substr($csv, 0, strlen($csv)-strlen($line_end));
        $csv .= ' ';

        $field = '';
        $retval = [];

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
            if ('"' == $csv[$index]) {
                // 2A. handle quote delimited field
                $index++;
                while ($index < $csv_len) {
                    if ('"' == $csv[$index]) {
                        // handle double quote
                        if ('"' == $csv[$index + 1]) {
                            $field .= $csv[$index];
                            $index += 2;
                        } else {
                            // must be end of string
                            while ($csv[$index] != $delimiter && $index < $csv_len) {
                                $index++;
                            }
                            if ($csv[$index] == $delimiter) {
                                $index++;
                            }
                            break;
                        }
                    } else {
                        // normal character
                        if (preg_match('/^([^"]*)/S', substr($csv, $index), $match)) {
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
                if (isset($csv[$index]) && $csv[$index] == $delimiter) {
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
