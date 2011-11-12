<?php
/**
 * Data container object
 * @version $Rev$ $Date$
 * @link $URL$
 */
class Openid_Context
{
    /**
     * @var Array
     */
    var $_value = array();

    /**
     * @param string $key
     * @return mixed
     */
    function get($key)
    {
        return isset($this->_value[$key]) ? $this->_value[$key] : '';
    }

    /**
     * @param string $key
     * @return mixed
     */
    function get4Sql($key)
    {
        $value = $this->get($key);
        if (!is_int($value)) {
            $value = mysql_real_escape_string($value);
        }
        return $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    function get4Show($key)
    {
        return htmlspecialchars($this->get($key), ENT_QUOTES);
    }

    /**
     * @param string $key
     * @param string $value
     */
    function set($key, $value)
    {
        $this->_value[$key] = $value;
    }

    /**
     * @param string $name
     * @param string $type
     * @param string $method
     * @param string $key
     * @return boolean
     */
    function accept($name, $type = 'string', $method = 'post', $key = null)
    {
        if ($method == 'post') {
            $querys = $_POST;
        } else if ($method == 'get') {
            $querys = $_GET;
        } else if ($method == 'cookie') {
            $querys = $_COOKIE;
        } else if ($method == 'request') {
            $querys = $_REQUEST;
        }
        $key = $key ? $key : $name;
        if (!isset($querys[$key])) {
            return false;
        }
        $value = $querys[$key];
        switch ($type) {
            case 'string':
                if (get_magic_quotes_gpc()) {
                    $value = stripslashes($value);
                }
                $this->_value[$name] = preg_replace('/[\x00-\x1f\x7f]/', '', $value);
                break;
            case 'int':
                $this->_value[$name] = intval($value);
                break;
            case 'array':
                $values = $delim = '';
                foreach ($value as $v) {
                    $v = intval($v);
                    if ($v) {
                        $values .= $delim . $v;
                        $delim = '|';
                    }
                }
                $this->_value[$name] = $values;
                break;
        }
        return true;
    }
}
?>