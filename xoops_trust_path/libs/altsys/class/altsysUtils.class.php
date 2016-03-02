<?php
class altsysUtils
{
    public static function getDelegateCallbackClassNames($name, $doRegist = true)
    {
        $names = array();
        
        if (! class_exists('XCube_Delegate')) {
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
    
    public static function isInstalledXclHtmleditor()
    {
        if (defined('LEGACY_BASE_VERSION') && version_compare(LEGACY_BASE_VERSION, '2.2.0.0', '>=')) {
            $cNames = self::getDelegateCallbackClassNames('Site.TextareaEditor.HTML.Show');
            if ($cNames) {
                $last = array_pop($cNames);
                if ($last !== 'Legacy_TextareaEditor') {
                    return true;
                }
            }
        }
        return false;
    }
    
    public static function htmlspecialchars($str, $flags = ENT_COMPAT, $encoding = null, $double_encode = true)
    {
        static $php523 = null;
        if (is_null($php523)) {
            $php523 = version_compare(PHP_VERSION, '5.2.3', '>=');
        }
        if (is_null($encoding)) {
            $encoding = (defined('_CHARSET'))? _CHARSET : '';
        }
        if ($php523) {
            return htmlspecialchars($str, $flags, $encoding, $double_encode);
        } else {
            $ret = htmlspecialchars($str, $flags, $encoding);
            if (! $double_encode) {
                $ret = str_replace('&amp;amp;', '&amp;', $ret);
            }
            return $ret;
        }
    }
}
