<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Class altsysUtils
 * @package    Altsys
 * @version    XCL 2.4.0
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2024 Authors
 * @license    GPL v2.0
 */

class altsysUtils
{
    /**
     * @param      $name
     * @param bool $doRegist
     * @return array
     */
    public static function getDelegateCallbackClassNames($name, bool $doRegist = true)
    {
        $names = [];

        if (!class_exists('XCube_Delegate')) {
            return $names;
        }

        if ($doRegist) {
            $delegate = new XCube_Delegate();
            $delegate->register($name);
        }
        $m = XCube_Root::getSingleton()->mDelegateManager;
        if ($m) {
            $delgates = $m->getDelegates();

            if (isset($delgates[$name])) {
                $d_target = $delgates[$name];

                $keys = array_keys($d_target);
                $callbacks = $d_target[$keys[0]]->_mCallbacks;
                foreach (array_keys($callbacks) as $priority) {
                    foreach (array_keys($callbacks[$priority]) as $idx) {
                        $callback = $callbacks[$priority][$idx][0];
                        $_name = (is_array($callback))? ((is_object($callback[0]))? get_class($callback[0]) : $callback[0]) : $callback;
                        $names[$priority] = $_name;
                    }
                }
                ksort($names, SORT_NUMERIC);
            }
        }
        return $names;
    }

    /**
     * @return bool
     */
    public static function isInstalledXclHtmleditor(): bool
    {
        if (defined('LEGACY_BASE_VERSION') && version_compare(LEGACY_BASE_VERSION, '2.2', '>=')) {
            $cNames = self::getDelegateCallbackClassNames('Site.TextareaEditor.HTML.Show');
            if ($cNames) {
                $last = array_pop($cNames);
                if ('Legacy_TextareaEditor' !== $last) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @param      $str
     * @param int $flags
     * @param null $encoding
     * @param bool $double_encode
     * @return array|string|string[]
     */
    public static function htmlSpecialChars($str, int $flags = ENT_COMPAT, $encoding = null, bool $double_encode = true)
    {

        $ver = (float)phpversion();

        if ($encoding === null) {
            $encoding = defined('_CHARSET') ? _CHARSET : '';
        }
        if ($ver > 7.0) {
            // PHP_VERSION_ID >= 70000;
            // do something for php7.1 and above.
            return htmlspecialchars($str, $flags, $encoding, $double_encode);
        }

        $ret = htmlspecialchars($str, $flags, $encoding);

        if (!$double_encode) {
            $ret = str_replace('&amp;amp;', '&amp;', $ret);
        }

        return $ret;
    }
}
