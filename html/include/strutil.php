<?php
/**
 * String Utility
 */

class StrUtil 
{
    public static function toPascal($str = null, $delim = "_")
    {
        $str  = strtolower($str);
        $strs = explode($delim, $str);
        $strs = array_map("ucfirst", $strs);
        return implode("", $strs);
    }
    public static function toCamel($str = null, $delim = "_")
    {
        $str  = strtolower($str);
        $strs = explode($delim, $str);
        $strs = array_map("ucfirst", $strs);
        $strs[0] = strtolower($strs[0]);
        return implode("", $strs);
    }
    public static function toSnake($str = null, $delim = "_")
    {
        $str = preg_replace("/([A-Z])/", "_$1", $str);
        $str = strtolower($str);
        return ltrim($str, "_");
    }

    // Zend_Db_Adapter_Abstruct::_quote() の先頭と末尾の "'" を付けないバージョン
    public static function quote($value)
    {
        if (is_int($value)) { 
            return $value;
        } elseif (is_float($value)) {
            return sprintf('%F', $value);
        } 
        return addcslashes($value, "\000\n\r\\'\"\032");
    }

    // print_r の結果の文字列を配列変換する
    // -> 参照元：http://blog.asial.co.jp/407
    public static function unprint_r($str, $num=0)
    {   
        $data_list = array();
        $str_list = explode("\n", $str);
        $add_list = array();
        $indent = ' {' . ($num*8 + 4) . '}';
        $pattern = '/^' . $indent . '\[.+\] => .*$/';
        $flag = false;
        foreach ($str_list as $value) { 
            if (preg_match('/^' .' {' . ($num*8) . '}' . '(Array|\()$/', $value)) {
                continue;
            }   
            if (preg_match('/^' .' {' . ($num*8) . '}' . '\)$/', $value)) {
                break;
            }   
            if (preg_match($pattern, $value, $matches)) {
                $flag = true;
                if (count($add_list)) {
                    $data_list[] = join("\n", $add_list);
                }   
                $add_list = array();
                $add_list[] = $value;
            } else {
                if ($flag) {
                    $add_list[] = $value;
                }
            }
        }
        $data_list[] = join("\n", $add_list);
        
        $result_list = array();
        foreach ($data_list as $data) {
            $pattern = '/'. $indent . '\[(.+?)\] => (.*)/s';
            preg_match($pattern, $data, $matches);
            $key   = $matches[1];
            $value = $matches[2];
            if (strstr($value, 'Array')) {
              $result_list[$key] = unprint_r($data, $num+1);
            } else {
              $result_list[$key] = $value;
            }
        }
        return $result_list;
    }

    public static function toUtf8($val = array()){
        $count = count($val);
        for($i = 0; $i < $count; $i++ ){
            $val[$i] = mb_convert_encoding($val[$i], "UTF-8", 'sjis-win');
        }
        return $val;
    }

    public static function myDbQuote($str)
    {
        switch(XOOPS_DB_TYPE) {
        case "pdo_pgsql":
            $str = str_replace('`', '"', $str);
            break;
        case "mysql":
        default:
            break;
        }
        return $str;
    }
}
