<?php
/**
 * A MySQL store (without PEAR).
 *
 * @version $Rev: 263 $
 * @link $URL: https://ajax-discuss.svn.sourceforge.net/svnroot/ajax-discuss/openid/trunk/openid/class/php5-openid/ExMySQLStore.php $
 */
if (!defined('XOOPS_ROOT_PATH')) {
    exit();
}
require_once XOOPS_ROOT_PATH . '/modules/openid/class/php5-openid/Auth/OpenID/MySQLStore.php';

/**
 * An SQL store that uses MySQL as its backend.
 */
class OpenID_ExMySQLStore extends Auth_OpenID_MySQLStore
{
    /**
     * Returns true if $value constitutes a database error; returns
     * false otherwise.
     */
    function isError($value)
    {
        return ($value === False);
    }
}
?>