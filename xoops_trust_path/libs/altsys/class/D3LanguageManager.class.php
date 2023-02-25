<?php
/**
 * Altsys library (UI-Components) for D3 modules
 * Class D3LanguageManager
 * @package    Altsys
 * @version    XCL 2.3.1
 * @author     Other authors Gigamaster, 2020 XCL PHP7
 * @author     Gijoe (Peak)
 * @copyright  (c) 2005-2023 Authors
 * @license    GPL v2.0
 */

class D3LanguageManager
{
    public $default_language = 'english';

    public $language = 'english';

    public $salt;

    public $cache_path;

    public $cache_prefix = 'lang';

    public $my_language = false;


    /**
     * D3LanguageManager constructor.
     */
    public function __construct()
    {
        $this->language = preg_replace('/[^0-9a-zA-Z_-]/', '', @$GLOBALS['xoopsConfig']['language']);

        $this->salt = mb_substr(md5(XOOPS_ROOT_PATH . XOOPS_DB_USER . XOOPS_DB_PREFIX), 0, 6);

        $this->cache_path = XOOPS_TRUST_PATH . '/cache';

        if (defined('ALTSYS_MYLANGUAGE_ROOT_PATH') && file_exists(ALTSYS_MYLANGUAGE_ROOT_PATH)) {
            $this->my_language = ALTSYS_MYLANGUAGE_ROOT_PATH;
        }
    }


    /**
     * @param null $conn
     * @return \D3LanguageManager
     */
    public static function getInstance($conn = null)
    {
        static $instance;

        if (!isset($instance)) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * @param      $resource
     * @param      $mydirname
     * @param null $mytrustdirname
     * @param bool $read_once
     */
    public function read($resource, $mydirname, $mytrustdirname = null, bool $read_once = true)
    {
        $d3file = XOOPS_ROOT_PATH . '/modules/' . $mydirname . '/mytrustdirname.php';

        if (empty($mytrustdirname) && is_file($d3file)) {
            require $d3file;
        }

        if (empty($this->language)) {
            $this->language = preg_replace('/[^0-9a-zA-Z_-]/', '', @$GLOBALS['xoopsConfig']['language']);
        }

        $cache_file = $this->getCacheFileName($resource, $mydirname);

        $root_file = XOOPS_ROOT_PATH . '/modules/' . $mydirname . '/language/' . $this->language . '/' . $resource;

        // language overriding by XOOPS_ROOT_PATH/my_language
        if ($this->my_language) {
            $mylang_file = $this->my_language . '/modules/' . $mydirname . '/' . $this->language . '/' . $resource;

            if (is_file($mylang_file)) {
                require_once $mylang_file;
            }

            $original_error_level = error_reporting();

            error_reporting($original_error_level & ~E_NOTICE);
        }

        if (empty($mytrustdirname)) {
            // conventional module
            $default_file = XOOPS_ROOT_PATH . '/modules/' . $mydirname . '/language/' . $this->default_language . '/' . $resource;

            if (is_file($cache_file)) {
                require_once $cache_file;
            } elseif (is_file($root_file)) {
                require_once $root_file;
            } elseif (is_file($default_file)) {
                // fall back english

                require_once $default_file;
            }
        } else {
            // D3 modules
            $trust_file = XOOPS_TRUST_PATH . '/modules/' . $mytrustdirname . '/language/' . $this->language . '/' . $resource;

            $default_file = XOOPS_TRUST_PATH . '/modules/' . $mytrustdirname . '/language/' . $this->default_language . '/' . $resource;

            if (is_file($cache_file)) {
                require_once $cache_file;
            } elseif (is_file($root_file)) {
                require_once $root_file;
            } elseif (is_file($trust_file)) {
                if ($read_once) {
                    require_once $trust_file;
                } else {
                    require $trust_file;
                }
            } elseif (is_file($default_file)) {
                // fall back to english
                if ($read_once) {
                    require_once $default_file;
                } else {
                    require $default_file;
                }
            }
        }

        if ($this->my_language) {
            error_reporting($original_error_level);
        }
    }

    /**
     * @param      $resource
     * @param      $mydirname
     * @param null $language
     * @return string
     */
    public function getCacheFileName($resource, $mydirname, $language = null)
    {
        if (empty($language)) {
            $language = $this->language;
        }

        return $this->cache_path . '/' . $this->cache_prefix . '_' . $this->salt . '_' . $mydirname . '_' . $language . '_' . $resource;
    }
}
